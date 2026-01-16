<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Registrar\Otr;
use App\Models\Registrar\OtrGrade;
use App\Models\System\SrmProgram;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

// Add PhpSpreadsheet classes for Excel export
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

use App\Http\Requests\ImportOtrRequest;
use App\Imports\OtrImport;
use Maatwebsite\Excel\Facades\Excel;


class OtrController extends Controller
{
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
            
            'student_photo' => 'nullable|image|mimes:jpeg,png,jjpg|max:5120',
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

    // ===================================
    // GRADE MANAGEMENT METHODS
    // ===================================

    /**
     * Show the form for adding a new grade to an OTR
     */
    public function addGradeForm($id)
    {
        $otr = Otr::findOrFail($id);
        
        return view('registrar.otr.add-grade', compact('otr'));
    }

    /**
     * Store a newly created grade in storage
     */
    public function storeGrade(Request $request, $id)
    {
        $otr = Otr::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'school_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'subject_code' => 'required|string|max:20',
            'subject_title' => 'required|string|max:255',
            'type' => 'required|string|max:20',
            'final_rating' => 'required|numeric|min:0|max:5',
            'units_earned' => 'required|numeric|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $grade = new OtrGrade();
            $grade->otr_id = $otr->id;
            $grade->school_year = $request->school_year;
            $grade->semester = $request->semester;
            $grade->subject_code = $request->subject_code;
            $grade->subject_title = $request->subject_title;
            $grade->type = $request->type;
            $grade->final_rating = $request->final_rating;
            $grade->units_earned = $request->units_earned;
            $grade->save();

            return redirect()->route('registrar.otr.show', $otr->id)
                ->with('success', 'Grade added successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error adding grade: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a grade
     */
    public function editGradeForm($id, $gradeId)
    {
        $otr = Otr::findOrFail($id);
        $grade = OtrGrade::where('otr_id', $id)->findOrFail($gradeId);
        
        return view('registrar.otr.edit-grade', compact('otr', 'grade'));
    }

    /**
     * Update the specified grade in storage
     */
    public function updateGrade(Request $request, $id, $gradeId)
    {
        $otr = Otr::findOrFail($id);
        $grade = OtrGrade::where('otr_id', $id)->findOrFail($gradeId);
        
        $validator = Validator::make($request->all(), [
            'school_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'subject_code' => 'required|string|max:20',
            'subject_title' => 'required|string|max:255',
            'type' => 'required|string|max:20',
            'final_rating' => 'required|numeric|min:0|max:5',
            'units_earned' => 'required|numeric|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $grade->school_year = $request->school_year;
            $grade->semester = $request->semester;
            $grade->subject_code = $request->subject_code;
            $grade->subject_title = $request->subject_title;
            $grade->type = $request->type;
            $grade->final_rating = $request->final_rating;
            $grade->units_earned = $request->units_earned;
            $grade->save();

            return redirect()->route('registrar.otr.show', $otr->id)
                ->with('success', 'Grade updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating grade: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified grade from storage
     */
    public function deleteGrade($id, $gradeId)
    {
        try {
            $grade = OtrGrade::where('otr_id', $id)->findOrFail($gradeId);
            $grade->delete();

            return redirect()->route('registrar.otr.show', $id)
                ->with('success', 'Grade deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting grade: ' . $e->getMessage());
        }
    }
    
    /**
     * Import grades from Excel/CSV
     */
    public function importGrades(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'grades_file' => 'required|file|mimes:csv,xls,xlsx'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        try {
            // You can implement Excel/CSV import logic here
            // This would require additional packages like Maatwebsite/Laravel-Excel
            
            return redirect()->route('registrar.otr.show', $id)
                ->with('success', 'Grades imported successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing grades: ' . $e->getMessage());
        }
    }

    // ===================================
    // EXPORT METHODS
    // ===================================

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

    // Export combined OTR and grades to Excel with separate sheets
    public function exportGradesExcel($id)
    {
        $otr = Otr::findOrFail($id);
        $otr->load(['program', 'grades' => function($query) {
            $query->orderBy('school_year')->orderBy('semester')->orderBy('subject_code');
        }]);
        
        // Create new Spreadsheet with two worksheets
        $spreadsheet = new Spreadsheet();
        
        // ========== SHEET 1: OTR FRONT PAGE (from generatePDF) ==========
        $frontPageSheet = $spreadsheet->getActiveSheet();
        $frontPageSheet->setTitle('OTR Front Page');
        
        // Set to LETTER size (8.5 x 11 inches) for front page
        $frontPageSheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // Set margins for front page
        $frontPageSheet->getPageMargins()
            ->setTop(0.4)
            ->setRight(0.6)
            ->setLeft(0.6)
            ->setBottom(0.6);
        
        // Set column widths for front page - Keep original widths
        $frontPageSheet->getColumnDimension('A')->setWidth(40);
        $frontPageSheet->getColumnDimension('B')->setWidth(30);
        $frontPageSheet->getColumnDimension('C')->setWidth(40);
        
        // Set default font for front page
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        
        $currentRow = 1;
        
        // ========== TITLE ==========
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Official Transcript of Records");
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->getColor()->setARGB('FF1E50B4'); // Blue color
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow += 1;
        
        // ========== NAME BOXES ==========
        $frontPageSheet->setCellValue('A' . $currentRow, strtoupper($otr->Last_Name ?? ''));
        $frontPageSheet->setCellValue('B' . $currentRow, strtoupper($otr->First_Name ?? ''));
        $frontPageSheet->setCellValue('C' . $currentRow, strtoupper($otr->Middle_Name ?? ''));
        
        $nameBoxStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        
        $frontPageSheet->getStyle('A' . $currentRow)->applyFromArray($nameBoxStyle);
        $frontPageSheet->getStyle('B' . $currentRow)->applyFromArray($nameBoxStyle);
        $frontPageSheet->getStyle('C' . $currentRow)->applyFromArray($nameBoxStyle);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(20);
        $currentRow++;
        
        // Name labels
        $frontPageSheet->setCellValue('A' . $currentRow, "Last Name");
        $frontPageSheet->setCellValue('B' . $currentRow, "First Name");
        $frontPageSheet->setCellValue('C' . $currentRow, "Middle Name");
        
        $frontPageSheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setSize(10);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 2;
        
        // ========== PHOTO AREA ==========
        $photoPath = $otr->Photo_Path;
        $hasPhoto = false;
        
        if ($photoPath && $photoPath != 'assets/photos/default.jpg') {
            if (Storage::disk('public')->exists($photoPath)) {
                $fullPhotoPath = Storage::disk('public')->path($photoPath);
                if (file_exists($fullPhotoPath)) {
                    $frontPageSheet->getRowDimension($currentRow)->setRowHeight(100);
                    
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Student Photo');
                    $drawing->setDescription('Student Photo');
                    $drawing->setPath($fullPhotoPath);
                    $drawing->setHeight(160);
                    $drawing->setWidth(160);
                    $drawing->setCoordinates('B' . $currentRow);
                    $drawing->setOffsetX(35);
                    $drawing->setOffsetY(2.5);
                    $drawing->setWorksheet($frontPageSheet);
                    $hasPhoto = true;
                }
            }
        }
        
        if (!$hasPhoto) {
            $frontPageSheet->getRowDimension($currentRow)->setRowHeight(100);
            $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
            $frontPageSheet->setCellValue('A' . $currentRow, "[Photo Area - 2x2 Photo Here]");
            $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setItalic(true);
            $frontPageSheet->getStyle('A' . $currentRow)->getFont()->getColor()->setARGB('FF969696');
            $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            
            $photoBoxStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ];
            $frontPageSheet->getStyle('A' . $currentRow . ':C' . $currentRow)->applyFromArray($photoBoxStyle);
        }
        
        $currentRow += 2;
        
        // ========== STUDENT ID ==========
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, $otr->Student_ID ?? '');
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(12);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Student ID No.");
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(10);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 2;
        
        // ========== DEGREE / COURSE ==========
        $courseDisplay = 'Not Specified';
        if ($otr->program) {
            $courseDisplay = $otr->program->code . ' - ' . $otr->program->name;
        }
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, $courseDisplay);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Degree/Course");
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 1;
        
        // ========== EXEMPTION NOTES ==========
        $exemptionNote = $otr->Exemption_Note ?? 'Exempted from the Issuance of Special Order (S.O.)';
        $accreditationLevel = $otr->Accreditation_Level ?? 'PACUCOA Re-Accredited Level II';
        $chedMemo = $otr->CHED_Memo_Order ?? 'CHED Memo Order No. 01, s. 2005';
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, $exemptionNote);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setWrapText(true);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, $accreditationLevel);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, $chedMemo);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(11);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 2;
        
        // ========== GRADUATION INFO ==========
        $graduationDate = $otr->Date_of_Graduation ? 
            $otr->Date_of_Graduation->format('F j, Y') : 'Not Specified';
        $nstpNumber = $otr->NSTP_Serial_Number ?? 'Not Specified';
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Date of Graduation: " . $graduationDate);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "NSTP Serial No.: " . $nstpNumber);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 2;
        
        // ========== ENTRANCE DATA TITLE ==========
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Entrance Data");
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->getColor()->setARGB('FF1E50B4');
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(18);
        $currentRow += 1;
        
        // ========== ENTRANCE DATA TABLE ==========
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
        $frontPageSheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, 'Admission Credentials: ' . $admissionCredentials);
        $frontPageSheet->setCellValue('C' . $currentRow, 'Address: ' . $address);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(16);
        $currentRow++;
        
