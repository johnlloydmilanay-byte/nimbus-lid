<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Registrar\Otr;
use App\Models\Registrar\OtrGrade;
use App\Models\System\SrmProgram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

// Add PhpSpreadsheet classes for Excel export
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class OtrController extends Controller
{
    // Display OTR dashboard
    public function dashboard()
    {
        try {
            $totalOtrs = Otr::count();
            $totalGraduates = Otr::whereNotNull('Date_of_Graduation')->count();
            $totalCourses = Otr::distinct('Degree_Course')->count('Degree_Course');
            $newOtrs = Otr::where('created_at', '>=', now()->subDays(30))->count();
            
            $topCourses = Otr::select('Degree_Course')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('Degree_Course')
                ->orderByDesc('count')
                ->limit(5)
                ->get();
                
            $recentGraduates = Otr::whereNotNull('Date_of_Graduation')
                ->orderByDesc('Date_of_Graduation')
                ->limit(5)
                ->get();
                
            $recentAdditions = Otr::latest()
                ->limit(5)
                ->get();

            return view('registrar.dashboard', compact(
                'totalOtrs',
                'totalGraduates',
                'totalCourses',
                'newOtrs',
                'topCourses',
                'recentGraduates',
                'recentAdditions'
            ));
            
        } catch (\Exception $e) {
            return view('registrar.dashboard')->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    // List all OTRs
    public function index(Request $request)
    {
        $query = Otr::query();
        
        if ($request->has('graduation_year')) {
            $query->whereYear('Date_of_Graduation', $request->graduation_year);
        }
        
        if ($request->has('course')) {
            $query->where('Degree_Course', $request->course);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('First_Name', 'like', "%{$search}%")
                  ->orWhere('Last_Name', 'like', "%{$search}%")
                  ->orWhere('Student_ID', 'like', "%{$search}%")
                  ->orWhereHas('program', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }
        
        $otrs = $query->latest()->paginate(10);
        
        // Fetch programs for the sidebar/filter dropdown
        $courses = SrmProgram::orderBy('code')->pluck('name', 'id');
        
        return view('registrar.otr.index', compact('otrs', 'courses'));
    }

    // Show create form
    public function create()
    {
        // Fetch all programs
        $programs = SrmProgram::select('id', 'code', 'name')->orderBy('code')->get();
        
        return view('registrar.otr.create', compact('programs'));
    }

    // Store new OTR record
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Last_Name' => 'required|string|max:255',
            'First_Name' => 'required|string|max:255',
            'Student_ID' => 'required|string|unique:otrs,Student_ID', 
            
            // Validate that Degree_Course ID exists in srm_programs table
            'Degree_Course' => 'nullable|exists:srm_programs,id', 
            
            'student_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please fix the following errors:',
                    'errors' => $validator->errors()->all()
                ]);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $otrData = $request->except('student_photo', '_token');
            
            // Handle photo upload
            $photoPath = 'assets/photos/default.jpg';
            if ($request->hasFile('student_photo') && $request->file('student_photo')->isValid()) {
                $photo = $request->file('student_photo');
                $extension = $photo->getClientOriginalExtension();
                $photoName = str_replace([' ', '/', '\\'], '_', $request->Student_ID) . '_' . time() . '.' . $extension;
                
                $directory = 'public/assets/photos';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory, 0755, true);
                }
                
                $photoPath = 'assets/photos/' . $photoName;
                $photo->storeAs('public/assets/photos', $photoName);
            }
            
            $otrData['Photo_Path'] = $photoPath;
            
            if (empty($otrData['Exemption_Note'])) {
                $otrData['Exemption_Note'] = 'Exempted from the Issuance of Special Order (S.O.)';
            }
            if (empty($otrData['Accreditation_Level'])) {
                $otrData['Accreditation_Level'] = 'PACUCOA Re-Accredited Level II';
            }
            if (empty($otrData['CHED_Memo_Order'])) {
                $otrData['CHED_Memo_Order'] = 'CHED Memo Order No. 01, s. 2005';
            }
            if (empty($otrData['Registrar_Name'])) {
                $otrData['Registrar_Name'] = 'MICHELLE J. BARBACENA-LLANTO, LPT';
            }
            
            $otr = Otr::create($otrData);
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTR record added successfully!',
                    'otr_id' => $otr->id
                ]);
            }
            
            return redirect()->route('registrar.otr.show', $otr->id)
                ->with('success', 'OTR record added successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error creating OTR: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    // Show single OTR
    public function show($id)
    {
        $otr = Otr::findOrFail($id);
        return view('registrar.otr.show', compact('otr'));
    }

    // Show edit form
    public function edit($id)
    {
        $otr = Otr::findOrFail($id);
        $programs = SrmProgram::select('id', 'code', 'name')->orderBy('code')->get();
        return view('registrar.otr.edit', compact('otr', 'programs'));
    }

    // Update OTR
    public function update(Request $request, $id)
    {
        $otr = Otr::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'Last_Name' => 'required|string|max:255',
            'First_Name' => 'required|string|max:255',
            'Student_ID' => 'required|string|unique:otrs,Student_ID,' . $id,
            
            // Validate that Degree_Course ID exists
            'Degree_Course' => 'nullable|exists:srm_programs,id',
            
            'student_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $otrData = $request->except('student_photo', '_token', '_method');
            
            if ($request->hasFile('student_photo')) {
                $photo = $request->file('student_photo');
                $extension = $photo->getClientOriginalExtension();
                $photoName = $request->Student_ID . '_' . time() . '.' . $extension;
                $photoPath = 'assets/photos/' . $photoName;
                
                if ($otr->Photo_Path !== 'assets/photos/default.jpg') {
                    Storage::delete('public/' . $otr->Photo_Path);
                }
                
                $photo->storeAs('public/assets/photos', $photoName);
                $otrData['Photo_Path'] = $photoPath;
            }
            
            $otr->update($otrData);
            
            return redirect()->route('registrar.otr.show', $otr->id)
                ->with('success', 'OTR record updated successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    // Delete OTR
    public function destroy($id)
    {
        try {
            $otr = Otr::findOrFail($id);
            
            if ($otr->Photo_Path !== 'assets/photos/default.jpg') {
                Storage::delete('public/' . $otr->Photo_Path);
            }
            
            $otrName = $otr->First_Name . ' ' . $otr->Last_Name;
            $otr->delete();
            
            return redirect()->route('registrar.otr.index')
                ->with('success', 'OTR deleted successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

// Generate Excel with exact PDF layout (keeping same function name for testing)
public function generatePDF($id)
{
    $otr = Otr::findOrFail($id);
    $otr->load('program');
    
    // Create new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // ========== PAGE SETUP ==========
    // Set to LETTER size (8.5 x 11 inches)
    $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER)
        ->setFitToWidth(1)
        ->setFitToHeight(0)
        ->setHorizontalCentered(true);
    
    // Set margins
    $sheet->getPageMargins()
        ->setTop(0.4)
        ->setRight(0.6)
        ->setLeft(0.6)
        ->setBottom(0.6);
    
    // Set column widths - Keep original widths for most content
    // We'll use merged cells to simulate different widths for specific sections
    $sheet->getColumnDimension('A')->setWidth(40); // Keep original
    $sheet->getColumnDimension('B')->setWidth(30); // Keep original  
    $sheet->getColumnDimension('C')->setWidth(40); // Keep original
    
    // Set default font
    $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
    
    $currentRow = 1;
    
    // ========== TITLE ==========
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Official Transcript of Records");
    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A' . $currentRow)->getFont()->getColor()->setARGB('FF1E50B4'); // Blue color
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(20);
    $currentRow += 1;
    
    // ========== NAME BOXES ==========
    // Create name boxes with borders
    $sheet->setCellValue('A' . $currentRow, strtoupper($otr->Last_Name ?? ''));
    $sheet->setCellValue('B' . $currentRow, strtoupper($otr->First_Name ?? ''));
    $sheet->setCellValue('C' . $currentRow, strtoupper($otr->Middle_Name ?? ''));
    
    $nameBoxStyle = [
        'font' => ['bold' => true, 'size' => 12],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ];
    
    $sheet->getStyle('A' . $currentRow)->applyFromArray($nameBoxStyle);
    $sheet->getStyle('B' . $currentRow)->applyFromArray($nameBoxStyle);
    $sheet->getStyle('C' . $currentRow)->applyFromArray($nameBoxStyle);
    $sheet->getRowDimension($currentRow)->setRowHeight(20);
    $currentRow++;
    
    // Name labels
    $sheet->setCellValue('A' . $currentRow, "Last Name");
    $sheet->setCellValue('B' . $currentRow, "First Name");
    $sheet->setCellValue('C' . $currentRow, "Middle Name");
    
    $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setSize(10);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow += 2;
    
    // ========== PHOTO AREA ==========
    // Try to fetch and insert actual photo
    $photoPath = $otr->Photo_Path;
    $hasPhoto = false;
    
    if ($photoPath && $photoPath != 'assets/photos/default.jpg') {
        if (Storage::disk('public')->exists($photoPath)) {
            $fullPhotoPath = Storage::disk('public')->path($photoPath);
            if (file_exists($fullPhotoPath)) {
                // Set the row height to 100 points (approximately 135px)
                $sheet->getRowDimension($currentRow)->setRowHeight(100);
                
                // 2x2 photo - adjust height to fit within the row
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Student Photo');
                $drawing->setDescription('Student Photo');
                $drawing->setPath($fullPhotoPath);
                $drawing->setHeight(160); // Slightly less than row height to allow for padding
                $drawing->setWidth(160);  // Square photo (2x2 inches approx)
                $drawing->setCoordinates('B' . $currentRow);
                $drawing->setOffsetX(35); // Center horizontally
                $drawing->setOffsetY(2.5); // Small offset from top
                $drawing->setWorksheet($sheet);
                $hasPhoto = true;
            }
        }
    }
    
    if (!$hasPhoto) {
        // Set the row height to 100 points (approximately 135px)
        $sheet->getRowDimension($currentRow)->setRowHeight(100);
        
        // Show placeholder box for photo
        $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, "[Photo Area - 2x2 Photo Here]");
        $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true);
        $sheet->getStyle('A' . $currentRow)->getFont()->getColor()->setARGB('FF969696');
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Create a border for the photo area
        $photoBoxStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->applyFromArray($photoBoxStyle);
    }
    
    $currentRow += 2;
    
    // ========== STUDENT ID ==========
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, $otr->Student_ID ?? '');
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(12);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Student ID No.");
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(10);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow += 2;
    
    // ========== DEGREE / COURSE ==========
    $courseDisplay = 'Not Specified';
    if ($otr->program) {
        $courseDisplay = $otr->program->code . ' - ' . $otr->program->name;
    }
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, $courseDisplay);
    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Degree/Course");
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow += 1;
    
    // ========== EXEMPTION NOTES ==========
    $exemptionNote = $otr->Exemption_Note ?? 'Exempted from the Issuance of Special Order (S.O.)';
    $accreditationLevel = $otr->Accreditation_Level ?? 'PACUCOA Re-Accredited Level II';
    $chedMemo = $otr->CHED_Memo_Order ?? 'CHED Memo Order No. 01, s. 2005';
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, $exemptionNote);
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setWrapText(true);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, $accreditationLevel);
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, $chedMemo);
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow += 2;
    
    // ========== GRADUATION INFO ==========
    $graduationDate = $otr->Date_of_Graduation ? 
        $otr->Date_of_Graduation->format('F j, Y') : 'Not Specified';
    $nstpNumber = $otr->NSTP_Serial_Number ?? 'Not Specified';
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Date of Graduation: " . $graduationDate);
    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "NSTP Serial No.: " . $nstpNumber);
    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow += 2;
    
    // ========== ENTRANCE DATA TITLE ==========
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Entrance Data");
    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A' . $currentRow)->getFont()->getColor()->setARGB('FF1E50B4'); // Blue color
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(18);
    $currentRow += 1;
    
    // ========== ENTRANCE DATA TABLE ==========
    // Get data
    $admissionCredentials = $otr->Admission_Credentials ?? 'Not Specified';
    $address = $otr->Address ?? 'Not Specified';
    $category = $otr->Category ?? 'Not Specified';
    $birthDate = $otr->Birth_Date ? 
        $otr->Birth_Date->format('F j, Y') : 'Not Specified';
    $schoolLastAttended = $otr->School_Last_Attended ?? 'Not Specified';
    $schoolYearLastAttended = $otr->School_Year_Last_Attended ?? 'Not Specified';
    $birthPlace = $otr->Birth_Place ?? 'Not Specified';
    $schoolAddress = $otr->School_Address ?? 'Not Specified';
    $citizenship = $otr->Citizenship ?? 'Not Specified';
    $semesterYearAdmitted = $otr->Semester_Year_Admitted ?? 'Not Specified';
    $religion = $otr->Religion ?? 'Not Specified';
    $college = $otr->College ?? 'Not Specified';
    $gender = $otr->Gender ?? 'Not Specified';
    
    $tableStartRow = $currentRow;
    
    // Row 1: Admission Credentials & Address
    // For Entrance Data table, we need wider column A and narrower column B
    // We'll use merged cells to simulate this
    $sheet->mergeCells('A' . $currentRow . ':B' . $currentRow); // Merge A and B for wider left column
    $sheet->setCellValue('A' . $currentRow, 'Admission Credentials: ' . $admissionCredentials);
    
    // Use only column C for the right side (address)
    $sheet->setCellValue('C' . $currentRow, 'Address: ' . $address);
    $sheet->getRowDimension($currentRow)->setRowHeight(16);
    $currentRow++;
    
    // Row 2: Category & Birth Date
    $sheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, 'Category: ' . $category);
    $sheet->setCellValue('C' . $currentRow, 'Birth Date: ' . $birthDate);
    $currentRow++;
    
    // Row 3: School/Year & Birth Place
    $sheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, 'School/Year Last Attended: ' . $schoolLastAttended . ' – ' . $schoolYearLastAttended);
    $sheet->setCellValue('C' . $currentRow, 'Birth Place: ' . $birthPlace);
    $currentRow++;
    
    // Row 4: School Address & Citizenship
    $sheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, 'School Address: ' . $schoolAddress);
    $sheet->setCellValue('C' . $currentRow, 'Citizenship: ' . $citizenship);
    $currentRow++;
    
    // Row 5: Semester/Year & Religion
    $sheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, 'Semester/Year Admitted: ' . $semesterYearAdmitted);
    $sheet->setCellValue('C' . $currentRow, 'Religion: ' . $religion);
    $currentRow++;
    
    // Row 6: College & Gender
    $sheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, 'College: ' . $college);
    $sheet->setCellValue('C' . $currentRow, 'Gender: ' . $gender);
    $tableEndRow = $currentRow;
    $currentRow += 1;
    
    // Apply borders ONLY to the entrance data table
    // Outer border around entire table
    $tableRange = 'A' . $tableStartRow . ':C' . $tableEndRow;
    $tableBorderStyle = [
        'borders' => [
            'outline' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ];
    $sheet->getStyle($tableRange)->applyFromArray($tableBorderStyle);
    
    // Add vertical divider between the merged A:B column and C column
    for ($row = $tableStartRow; $row <= $tableEndRow; $row++) {
        $sheet->getStyle('C' . $row)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
    }
    
    // ========== FOOT NOTES ==========
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "N.B.: Any erasure or alteration renders the whole transcript invalid.");
    $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true)->setSize(9);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Remarks: For Official Purposes Only");
    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(16);
    $currentRow += 1;
    
    // ========== ADD ROW BETWEEN REMARKS AND PREPARED BY ==========
    $sheet->getRowDimension($currentRow)->setRowHeight(12);
    $currentRow++;
    
    // ========== SIGNATORIES ==========
    // Get signatory names - IMPORTANT: Using exact same logic as original PDF
    $preparedBy = $otr->Prepared_By ?? 'Not Specified';
    $checkedBy = $otr->Checked_By ?? 'Not Specified';
    $deanName = $otr->Dean_Name ?? 'Not Specified';
    $registrarName = $otr->Registrar_Name ?? 'MICHELLE J. BARBACENA-LLANTO, LPT';
    
    // Prepared by and Checked by - B31: Merge and Center with C31
    $sheet->setCellValue('A' . $currentRow, "Prepared by: " . $preparedBy);
    $sheet->setCellValue('B' . $currentRow, "Checked by: " . $checkedBy);
    $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
    $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setSize(11);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow += 2;
    
    // Empty space - B32: Merge and Center to C32
    $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
    $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getRowDimension($currentRow)->setRowHeight(12);
    $currentRow++;
    
    // Dean and Registrar names - Dean name left aligned
    $sheet->setCellValue('A' . $currentRow, $deanName);
    $sheet->setCellValue('B' . $currentRow, $registrarName);
    $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
    
    // Left align Dean name
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    // Center Registrar name
    $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true)->setSize(11);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    // Titles
    $sheet->setCellValue('A' . $currentRow, 'Dean, ' . $college);
    $sheet->setCellValue('B' . $currentRow, "University Registrar");
    $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
    $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setSize(10);
    
    // Left align Dean title, center Registrar title
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow += 1;
    
    // Add row before date prepared
    $sheet->getRowDimension($currentRow)->setRowHeight(5);
    $currentRow++;
    
    // Date prepared
    $datePrepared = $otr->Date_Prepared ? 
        $otr->Date_Prepared->format('F j, Y') : date('F j, Y');
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Date prepared: " . $datePrepared);
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(10);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    $currentRow++;
    
    // ========== PAGE NUMBER ==========
    $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
    $sheet->setCellValue('A' . $currentRow, "Page 1");
    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(9)->setItalic(true);
    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getRowDimension($currentRow)->setRowHeight(15);
    
    // ========== FINAL FORMATTING ==========
    // Set print area
    $sheet->getPageSetup()->setPrintArea('A1:C' . $currentRow);
    
    // ========== SAVE AND DOWNLOAD ==========
    $filename = 'OTR_Transcript_' . $otr->Student_ID . '.xlsx';
    
    // Create writer and output
    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

