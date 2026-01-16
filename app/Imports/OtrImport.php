<?php

namespace App\Imports;

use App\Models\Registrar\Otr;
use App\Models\Registrar\OtrGrade;
use App\Models\System\SrmProgram;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class OtrImport implements WithMultipleSheets
{
    protected $skipDuplicates;
    protected $results = [
        'students_imported' => 0,
        'grades_imported' => 0,
        'duplicates_skipped' => 0,
        'errors' => []
    ];
    
    protected $studentIdMap = []; // Maps Student_ID to Otr ID
    protected $programCodeMap = []; // Maps program codes to IDs
    
    public function __construct($skipDuplicates = false)
    {
        $this->skipDuplicates = $skipDuplicates;
        
        // Preload program codes for mapping
        $programs = SrmProgram::all();
        foreach ($programs as $program) {
            $this->programCodeMap[$program->code] = $program->id;
        }
    }
    
    public function sheets(): array
    {
        return [
            'OTR Student Information' => new OtrSheetImport($this),
            'Grades' => new GradesSheetImport($this),
        ];
    }
    
    // Add getter for skipDuplicates
    public function shouldSkipDuplicates()
    {
        return $this->skipDuplicates;
    }
    
    public function getResults()
    {
        return $this->results;
    }
    
    public function addStudentMapping($studentId, $otrId)
    {
        $this->studentIdMap[$studentId] = $otrId;
    }
    
    public function getStudentId($studentId)
    {
        return $this->studentIdMap[$studentId] ?? null;
    }
    
    public function getProgramId($programCode)
    {
        return $this->programCodeMap[$programCode] ?? null;
    }
    
    public function incrementStudentsImported()
    {
        $this->results['students_imported']++;
    }
    
    public function incrementGradesImported()
    {
        $this->results['grades_imported']++;
    }
    
    public function incrementDuplicatesSkipped()
    {
        $this->results['duplicates_skipped']++;
    }
    
    public function addError($error)
    {
        $this->results['errors'][] = $error;
    }
}

class OtrSheetImport implements ToCollection
{
    protected $import;
    
    public function __construct($import)
    {
        $this->import = $import;
    }
    
    public function collection(Collection $rows)
    {
        // Skip the header row
        $rows = $rows->slice(1);
        
        foreach ($rows as $rowIndex => $row) {
            // Skip empty rows
            if ($row->filter()->isEmpty()) {
                continue;
            }
            
            try {
                $this->processStudentRow($row, $rowIndex + 2); // +2 for header and 1-based index
            } catch (\Exception $e) {
                $this->import->addError("Row {$rowIndex}: " . $e->getMessage());
                continue;
            }
        }
    }
    