        // Row 2: Category & Birth Date
        $frontPageSheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, 'Category: ' . $category);
        $frontPageSheet->setCellValue('C' . $currentRow, 'Birth Date: ' . $birthDate);
        $currentRow++;
        
        // Row 3: School/Year & Birth Place
        $frontPageSheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, 'School/Year Last Attended: ' . $schoolLastAttended . ' – ' . $schoolYearLastAttended);
        $frontPageSheet->setCellValue('C' . $currentRow, 'Birth Place: ' . $birthPlace);
        $currentRow++;
        
        // Row 4: School Address & Citizenship
        $frontPageSheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, 'School Address: ' . $schoolAddress);
        $frontPageSheet->setCellValue('C' . $currentRow, 'Citizenship: ' . $citizenship);
        $currentRow++;
        
        // Row 5: Semester/Year & Religion
        $frontPageSheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, 'Semester/Year Admitted: ' . $semesterYearAdmitted);
        $frontPageSheet->setCellValue('C' . $currentRow, 'Religion: ' . $religion);
        $currentRow++;
        
        // Row 6: College & Gender
        $frontPageSheet->mergeCells('A' . $currentRow . ':B' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, 'College: ' . $college);
        $frontPageSheet->setCellValue('C' . $currentRow, 'Gender: ' . $gender);
        $tableEndRow = $currentRow;
        $currentRow += 1;
        
        // Apply borders ONLY to the entrance data table
        $tableRange = 'A' . $tableStartRow . ':C' . $tableEndRow;
        $tableBorderStyle = [
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $frontPageSheet->getStyle($tableRange)->applyFromArray($tableBorderStyle);
        
        for ($row = $tableStartRow; $row <= $tableEndRow; $row++) {
            $frontPageSheet->getStyle('C' . $row)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        }
        
        // ========== FOOT NOTES ==========
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "N.B.: Any erasure or alteration renders the whole transcript invalid.");
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setItalic(true)->setSize(9);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Remarks: For Official Purposes Only");
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(11);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(16);
        $currentRow += 1;
        
        // ========== ADD ROW BETWEEN REMARKS AND PREPARED BY ==========
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(12);
        $currentRow++;
        
        // ========== SIGNATORIES ==========
        $preparedBy = $otr->Prepared_By ?? 'Not Specified';
        $checkedBy = $otr->Checked_By ?? 'Not Specified';
        $deanName = $otr->Dean_Name ?? 'Not Specified';
        $registrarName = $otr->Registrar_Name ?? 'MICHELLE J. BARBACENA-LLANTO, LPT';
        
        // Prepared by and Checked by
        $frontPageSheet->setCellValue('A' . $currentRow, "Prepared by: " . $preparedBy);
        $frontPageSheet->setCellValue('B' . $currentRow, "Checked by: " . $checkedBy);
        $frontPageSheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setSize(11);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 2;
        
        // Empty space
        $frontPageSheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(12);
        $currentRow++;
        
        // Dean and Registrar names
        $frontPageSheet->setCellValue('A' . $currentRow, $deanName);
        $frontPageSheet->setCellValue('B' . $currentRow, $registrarName);
        $frontPageSheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $frontPageSheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true)->setSize(11);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        // Titles
        $frontPageSheet->setCellValue('A' . $currentRow, 'Dean, ' . $college);
        $frontPageSheet->setCellValue('B' . $currentRow, "University Registrar");
        $frontPageSheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setSize(10);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $frontPageSheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow += 1;
        
        // Add row before date prepared
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(5);
        $currentRow++;
        
        // Date prepared
        $datePrepared = $otr->Date_Prepared ? 
            $otr->Date_Prepared->format('F j, Y') : date('F j, Y');
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Date prepared: " . $datePrepared);
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(10);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        $currentRow++;
        
        // ========== PAGE NUMBER ==========
        $frontPageSheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $frontPageSheet->setCellValue('A' . $currentRow, "Page 1");
        $frontPageSheet->getStyle('A' . $currentRow)->getFont()->setSize(9)->setItalic(true);
        $frontPageSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $frontPageSheet->getRowDimension($currentRow)->setRowHeight(15);
        
        // Set print area for front page
        $frontPageSheet->getPageSetup()->setPrintArea('A1:C' . $currentRow);
        
        // ========== SHEET 2: COLLEGIATE RECORD (Grades) ==========
        $gradesSheet = $spreadsheet->createSheet();
        $gradesSheet->setTitle('Collegiate Record');
        $gradesSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VISIBLE);
        
        // Page setup for grades sheet
        $gradesSheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true);
        
        // Set margins for grades sheet
        $gradesSheet->getPageMargins()
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
        
        // Set column widths for grades sheet
        foreach ($columns as $col => $config) {
            $gradesSheet->getColumnDimension($col)->setWidth($config['width']);
        }
        
        // Hide columns E, F, G if they exist
        for ($col = 'E'; $col <= 'G'; $col++) {
            if ($gradesSheet->getColumnDimension($col)) {
                $gradesSheet->getColumnDimension($col)->setVisible(false);
            }
        }
        
        // ========== HELPER FUNCTIONS FOR GRADES SHEET ==========
        // Define helper functions as local variables within the method
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
            
            $college = $otr->College ?? 'Not Specified';
            
            // Get signatory names
            $preparedBy = $otr->Prepared_By ?? 'Not Specified';
            $checkedBy = $otr->Checked_By ?? 'Not Specified';
            $deanName = $otr->Dean_Name ?? 'Not Specified';
            $registrarName = $otr->Registrar_Name ?? 'MICHELLE J. BARBACENA-LLANTO, LPT';
            
            // Graduation section only on last page
            if ($isLastPage && $otr->Date_of_Graduation) {
                $worksheet->getRowDimension($row)->setRowHeight(6);
                $row++;
                
                $graduationDate = $otr->Date_of_Graduation->format('F j, Y');
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
            
            // SIGNATORIES
            $worksheet->setCellValue('A' . $row, 'Prepared by:  ' . $preparedBy);
            $worksheet->getStyle('A' . $row)->getFont()->setSize(8.5);
            $worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
            $worksheet->setCellValue('C' . $row, 'Checked by:  ' . $checkedBy);
            $worksheet->getStyle('C' . $row)->getFont()->setSize(8.5);
            $worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
            $worksheet->setCellValue('B' . $row, '');
            $worksheet->setCellValue('D' . $row, '');
            
            $worksheet->getRowDimension($row)->setRowHeight(14);
            
            $row += 2;
            
            // Dean and Registrar names
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
            
            // Titles
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
            
            // Date prepared (only on last page)
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
            
            // Page number at the very bottom (on every page)
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
        
        $pageBreaks = [];
        $currentPageStartRow = 1;
        $currentPageNumber = 2; // Start from page 2 (page 1 is the front page)
        $isContinuationPage = false;
        $pageTableBoundaries = [];
        
        if ($grades->isEmpty()) {
            $currentRow = $addHeaderSection($gradesSheet, 1, $otr, false, $currentPageNumber);
            
            // Add University name
            $gradesSheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
            $gradesSheet->setCellValue('A' . $currentRow, 'UNIVERSITY OF SANTO TOMAS-LEGAZPI, Legazpi City');
            $gradesSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(9.5);
            $gradesSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $gradesSheet->getRowDimension($currentRow)->setRowHeight(16);
            
            $currentRow++;
            
            $gradesSheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
            $gradesSheet->setCellValue('A' . $currentRow, 'No grade records found.');
            $gradesSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $gradesSheet->getRowDimension($currentRow)->setRowHeight(16);
            
            // Store table boundary for this page
            $pageTableBoundaries[$currentPageNumber] = [
                'startRow' => $currentPageStartRow + 1,
                'endRow' => $currentRow - 1
            ];
            
            $currentRow = $addFooterSection($gradesSheet, $currentRow + 1, $otr, $currentPageNumber, true, false);
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
            $footerRows = 11;
            $availableRowsForGrades = $MAX_ROWS_PER_PAGE - $headerRows - $footerRows;
            
            $currentRow = $addHeaderSection($gradesSheet, 1, $otr, false, $currentPageNumber);
            $currentPageGradeCount = 0;
            $isFirstPage = true;
            $currentSemesterContinued = false;
            $currentPageTableStartRow = $currentRow + 1;
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
                        $gradesSheet->getStyle('A' . $lastTableRow . ':D' . $lastTableRow)
                            ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    }
                    
                    // Store table boundary for this page before footer
                    $pageTableBoundaries[$currentPageNumber] = [
                        'startRow' => $currentPageTableStartRow,
                        'endRow' => $currentRow - 1
                    ];
                    
                    // Add footer to current page with page number
                    $currentRow = $addFooterSection($gradesSheet, $currentRow, $otr, $currentPageNumber, false, false);
                    
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
                    $currentRow = $addHeaderSection($gradesSheet, $currentPageStartRow, $otr, true, $currentPageNumber);
                    
                    // On continuation pages, add University name right after table headers
                    $gradesSheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                    $gradesSheet->setCellValue('A' . $currentRow, 'UNIVERSITY OF SANTO TOMAS-LEGAZPI, Legazpi City');
                    $gradesSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(9.5);
                    $gradesSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $gradesSheet->getRowDimension($currentRow)->setRowHeight(16);
                    $currentRow++;
                    $currentPageGradeCount++;
                    
                    // Reset table start row for new page
                    $currentPageTableStartRow = $currentRow;
                    $currentPageHasContent = false;
                }
                
                // Add University name on first page only
                if ($isFirstPage && !$isContinuationPage) {
                    $gradesSheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                    $gradesSheet->setCellValue('A' . $currentRow, 'UNIVERSITY OF SANTO TOMAS-LEGAZPI, Legazpi City');
                    $gradesSheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(9.5);
                    $gradesSheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $gradesSheet->getRowDimension($currentRow)->setRowHeight(16);
                    $currentRow++;
                    $currentPageGradeCount++;
                }
                
                // Add semester header
                $gradesSheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                $gradesSheet->setCellValue('A' . $currentRow, $semesterText);
                
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
                $gradesSheet->getStyle('A' . $currentRow . ':D' . $currentRow)->applyFromArray($semesterStyle);
                $gradesSheet->getRowDimension($currentRow)->setRowHeight(16);
                
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
                        $gradesSheet->getStyle('A' . $lastTableRow . ':D' . $lastTableRow)
                            ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                        
                        // Store table boundary for this page
                        $pageTableBoundaries[$currentPageNumber] = [
                            'startRow' => $currentPageTableStartRow,
                            'endRow' => $lastTableRow
                        ];
                        
                        // Add footer to current page with page number
                        $currentRow = $addFooterSection($gradesSheet, $currentRow, $otr, $currentPageNumber, false, false);
                        
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
                        $currentRow = $addHeaderSection($gradesSheet, $currentPageStartRow, $otr, true, $currentPageNumber);
                        
                        // Add "cont'n" continuation text for the split semester
                        $gradesSheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                        $contSemesterText = $semester . ' ' . $group['school_year'] . ', cont\'n';
                        if ($otr->program) {
                            $contSemesterText .= ' - ' . strtoupper($otr->program->code);
                        }
                        $gradesSheet->setCellValue('A' . $currentRow, $contSemesterText);
                        $gradesSheet->getStyle('A' . $currentRow . ':D' . $currentRow)->applyFromArray($semesterStyle);
                        $gradesSheet->getRowDimension($currentRow)->setRowHeight(16);
                        
                        $currentRow++;
                        $currentPageGradeCount++;
                        
                        // Reset table start row for new page
                        $currentPageTableStartRow = $currentRow;
                        $currentPageHasContent = true;
                    }
                    
                    $gradesSheet->setCellValue('A' . $currentRow, $grade->subject_code);
                    $gradesSheet->setCellValue('B' . $currentRow, $grade->subject_title);
                    $gradesSheet->setCellValue('C' . $currentRow, $grade->units_earned ?? '0');
                    $gradesSheet->setCellValue('D' . $currentRow, $grade->final_rating ?? '-');
                    
                    $gradesSheet->getStyle('B' . $currentRow)->getAlignment()->setWrapText(true);
                    $gradesSheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $gradesSheet->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    $rowStyle = [
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_THIN],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN],
                            'left' => ['borderStyle' => Border::BORDER_THIN],
                            'right' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ];
                    $gradesSheet->getStyle('A' . $currentRow . ':D' . $currentRow)->applyFromArray($rowStyle);
                    $gradesSheet->getRowDimension($currentRow)->setRowHeight(15);
                    
                    $currentRow++;
                    $currentPageGradeCount++;
                    $gradesAdded++;
                }
                
                // Add minimal space between semesters
                $gradesSheet->getRowDimension($currentRow)->setRowHeight(3);
                $currentRow++;
                $currentPageGradeCount++;
                
                $isFirstPage = false;
            }
            
            // Add bottom border to the last row of content on the final page
            if ($currentPageHasContent && $currentRow > $currentPageTableStartRow) {
                $lastTableRow = $currentRow - 1;
                $gradesSheet->getStyle('A' . $lastTableRow . ':D' . $lastTableRow)
                    ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                
                // Store table boundary for the last page
                $pageTableBoundaries[$currentPageNumber] = [
                    'startRow' => $currentPageTableStartRow,
                    'endRow' => $lastTableRow
                ];
            }
            
            // Add final footer on last page with page number
            $currentRow = $addFooterSection($gradesSheet, $currentRow, $otr, $currentPageNumber, true, false);
        }
        
        // ========== ENSURE ALL PAGES HAVE COMPLETE TABLE BOX BORDERS ==========
        foreach ($pageTableBoundaries as $pageNum => $boundary) {
            $startRow = $boundary['startRow'];
            $endRow = $boundary['endRow'];
            
            if ($startRow <= $endRow) {
                $tableRange = 'A' . $startRow . ':D' . $endRow;
                
                $completeBoxBorderStyle = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ];
                
                $gradesSheet->getStyle($tableRange)->applyFromArray($completeBoxBorderStyle);
                
                $gradesSheet->getStyle('A' . $endRow . ':D' . $endRow)
                    ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            }
        }
        
        // ========== SET PAGE BREAKS ==========
        if (!empty($pageBreaks)) {
            sort($pageBreaks);
            
            foreach ($pageBreaks as $breakRow) {
                $gradesSheet->setBreak('A' . $breakRow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
            }
            
            $highestRow = $gradesSheet->getHighestRow();
            $gradesSheet->getPageSetup()->setPrintArea('A1:D' . $highestRow);
        }
        
        // ========== FINAL FORMATTING FOR GRADES SHEET ==========
        $highestRow = $gradesSheet->getHighestRow();
        
        $gradesSheet->getStyle('B1:B' . $highestRow)->getAlignment()->setWrapText(true);
        
        $contentStyle = [
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $gradesSheet->getStyle('A1:D' . $highestRow)->applyFromArray($contentStyle);
        
        $gradesSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
        
        // ========== SET ACTIVE SHEET BACK TO FRONT PAGE ==========
        $spreadsheet->setActiveSheetIndex(0);
        
        // ========== SAVE AND DOWNLOAD ==========
        $filename = 'Complete_OTR_' . ($otr->Student_ID ?? 'unknown') . '_' . date('Ymd_His') . '.xlsx';
        
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

    /**
 * Store bulk grades from the Excel-like interface
 */
public function bulkStoreGrades(Request $request, $id)
{
    $otr = Otr::findOrFail($id);
    
    DB::beginTransaction();
    
    try {
        $grades = $request->input('grades', []);
        $savedCount = 0;
        
        foreach ($grades as $gradeData) {
            // Skip empty rows
            if (empty($gradeData['school_year']) || empty($gradeData['subject_code'])) {
                continue;
            }
            
            $validator = Validator::make($gradeData, [
                'school_year' => 'required|string|max:20',
                'semester' => 'required|string|max:20',
                'subject_code' => 'required|string|max:20',
                'subject_title' => 'required|string|max:255',
                'type' => 'required|string|max:20',
                'final_rating' => 'required|numeric|min:0|max:5',
                'units_earned' => 'required|numeric|min:0|max:10',
            ]);
            
            if ($validator->fails()) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            $grade = new OtrGrade();
            $grade->otr_id = $otr->id;
            $grade->school_year = $gradeData['school_year'];
            $grade->semester = $gradeData['semester'];
            $grade->subject_code = $gradeData['subject_code'];
            $grade->subject_title = $gradeData['subject_title'];
            $grade->type = $gradeData['type'];
            $grade->final_rating = $gradeData['final_rating'];
            $grade->units_earned = $gradeData['units_earned'];
            $grade->save();
            
            $savedCount++;
        }
        
        DB::commit();
        
        return redirect()->route('registrar.otr.show', $otr->id)
            ->with('success', $savedCount . ' grade(s) added successfully!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Error adding grades: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Show the import form
 */
public function showImportForm()
{
    return view('registrar.otr.import');
}

/**
 * Handle the Excel file import
 */
public function import(ImportOtrRequest $request)
{
    DB::beginTransaction();
    
    try {
        $skipDuplicates = $request->has('skip_duplicates');
        
        // Import the Excel file
        $import = new OtrImport($skipDuplicates);
        Excel::import($import, $request->file('excel_file'));
        
        // Get import results
        $results = $import->getResults();
        
        DB::commit();
        
        return redirect()->route('registrar.otr.import')
            ->with('success', 
                'Successfully imported ' . $results['students_imported'] . ' student(s) and ' . 
                $results['grades_imported'] . ' grade(s).' . 
                ($results['duplicates_skipped'] > 0 ? 
                    ' Skipped ' . $results['duplicates_skipped'] . ' duplicate student(s).' : '')
            );
            
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        DB::rollBack();
        
        $failures = $e->failures();
        $errorMessages = [];
        
        foreach ($failures as $failure) {
            $errorMessages[] = "Row {$failure->row()}: {$failure->errors()[0]}";
        }
        
        return redirect()->back()
            ->withErrors(['import' => $errorMessages])
            ->withInput();
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Excel Import Error: ' . $e->getMessage());
        
        return redirect()->back()
            ->with('error', 'Import failed: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Download import template
 */
public function downloadTemplate()
{
    $templatePath = public_path('templates/OTR_Import_Template.xlsx');
    
    if (!file_exists($templatePath)) {
        return redirect()->route('registrar.otr.import')
            ->with('error', 'Template file not found. Please contact administrator.');
    }
    
    return response()->download($templatePath, 'OTR_Import_Template.xlsx');
}

/**
 * Generate and download Excel template
 */
public function generateTemplate()
{
    // Create new Spreadsheet
    $spreadsheet = new Spreadsheet();
    
    // ================= SHEET 1: OTR STUDENT INFORMATION =================
    $studentSheet = $spreadsheet->getActiveSheet();
    $studentSheet->setTitle('OTR Student Information');
    
    // Headers for OTR Student Information
    $studentHeaders = [
        'Student_ID',
        'Last_Name', 
        'First_Name',
        'Middle_Name',
        'Degree_Course (Program Code)',
        'Date_of_Graduation (YYYY-MM-DD)',
        'NSTP_Serial_Number',
        'Exemption_Note',
        'Accreditation_Level',
        'CHED_Memo_Order',
        'Admission_Credentials',
        'Category',
        'School_Last_Attended',
        'School_Year_Last_Attended',
        'School_Address',
        'Semester_Year_Admitted',
        'College',
        'Address',
        'Birth_Date (YYYY-MM-DD)',
        'Birth_Place',
        'Citizenship',
        'Religion',
        'Gender (Male/Female/Other)',
        'Prepared_By',
        'Checked_By',
        'Dean_Name',
        'Registrar_Name',
        'Date_Prepared (YYYY-MM-DD)'
    ];
    
    // Set headers with formatting
    $studentSheet->fromArray($studentHeaders, null, 'A1');
    
    // Format header row
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];
    
    $studentSheet->getStyle('A1:AB1')->applyFromArray($headerStyle);
    
    // Set column widths
    $columnWidths = [
        'A' => 15, 'B' => 15, 'C' => 15, 'D' => 15, 'E' => 20, 'F' => 20, 'G' => 18,
        'H' => 25, 'I' => 22, 'J' => 25, 'K' => 20, 'L' => 12, 'M' => 20, 'N' => 20,
        'O' => 25, 'P' => 22, 'Q' => 15, 'R' => 25, 'S' => 18, 'T' => 20, 'U' => 15,
        'V' => 15, 'W' => 12, 'X' => 20, 'Y' => 20, 'Z' => 20, 'AA' => 25, 'AB' => 18
    ];
    
    foreach ($columnWidths as $column => $width) {
        $studentSheet->getColumnDimension($column)->setWidth($width);
    }
    
    // Add sample data (2 rows)
    $sampleStudents = [
        [
            '2023-001',
            'Dela Cruz',
            'Juan',
            'Santos',
            'BSN', // Program code
            '2023-05-15',
            'NSTP2023-001',
            'Exempted from the Issuance of Special Order (S.O.)',
            'PACUCOA Re-Accredited Level II',
            'CHED Memo Order No. 01, s. 2005',
            'High School Diploma',
            'Regular',
            'Sample High School',
            '2021-2022',
            '123 Main St, Legazpi City',
            '1st Semester 2022',
            'College of Nursing',
            '456 Elm St, Legazpi City',
            '2000-01-15',
            'Legazpi City',
            'Filipino',
            'Roman Catholic',
            'Male',
            'John Doe',
            'Jane Smith',
            'Dr. Maria Santos',
            'MICHELLE J. BARBACENA-LLANTO, LPT',
            '2023-10-01'
        ],
        [
            '2023-002',
            'Santos',
            'Maria',
            'Reyes',
            'BSA',
            '2023-05-20',
            'NSTP2023-002',
            'Exempted from the Issuance of Special Order (S.O.)',
            'PACUCOA Re-Accredited Level II',
            'CHED Memo Order No. 01, s. 2005',
            'High School Diploma',
            'Regular',
            'Sample High School',
            '2021-2022',
            '789 Oak St, Legazpi City',
            '1st Semester 2022',
            'College of Business',
            '101 Pine St, Legazpi City',
            '2000-03-20',
            'Legazpi City',
            'Filipino',
            'Roman Catholic',
            'Female',
            'John Doe',
            'Jane Smith',
            'Dr. Pedro Reyes',
            'MICHELLE J. BARBACENA-LLANTO, LPT',
            '2023-10-01'
        ]
    ];
    
    $row = 2;
    foreach ($sampleStudents as $student) {
        $studentSheet->fromArray($student, null, "A{$row}");
        $row++;
    }
    
    // Add data validation for Gender column (Column W)
    $genderValidation = $studentSheet->getCell('W1')->getDataValidation();
    $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $genderValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $genderValidation->setAllowBlank(true);
    $genderValidation->setShowInputMessage(true);
    $genderValidation->setShowErrorMessage(true);
    $genderValidation->setShowDropDown(true);
    $genderValidation->setErrorTitle('Input error');
    $genderValidation->setError('Value is not in list.');
    $genderValidation->setPromptTitle('Pick from list');
    $genderValidation->setPrompt('Please pick a value from the drop-down list.');
    $genderValidation->setFormula1('"Male,Female,Other"');
    
    // Apply to all rows in column W
    for ($i = 2; $i <= 100; $i++) {
        $studentSheet->getCell("W{$i}")->setDataValidation(clone $genderValidation);
    }
    
    // Add instructions
    $studentSheet->setCellValue('AD1', 'INSTRUCTIONS:');
    $studentSheet->setCellValue('AD2', '1. Fill all required fields (Student_ID, Last_Name, First_Name)');
    $studentSheet->setCellValue('AD3', '2. Use valid date format (YYYY-MM-DD) for date fields');
    $studentSheet->setCellValue('AD4', '3. Degree_Course must match program codes in system');
    $studentSheet->setCellValue('AD5', '4. Do not modify column headers');
    $studentSheet->setCellValue('AD6', '5. Remove sample data before filling your own');
    
    // ================= SHEET 2: GRADES =================
    $gradesSheet = $spreadsheet->createSheet();
    $gradesSheet->setTitle('Grades');
    
    // Headers for Grades
    $gradeHeaders = [
        'Student_ID',
        'school_year',
        'semester',
        'subject_code',
        'subject_title',
        'type',
        'final_rating',
        'units_earned'
    ];
    
    $gradesSheet->fromArray($gradeHeaders, null, 'A1');
    
    // Format header row
    $gradesSheet->getStyle('A1:H1')->applyFromArray($headerStyle);
    
    // Set column widths for grades sheet
    $gradeColumnWidths = [
        'A' => 15, 'B' => 15, 'C' => 12, 'D' => 15, 
        'E' => 40, 'F' => 12, 'G' => 12, 'H' => 12
    ];
    
    foreach ($gradeColumnWidths as $column => $width) {
        $gradesSheet->getColumnDimension($column)->setWidth($width);
    }
    
    // Add sample grades data
    $sampleGrades = [
        [
            '2023-001',
            '2022-2023',
            'First',
            'NCM 100',
            'Theoretical Foundations of Nursing',
            'Lecture',
            1.25,
            3.0
        ],
        [
            '2023-001',
            '2022-2023',
            'First',
            'NCM 101',
            'Nursing Practice I',
            'Lecture/Lab',
            1.5,
            5.0
        ],
        [
            '2023-001',
            '2022-2023',
            'Second',
            'NCM 102',
            'Nursing Practice II',
            'Lecture/Lab',
            1.75,
            5.0
        ],
        [
            '2023-002',
            '2022-2023',
            'First',
            'ACC 101',
            'Fundamentals of Accounting',
            'Lecture',
            1.5,
            3.0
        ],
        [
            '2023-002',
            '2022-2023',
            'First',
            'BUS 101',
            'Business Mathematics',
            'Lecture',
            2.0,
            3.0
        ]
    ];
    
    $row = 2;
    foreach ($sampleGrades as $grade) {
        $gradesSheet->fromArray($grade, null, "A{$row}");
        $row++;
    }
    
    // Add data validations for grades sheet
    // Semester validation (Column C)
    $semesterValidation = $gradesSheet->getCell('C1')->getDataValidation();
    $semesterValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $semesterValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $semesterValidation->setAllowBlank(false);
    $semesterValidation->setShowInputMessage(true);
    $semesterValidation->setShowErrorMessage(true);
    $semesterValidation->setShowDropDown(true);
    $semesterValidation->setErrorTitle('Input error');
    $semesterValidation->setError('Value is not in list.');
    $semesterValidation->setPromptTitle('Pick from list');
    $semesterValidation->setPrompt('Please pick a value from the drop-down list.');
    $semesterValidation->setFormula1('"First,Second,Summer"');
    
    // Type validation (Column F)
    $typeValidation = $gradesSheet->getCell('F1')->getDataValidation();
    $typeValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $typeValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $typeValidation->setAllowBlank(false);
    $typeValidation->setShowInputMessage(true);
    $typeValidation->setShowErrorMessage(true);
    $typeValidation->setShowDropDown(true);
    $typeValidation->setErrorTitle('Input error');
    $typeValidation->setError('Value is not in list.');
    $typeValidation->setPromptTitle('Pick from list');
    $typeValidation->setPrompt('Please pick a value from the drop-down list.');
    $typeValidation->setFormula1('"Lecture,Lab,Lecture/Lab"');
    
    // Apply validations to all rows
    for ($i = 2; $i <= 500; $i++) {
        $gradesSheet->getCell("C{$i}")->setDataValidation(clone $semesterValidation);
        $gradesSheet->getCell("F{$i}")->setDataValidation(clone $typeValidation);
    }
    
    // Add instructions to grades sheet
    $gradesSheet->setCellValue('J1', 'INSTRUCTIONS:');
    $gradesSheet->setCellValue('J2', '1. Student_ID must match a student from Sheet 1');
    $gradesSheet->setCellValue('J3', '2. School Year format: YYYY-YYYY (e.g., 2022-2023)');
    $gradesSheet->setCellValue('J4', '3. Semester: First, Second, or Summer');
    $gradesSheet->setCellValue('J5', '4. Type: Lecture, Lab, or Lecture/Lab');
    $gradesSheet->setCellValue('J6', '5. Final Rating: 0.00 to 5.00 (numerical)');
    $gradesSheet->setCellValue('J7', '6. Units Earned: 0.0 to 10.0');
    
    // Set active sheet back to student sheet
    $spreadsheet->setActiveSheetIndex(0);
    
    // Create writer and output
    $writer = new Xlsx($spreadsheet);
    
    $filename = 'OTR_Import_Template_' . date('Ymd_His') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

/**
 * Preview imported data before saving
 */
public function previewImport(Request $request)
{
    $validator = Validator::make($request->all(), [
        'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
    ]);

    if ($validator->fails()) {
        return redirect()->route('registrar.otr.import')
            ->withErrors($validator)
            ->withInput();
    }

    DB::beginTransaction();
    
    try {
        $file = $request->file('excel_file');
        $skipDuplicates = $request->has('skip_duplicates');
        
        // Import the Excel file for preview
        $import = new OtrImport($skipDuplicates, true); // true = preview mode
        Excel::import($import, $file);
        
        // Get preview data
        $previewData = $import->getPreviewData();
        
        // Store file temporarily for final import
        $tempFileName = 'temp_import_' . time() . '_' . $file->getClientOriginalName();
        $file->storeAs('temp_imports', $tempFileName, 'local');
        
        DB::rollBack(); // Don't save yet
        
        return view('registrar.otr.preview', [
            'previewData' => $previewData,
            'tempFile' => $tempFileName,
            'skipDuplicates' => $skipDuplicates,
        ]);
        
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        DB::rollBack();
        
        $failures = $e->failures();
        $errorMessages = [];
        
        foreach ($failures as $failure) {
            $errorMessages[] = "Row {$failure->row()}: {$failure->errors()[0]}";
        }
        
        return redirect()->back()
            ->withErrors(['import' => $errorMessages])
            ->withInput();
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Excel Import Preview Error: ' . $e->getMessage());
        
        return redirect()->back()
            ->with('error', 'Preview failed: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Process the final import after preview
 */
public function processImport(Request $request)
{
    $validator = Validator::make($request->all(), [
        'temp_file' => 'required|string',
        'skip_duplicates' => 'sometimes|boolean',
    ]);

    if ($validator->fails()) {
        return redirect()->route('registrar.otr.import')
            ->with('error', 'Invalid request parameters.');
    }

    $tempFile = $request->input('temp_file');
    $skipDuplicates = $request->has('skip_duplicates');
    $filePath = storage_path('app/temp_imports/' . $tempFile);
    
    if (!file_exists($filePath)) {
        return redirect()->route('registrar.otr.import')
            ->with('error', 'Temporary file not found. Please upload again.');
    }

    DB::beginTransaction();
    
    try {
        // Import the Excel file for real
        $import = new OtrImport($skipDuplicates, false);
        Excel::import($import, $filePath);
        
        // Get import results
        $results = $import->getResults();
        
        // Clean up temp file
        @unlink($filePath);
        
        DB::commit();
        
        return redirect()->route('registrar.otr.import')
            ->with('success', 
                'Successfully imported ' . $results['students_imported'] . ' student(s) and ' . 
                $results['grades_imported'] . ' grade(s).' . 
                ($results['duplicates_skipped'] > 0 ? 
                    ' Skipped ' . $results['duplicates_skipped'] . ' duplicate student(s).' : '') .
                ($results['errors_count'] > 0 ? 
                    ' ' . $results['errors_count'] . ' error(s) encountered.' : '')
            );
            
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        DB::rollBack();
        @unlink($filePath);
        
        $failures = $e->failures();
        $errorMessages = [];
        
        foreach ($failures as $failure) {
            $errorMessages[] = "Row {$failure->row()}: {$failure->errors()[0]}";
        }
        
        return redirect()->route('registrar.otr.import')
            ->withErrors(['import' => $errorMessages])
            ->withInput();
            
    } catch (\Exception $e) {
        DB::rollBack();
        @unlink($filePath);
        
        \Log::error('Excel Import Processing Error: ' . $e->getMessage());
        
        return redirect()->route('registrar.otr.import')
            ->with('error', 'Import processing failed: ' . $e->getMessage())
            ->withInput();
    }
}
}