// Export grades to Excel with CHED transcript layout
public function exportGradesExcel($id, $startingPageNumber = 2)
{
    $otr = Otr::findOrFail($id);
    $otr->load(['program', 'grades' => function($query) {
        $query->orderBy('school_year')->orderBy('semester')->orderBy('subject_code');
    }]);
    
    // Create new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // ========== PAGE SETUP ==========
    $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
        ->setFitToWidth(1)
        ->setFitToHeight(0)
        ->setHorizontalCentered(true);
    
    // Set very small margins to maximize space usage
    $sheet->getPageMargins()
        ->setTop(1)
        ->setRight(0.5)
        ->setLeft(0.5)
        ->setBottom(1);
    
    // Define columns with adjusted widths for portrait orientation
    $columns = [
        'A' => ['width' => 25, 'title' => 'Subject Code'],
        'B' => ['width' => 50, 'title' => 'Subject Title'],
        'C' => ['width' => 20, 'title' => 'Final Rating'],
        'D' => ['width' => 25, 'title' => 'Units Earned'],
    ];
    
    // Set column widths
    foreach ($columns as $col => $config) {
        $sheet->getColumnDimension($col)->setWidth($config['width']);
    }
    
    // Hide columns E, F, G if they exist
    for ($col = 'E'; $col <= 'G'; $col++) {
        if ($sheet->getColumnDimension($col)) {
            $sheet->getColumnDimension($col)->setVisible(false);
        }
    }
    
    // Set default font
    $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8.5);
    
    // ========== HELPER FUNCTIONS ==========
    $addHeaderSection = function($worksheet, $startRow, $otr, $isContinuation = false, $pageNumber = null) {
        $row = $startRow;
        
        // Title: COLLEGIATE RECORD (only on first page)
        if (!$isContinuation) {
            $worksheet->mergeCells('A' . $row . ':D' . $row);
            $worksheet->setCellValue('A' . $row, 'C o l l e g i a t e   R e c o r d');
            $worksheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $worksheet->getRowDimension($row)->setRowHeight(20);
            
            $row += 2;
            
            // Name boxes
            $firstName = strtoupper($otr->First_Name ?? '');
            $middleName = strtoupper($otr->Middle_Name ?? '');
            $lastName = strtoupper($otr->Last_Name ?? '');
            
            // Last Name box
            $worksheet->mergeCells('A' . $row . ':A' . $row);
            $worksheet->setCellValue('A' . $row, $lastName);
            $worksheet->getStyle('A' . $row . ':A' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $worksheet->getStyle('A' . $row . ':A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $worksheet->getStyle('A' . $row . ':A' . $row)->getFont()->setBold(true)->setSize(9);
            $worksheet->getRowDimension($row)->setRowHeight(16);
            
            // First Name box
            $worksheet->mergeCells('B' . $row . ':B' . $row);
            $worksheet->setCellValue('B' . $row, $firstName);
            $worksheet->getStyle('B' . $row . ':B' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $worksheet->getStyle('B' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $worksheet->getStyle('B' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(9);
            
            // Middle Name box
            $worksheet->mergeCells('C' . $row . ':D' . $row);
            $worksheet->setCellValue('C' . $row, $middleName);
            $worksheet->getStyle('C' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $worksheet->getStyle('C' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $worksheet->getStyle('C' . $row . ':D' . $row)->getFont()->setBold(true)->setSize(9);
            
            $row++;
            
            // Name labels
            $worksheet->setCellValue('A' . $row, 'Last Name');
            $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $worksheet->getStyle('A' . $row)->getFont()->setSize(7.5);
            
            $worksheet->setCellValue('B' . $row, 'First Name');
            $worksheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $worksheet->getStyle('B' . $row)->getFont()->setSize(7.5);
            
            $worksheet->mergeCells('C' . $row . ':D' . $row);
            $worksheet->setCellValue('C' . $row, 'Middle Name');
            $worksheet->getStyle('C' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $worksheet->getStyle('C' . $row . ':D' . $row)->getFont()->setSize(7.5);
            
            $row += 2;
        } else {
            // For continuation pages, start directly at table headers
            $row = $startRow;
        }
        
        // Table headers (on every page)
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 9.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        
        $worksheet->setCellValue('A' . $row, 'Subject Code');
        $worksheet->setCellValue('B' . $row, 'Subject Title');
        $worksheet->setCellValue('C' . $row, 'Final Rating');
        $worksheet->setCellValue('D' . $row, 'Units Earned');
        
        $worksheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($headerStyle);
        $worksheet->getRowDimension($row)->setRowHeight(20);
        
        return $row + 1;
    };
    
        $addFooterSection = function($worksheet, $startRow, $otr, $pageNumber = null, $isLastPage = true, $hasContinuation = false) {
    $row = $startRow;
    
    // Get ALL dynamic values from OTR database record - EXACTLY like generatePDF
    $college = $otr->College ?? 'Not Specified';
    
    // FIX: Use EXACT SAME logic as generatePDF function
    // Get signatory names EXACTLY like the PDF does
    $preparedBy = $otr->Prepared_By ?? 'Not Specified';
    $checkedBy = $otr->Checked_By ?? 'Not Specified';
    $deanName = $otr->Dean_Name ?? 'Not Specified';
    $registrarName = $otr->Registrar_Name ?? 'MICHELLE J. BARBACENA-LLANTO, LPT'; // EXACT default like PDF
    
    // REMOVED the "1" check - just use the direct field access like PDF does
    // The PDF function doesn't check for "1", it just uses null coalescing
    
    // Graduation section only on last page
    if ($isLastPage && $otr->Date_of_Graduation) {
        $worksheet->getRowDimension($row)->setRowHeight(6);
        $row++;
        
        $graduationDate = $otr->Date_of_Graduation->format('F j, Y');
        // FIX: Use same logic as PDF for exemption notes
        $exemptionNote = $otr->Exemption_Note ?? 'Exempted from the issuance of Special Order (S.O.)';
        $accreditationLevel = $otr->Accreditation_Level ?? 'PACUCOA Re-Accredited Level II';
        $chedMemo = $otr->CHED_Memo_Order ?? 'CHED Memo Order No. 01 s. 2005';
        
        if ($otr->program) {
            $courseName = strtoupper($otr->program->name);
            $degreeAbbr = $this->getDegreeAbbreviation($otr->program->name);
        } else {
            $courseName = 'BACHELOR OF SCIENCE IN NURSING';
            $degreeAbbr = 'B.S.N.';
        }
        
        // Graduation header
        $worksheet->mergeCells('A' . $row . ':D' . $row);
        $worksheet->setCellValue('A' . $row, 'GRADUATE with the degree of ' . $courseName . ' (' . $degreeAbbr . ') on');
        $worksheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(9);
        $worksheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $worksheet->getRowDimension($row)->setRowHeight(14);
        
        $row++;
        
        // Graduation date and exemption
        $worksheet->mergeCells('A' . $row . ':D' . $row);
        $worksheet->setCellValue('A' . $row, $graduationDate . ' ' . $exemptionNote . ' by virtue of');
        $worksheet->getStyle('A' . $row)->getFont()->setSize(8.5);
        $worksheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $worksheet->getRowDimension($row)->setRowHeight(14);
        
        $row++;
        
        // CHED memo and accreditation
        $worksheet->mergeCells('A' . $row . ':D' . $row);
        $worksheet->setCellValue('A' . $row, $chedMemo . '. ' . $accreditationLevel . '.');
        $worksheet->getStyle('A' . $row)->getFont()->setSize(8.5);
        $worksheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $worksheet->getRowDimension($row)->setRowHeight(14);
        
        $row += 2;
    }
    
    // REMARKS (only on last page)
    if ($isLastPage) {
        $worksheet->mergeCells('A' . $row . ':D' . $row);
        $worksheet->setCellValue('A' . $row, 'Remarks: For Official Purposes Only');
        $worksheet->getStyle('A' . $row)->getFont()->setBold(true)->setItalic(true)->setSize(8.5);
        $worksheet->getRowDimension($row)->setRowHeight(14);
        $row += 1;
    }
    
    // ========== SIGNATORIES - EXACTLY LIKE PDF ==========
    // Prepared by - in column A only - LEFT ALIGNED
    $worksheet->setCellValue('A' . $row, 'Prepared by:  ' . $preparedBy);
    $worksheet->getStyle('A' . $row)->getFont()->setSize(8.5);
    $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    // Checked by - in column C only - LEFT ALIGNED
    $worksheet->setCellValue('C' . $row, 'Checked by:  ' . $checkedBy);
    $worksheet->getStyle('C' . $row)->getFont()->setSize(8.5);
    $worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    // Clear columns B and D for this row
    $worksheet->setCellValue('B' . $row, '');
    $worksheet->setCellValue('D' . $row, '');
    
    $worksheet->getRowDimension($row)->setRowHeight(14);
    
    $row += 2;
    
    // Dean and Registrar names - BOTH LEFT ALIGNED
    $worksheet->setCellValue('A' . $row, $deanName);
    $worksheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(8.5);
    $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    $worksheet->setCellValue('C' . $row, $registrarName);
    $worksheet->getStyle('C' . $row)->getFont()->setBold(true)->setSize(8.5);
    $worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    $worksheet->setCellValue('B' . $row, '');
    $worksheet->setCellValue('D' . $row, '');
    
    $worksheet->getRowDimension($row)->setRowHeight(14);
    
    $row++;
    
    // Titles - BOTH LEFT ALIGNED
    $worksheet->setCellValue('A' . $row, 'Dean, ' . $college);
    $worksheet->getStyle('A' . $row)->getFont()->setSize(8.5);
    $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    $worksheet->setCellValue('C' . $row, 'University Registrar');
    $worksheet->getStyle('C' . $row)->getFont()->setSize(8.5);
    $worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    $worksheet->setCellValue('B' . $row, '');
    $worksheet->setCellValue('D' . $row, '');
    
    $worksheet->getRowDimension($row)->setRowHeight(14);
    
    $row += 1;
    
    // Date prepared (only on last page) - LEFT ALIGNED
    if ($isLastPage) {
        $datePrepared = $otr->Date_Prepared ? 
            $otr->Date_Prepared->format('F j, Y') : date('F j, Y');
        $worksheet->mergeCells('A' . $row . ':D' . $row);
        $worksheet->setCellValue('A' . $row, 'Date prepared: ' . $datePrepared);
        $worksheet->getStyle('A' . $row)->getFont()->setSize(8.5);
        $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $worksheet->getRowDimension($row)->setRowHeight(14);
        $row++;
    }
    
    // Page number at the very bottom (on every page) - RIGHT ALIGNED
    if ($pageNumber) {
        $worksheet->mergeCells('A' . $row . ':D' . $row);
        $worksheet->setCellValue('A' . $row, 'Page ' . $pageNumber);
        $worksheet->getStyle('A' . $row)->getFont()->setSize(7.5)->setItalic(true);
        $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $worksheet->getRowDimension($row)->setRowHeight(12);
        
        // Add bottom border to page number row
        $worksheet->getStyle('A' . $row . ':D' . $row)
            ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
    }
    
    return $row;
};
    
    // ========== PROCESS GRADES WITH PAGINATION ==========
    $grades = $otr->grades;
    
    // Page break manager
    $pageBreaks = [];
    $currentPageStartRow = 1;
    
    // Start from the specified page number (default is 2)
    $currentPageNumber = $startingPageNumber;
    
    $isContinuationPage = false;
    
    // Track table boundaries for each page
    $pageTableBoundaries = [];
    
    if ($grades->isEmpty()) {
        $currentRow = $addHeaderSection($sheet, 1, $otr, false, $currentPageNumber);
        
        // Add University name
        $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'UNIVERSITY OF SANTO TOMAS-LEGAZPI, Legazpi City');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(9.5);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($currentRow)->setRowHeight(16);
        
        $currentRow++;
        
        $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'No grade records found.');
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($currentRow)->setRowHeight(16);
        
        // Store table boundary for this page
        $pageTableBoundaries[$currentPageNumber] = [
            'startRow' => $currentPageStartRow + 1, // Row after table headers
            'endRow' => $currentRow - 1 // Row before footer starts
        ];
        
        $currentRow = $addFooterSection($sheet, $currentRow + 1, $otr, $currentPageNumber, true, false);
    } else {
        // Group by semester and school year
        $groupedGrades = [];
        foreach ($grades as $grade) {
            $key = $grade->semester . '|' . $grade->school_year;
            if (!isset($groupedGrades[$key])) {
                $groupedGrades[$key] = [
                    'semester' => $grade->semester,
                    'school_year' => $grade->school_year,
                    'grades' => []
                ];
            }
            $groupedGrades[$key]['grades'][] = $grade;
        }
        
        // Sort by school year and semester
        uasort($groupedGrades, function($a, $b) {
            if ($a['school_year'] == $b['school_year']) {
                $order = ['1st' => 1, '2nd' => 2, 'Summer' => 3];
                return ($order[$a['semester']] ?? 4) <=> ($order[$b['semester']] ?? 4);
            }
            return $a['school_year'] <=> $b['school_year'];
        });
        
        // Constants for pagination
        $MAX_ROWS_PER_PAGE = 65;
        $headerRows = 7;
        $footerRows = 11; // REDUCED: Was 12, now 11 (reduced spacing before page number)
        $availableRowsForGrades = $MAX_ROWS_PER_PAGE - $headerRows - $footerRows;
        
        $currentRow = $addHeaderSection($sheet, 1, $otr, false, $currentPageNumber);
        $currentPageGradeCount = 0;
        $isFirstPage = true;
        $currentSemesterContinued = false;
        
        // Track where the table content starts for the current page
        $currentPageTableStartRow = $currentRow + 1; // Row after University name
        $currentPageHasContent = false;
        
        foreach ($groupedGrades as $group) {
            // Format semester text
            $semester = $group['semester'];
            if (stripos($semester, 'first') !== false || $semester == '1st') {
                $semester = '1st Semester';
            } elseif (stripos($semester, 'second') !== false || $semester == '2nd') {
                $semester = '2nd Semester';
            } elseif (stripos($semester, 'summer') !== false) {
                $semester = 'Summer';
            }
            
            // Add "cont'n" if this semester was split across pages
            $semesterText = $semester . ' ' . $group['school_year'];
            if ($currentSemesterContinued) {
                $semesterText .= ', cont\'n';
                $currentSemesterContinued = false;
            }
            
            if ($otr->program) {
                $semesterText .= ' - ' . strtoupper($otr->program->code);
            }
            
            // Calculate space needed for this semester
            $semesterRows = 1 + count($group['grades']) + 1;
            
            // Check if we need a new page before adding this semester
            if (!$isFirstPage && ($currentPageGradeCount + $semesterRows) > $availableRowsForGrades) {
                // Add bottom border to the last row of the current page's table
                if ($currentPageHasContent) {
                    $lastTableRow = $currentRow - 1;
                    // Ensure proper bottom border
                    $sheet->getStyle('A' . $lastTableRow . ':D' . $lastTableRow)
                        ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                }
                
                // Store table boundary for this page before footer
                $pageTableBoundaries[$currentPageNumber] = [
                    'startRow' => $currentPageTableStartRow,
                    'endRow' => $currentRow - 1
                ];
                
                // Add footer to current page with page number
                $currentRow = $addFooterSection($sheet, $currentRow, $otr, $currentPageNumber, false, false);
                
                // Record page break position
                $pageBreaks[] = $currentRow;
                
                // Start new page
                $currentPageNumber++;
                $currentPageStartRow = $currentRow + 1;
                $currentPageGradeCount = 0;
                $isContinuationPage = true;
                
                // Mark that the next semester should have "cont'n"
                $currentSemesterContinued = true;
                
                // Add header for new page
                $currentRow = $addHeaderSection($sheet, $currentPageStartRow, $otr, true, $currentPageNumber);
                
                // On continuation pages, add University name right after table headers
                $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'UNIVERSITY OF SANTO TOMAS-LEGAZPI, Legazpi City');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getRowDimension($currentRow)->setRowHeight(16);
                $currentRow++;
                $currentPageGradeCount++;
                
                // Reset table start row for new page
                $currentPageTableStartRow = $currentRow;
                $currentPageHasContent = false;
            }
            
            // Add University name on first page only
            if ($isFirstPage && !$isContinuationPage) {
                $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'UNIVERSITY OF SANTO TOMAS-LEGAZPI, Legazpi City');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getRowDimension($currentRow)->setRowHeight(16);
                $currentRow++;
                $currentPageGradeCount++;
            }
            
            // Add semester header
            $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, $semesterText);
            
            $semesterStyle = [
                'font' => ['bold' => true, 'size' => 9.5],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    'left' => ['borderStyle' => Border::BORDER_THIN],
                    'right' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ];
            $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->applyFromArray($semesterStyle);
            $sheet->getRowDimension($currentRow)->setRowHeight(16);
            
            $currentRow++;
            $currentPageGradeCount++;
            $currentPageHasContent = true;
            
            // Add grades for this semester
            $gradesInThisSemester = count($group['grades']);
            $gradesAdded = 0;
            
            foreach ($group['grades'] as $grade) {
                // Check if we need a page break in the middle of this semester
                if (!$isFirstPage && $currentPageGradeCount >= $availableRowsForGrades && $gradesAdded < $gradesInThisSemester) {
                    // Add bottom border to the last row before page break
                    $lastTableRow = $currentRow - 1;
                    $sheet->getStyle('A' . $lastTableRow . ':D' . $lastTableRow)
                        ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    
                    // Store table boundary for this page
                    $pageTableBoundaries[$currentPageNumber] = [
                        'startRow' => $currentPageTableStartRow,
                        'endRow' => $lastTableRow
                    ];
                    
                    // Add footer to current page with page number
                    $currentRow = $addFooterSection($sheet, $currentRow, $otr, $currentPageNumber, false, false);
                    
                    // Record page break position
                    $pageBreaks[] = $currentRow;
                    
                    // Start new page
                    $currentPageNumber++;
                    $currentPageStartRow = $currentRow + 1;
                    $currentPageGradeCount = 0;
                    $isContinuationPage = true;
                    
                    // We need to split this semester across pages
                    $currentSemesterContinued = true;
                    
                    // Add header for new page
                    $currentRow = $addHeaderSection($sheet, $currentPageStartRow, $otr, true, $currentPageNumber);
                    
                    // Add "cont'n" continuation text for the split semester
                    $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                    $contSemesterText = $semester . ' ' . $group['school_year'] . ', cont\'n';
                    if ($otr->program) {
                        $contSemesterText .= ' - ' . strtoupper($otr->program->code);
                    }
                    $sheet->setCellValue('A' . $currentRow, $contSemesterText);
                    $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->applyFromArray($semesterStyle);
                    $sheet->getRowDimension($currentRow)->setRowHeight(16);
                    
                    $currentRow++;
                    $currentPageGradeCount++;
                    
                    // Reset table start row for new page
                    $currentPageTableStartRow = $currentRow;
                    $currentPageHasContent = true;
                }
                
                $sheet->setCellValue('A' . $currentRow, $grade->subject_code);
                $sheet->setCellValue('B' . $currentRow, $grade->subject_title);
                $sheet->setCellValue('C' . $currentRow, $grade->units_earned ?? '0');
                $sheet->setCellValue('D' . $currentRow, $grade->final_rating ?? '-');
                
                $sheet->getStyle('B' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add all borders to each grade row to complete the box
                $rowStyle = [
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                        'left' => ['borderStyle' => Border::BORDER_THIN],
                        'right' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ];
                $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->applyFromArray($rowStyle);
                $sheet->getRowDimension($currentRow)->setRowHeight(15);
                
                $currentRow++;
                $currentPageGradeCount++;
                $gradesAdded++;
            }
            
            // Add minimal space between semesters
            $sheet->getRowDimension($currentRow)->setRowHeight(3);
            $currentRow++;
            $currentPageGradeCount++;
            
            $isFirstPage = false;
        }
        
        // Add bottom border to the last row of content on the final page
        if ($currentPageHasContent && $currentRow > $currentPageTableStartRow) {
            $lastTableRow = $currentRow - 1;
            // Ensure all borders including bottom
            $sheet->getStyle('A' . $lastTableRow . ':D' . $lastTableRow)
                ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            
            // Store table boundary for the last page
            $pageTableBoundaries[$currentPageNumber] = [
                'startRow' => $currentPageTableStartRow,
                'endRow' => $lastTableRow
            ];
        }
        
        // Add final footer on last page with page number
        $currentRow = $addFooterSection($sheet, $currentRow, $otr, $currentPageNumber, true, false);
    }
    
    // ========== ENSURE ALL PAGES HAVE COMPLETE TABLE BOX BORDERS ==========
    // Add complete box borders to all table content areas
    foreach ($pageTableBoundaries as $pageNum => $boundary) {
        $startRow = $boundary['startRow'];
        $endRow = $boundary['endRow'];
        
        if ($startRow <= $endRow) {
            // Apply complete box borders to the entire table area for this page
            $tableRange = 'A' . $startRow . ':D' . $endRow;
            
            $completeBoxBorderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ];
            
            // Apply all borders to make a complete box
            $sheet->getStyle($tableRange)->applyFromArray($completeBoxBorderStyle);
            
            // Specifically ensure bottom border on the last row is complete
            $sheet->getStyle('A' . $endRow . ':D' . $endRow)
                ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        }
    }
    
    // ========== SET PAGE BREAKS ==========
    if (!empty($pageBreaks)) {
        sort($pageBreaks);
        
        foreach ($pageBreaks as $breakRow) {
            $sheet->setBreak('A' . $breakRow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        }
        
        // Set overall print area
        $highestRow = $sheet->getHighestRow();
        $sheet->getPageSetup()->setPrintArea('A1:D' . $highestRow);
    }
    
    // ========== FINAL FORMATTING ==========
    $highestRow = $sheet->getHighestRow();
    
    // Enable text wrapping for subject titles only
    $sheet->getStyle('B1:B' . $highestRow)->getAlignment()->setWrapText(true);
    
    // Add thin border around entire document (outer border)
    $contentStyle = [
        'borders' => [
            'outline' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ];
    $sheet->getStyle('A1:D' . $highestRow)->applyFromArray($contentStyle);
    
    // Set repeating rows (only table headers on continuation pages)
    $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
    
    // ========== SAVE AND DOWNLOAD ==========
    $filename = 'CHED_Transcript_' . ($otr->Student_ID ?? 'unknown') . '_' . date('Ymd_His') . '.xlsx';
    
    // Create writer and output
    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

// Helper function to get degree abbreviation
private function getDegreeAbbreviation($programName)
{
    $abbreviations = [
        'Bachelor of Science in Nursing' => 'B.S.N.',
        'Bachelor of Science in Accountancy' => 'B.S.A.',
        'Bachelor of Science in Business Administration' => 'B.S.B.A.',
        'Bachelor of Science in Computer Science' => 'B.S.C.S.',
        'Bachelor of Science in Information Technology' => 'B.S.I.T.',
        'Bachelor of Elementary Education' => 'B.E.Ed.',
        'Bachelor of Secondary Education' => 'B.S.Ed.',
        'Bachelor of Arts in Communication' => 'A.B. Comm.',
        'Bachelor of Arts in Communication' => 'B.A. Comm.',
    ];
    
    return $abbreviations[$programName] ?? 'B.S.';
}
    
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $otrs = Otr::where('First_Name', 'like', "%{$search}%")
            ->orWhere('Last_Name', 'like', "%{$search}%")
            ->orWhere('Student_ID', 'like', "%{$search}%")
            ->orWhereHas('program', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);
            
        return view('registrar.otr.index', compact('otrs'));
    }
}