    private function processStudentRow($row, $rowNumber)
    {
        // Map Excel columns to database fields
        $data = [
            'Student_ID' => $row[0] ?? null, // Column A
            'Last_Name' => $row[1] ?? null, // Column B
            'First_Name' => $row[2] ?? null, // Column C
            'Middle_Name' => $row[3] ?? null, // Column D
            'Degree_Course' => $row[4] ?? null, // Column E (program code)
            'Date_of_Graduation' => $row[5] ?? null, // Column F
            'NSTP_Serial_Number' => $row[6] ?? null, // Column G
            'Exemption_Note' => $row[7] ?? 'Exempted from the Issuance of Special Order (S.O.)', // Column H
            'Accreditation_Level' => $row[8] ?? 'PACUCOA Re-Accredited Level II', // Column I
            'CHED_Memo_Order' => $row[9] ?? 'CHED Memo Order No. 01, s. 2005', // Column J
            'Admission_Credentials' => $row[10] ?? null, // Column K
            'Category' => $row[11] ?? null, // Column L
            'School_Last_Attended' => $row[12] ?? null, // Column M
            'School_Year_Last_Attended' => $row[13] ?? null, // Column N
            'School_Address' => $row[14] ?? null, // Column O
            'Semester_Year_Admitted' => $row[15] ?? null, // Column P
            'College' => $row[16] ?? null, // Column Q
            'Address' => $row[17] ?? null, // Column R
            'Birth_Date' => $row[18] ?? null, // Column S
            'Birth_Place' => $row[19] ?? null, // Column T
            'Citizenship' => $row[20] ?? null, // Column U
            'Religion' => $row[21] ?? null, // Column V
            'Gender' => $row[22] ?? null, // Column W
            'Prepared_By' => $row[23] ?? null, // Column X
            'Checked_By' => $row[24] ?? null, // Column Y
            'Dean_Name' => $row[25] ?? null, // Column Z
            'Registrar_Name' => $row[26] ?? 'MICHELLE J. BARBACENA-LLANTO, LPT', // Column AA
            'Date_Prepared' => $row[27] ?? now(), // Column AB
        ];
        
        // Validate required fields
        $validator = Validator::make($data, [
            'Student_ID' => 'required|string|max:50',
            'Last_Name' => 'required|string|max:255',
            'First_Name' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }
        
        // Check for duplicate Student_ID
        if (Otr::where('Student_ID', $data['Student_ID'])->exists()) {
            // FIXED: Use the getter method instead of accessing protected property directly
            if ($this->import->shouldSkipDuplicates()) {
                $this->import->incrementDuplicatesSkipped();
                return;
            } else {
                throw new \Exception("Duplicate Student ID: {$data['Student_ID']}");
            }
        }
        
        // Convert program code to ID
        if ($data['Degree_Course'] && $programId = $this->import->getProgramId($data['Degree_Course'])) {
            $data['Degree_Course'] = $programId;
        } else {
            $data['Degree_Course'] = null;
        }
        
        // Format dates
        if ($data['Date_of_Graduation']) {
            try {
                $data['Date_of_Graduation'] = Carbon::parse($data['Date_of_Graduation']);
            } catch (\Exception $e) {
                $data['Date_of_Graduation'] = null;
            }
        }
        
        if ($data['Birth_Date']) {
            try {
                $data['Birth_Date'] = Carbon::parse($data['Birth_Date']);
            } catch (\Exception $e) {
                $data['Birth_Date'] = null;
            }
        }
        
        if ($data['Date_Prepared']) {
            try {
                $data['Date_Prepared'] = Carbon::parse($data['Date_Prepared']);
            } catch (\Exception $e) {
                $data['Date_Prepared'] = now();
            }
        }
        
        // Set default photo path
        $data['Photo_Path'] = 'assets/photos/default.jpg';
        
        // Create the OTR record
        $otr = Otr::create($data);
        
        // Store mapping for grades import
        $this->import->addStudentMapping($data['Student_ID'], $otr->id);
        $this->import->incrementStudentsImported();
    }
}

class GradesSheetImport implements ToCollection
{
    protected $import;
    
    public function __construct($import)
    {
        $this->import = $import;
    }
    
    public function collection(Collection $rows)
    {
        // Skip the header row
        $rows = $rows->slice(1);
        
        foreach ($rows as $rowIndex => $row) {
            // Skip empty rows
            if ($row->filter()->isEmpty()) {
                continue;
            }
            
            try {
                $this->processGradeRow($row, $rowIndex + 2);
            } catch (\Exception $e) {
                $this->import->addError("Grades Row {$rowIndex}: " . $e->getMessage());
                continue;
            }
        }
    }
    
    private function processGradeRow($row, $rowNumber)
    {
        // Map Excel columns to database fields
        $data = [
            'Student_ID' => $row[0] ?? null, // Column A
            'school_year' => $row[1] ?? null, // Column B
            'semester' => $row[2] ?? null, // Column C
            'subject_code' => $row[3] ?? null, // Column D
            'subject_title' => $row[4] ?? null, // Column E
            'type' => $row[5] ?? null, // Column F
            'final_rating' => $row[6] ?? null, // Column G
            'units_earned' => $row[7] ?? null, // Column H
        ];
        
        // Validate required fields
        $validator = Validator::make($data, [
            'Student_ID' => 'required|string|max:50',
            'school_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'subject_code' => 'required|string|max:20',
            'subject_title' => 'required|string|max:255',
            'type' => 'required|string|max:20',
            'final_rating' => 'required|numeric|min:0|max:5',
            'units_earned' => 'required|numeric|min:0|max:10',
        ]);
        
        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }
        
        // Get Otr ID from mapping
        $otrId = $this->import->getStudentId($data['Student_ID']);
        
        if (!$otrId) {
            throw new \Exception("Student ID not found: {$data['Student_ID']}. Make sure student exists in Sheet 1.");
        }
        
        // Check for duplicate grade (same student, subject, semester, year)
        $exists = OtrGrade::where('otr_id', $otrId)
            ->where('subject_code', $data['subject_code'])
            ->where('school_year', $data['school_year'])
            ->where('semester', $data['semester'])
            ->exists();
            
        if ($exists) {
            // Skip duplicate grades
            return;
        }
        
        // Create grade record
        OtrGrade::create([
            'otr_id' => $otrId,
            'school_year' => $data['school_year'],
            'semester' => $data['semester'],
            'subject_code' => $data['subject_code'],
            'subject_title' => $data['subject_title'],
            'type' => $data['type'],
            'final_rating' => $data['final_rating'],
            'units_earned' => $data['units_earned'],
        ]);
        
        $this->import->incrementGradesImported();
    }
}