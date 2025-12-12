<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\OnlineAdmissionForm;
use App\Models\Registrar\RequirementsCollege;
use App\Models\Registrar\RequirementsJhs;
use App\Models\Registrar\RequirementsPse;
use App\Models\Registrar\RequirementsShs;
use App\Models\System\SrmProgram;
use App\Models\System\SrmStudents;
use App\Models\System\SysDepartment;
use App\Models\System\SysProvinces;
use App\Models\System\SysTowns;
use App\Models\System\SysYearLevels;
use App\Models\System\SysYearLevelsDetails;
use App\Models\System\SysStudentStatus;
use App\Models\System\SrmCurriculumSubject;
use App\Models\System\SrmEnrolledSubjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentManagementController extends Controller
{
    public function index(Request $request)
    {
        $auth = auth()->user();

        if ($auth && $auth->usertype != 2) {
            abort(403, 'YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE.');
        }

        $search = $request->input('search');
        $students = collect(); // Empty collection by default

        // Only search when input is provided
        if (! empty($search)) {
            $students = SrmStudents::query()
                ->where(function ($q) use ($search) {
                    $q->where('lastname', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('application_number', 'like', "%{$search}%");
                })
                ->orderBy('lastname')
                ->paginate(10);
        }

        $totalStudents = SrmStudents::count();

        return view('Registrar.StudentManagement.index', compact('auth', 'totalStudents', 'students', 'search'));
    }

    public function create()
    {
        $department = SysDepartment::where('is_academic',1)->orderBy('code')->where('is_active', 1)->get();
        $provinces = SysProvinces::all();
        $towns = SysTowns::all();
        $application = null;

        $studentstatus = SysStudentStatus::where('is_active', 1)->get();

        $yearLevelDetails = SysYearLevelsDetails::orderBy('id')->get();

        return view('Registrar.StudentManagement.create', compact('department', 'provinces', 'towns', 'application', 'studentstatus', 'yearLevelDetails'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $students = OnlineAdmissionForm::where('application_number', 'LIKE', "%{$query}%")->orWhere('lastname', 'LIKE', "%{$query}%")->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'No matching records found.'], 404);
        }

        return response()->json($students);
    }

    public function getProgramsByDepartment($departmentId)
    {
        $programs = SrmProgram::where('department_id', $departmentId)->where('is_active', 1)->get(['id', 'name', 'code']);

        return response()->json($programs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_number' => 'required|string|max:20',
            'lastname' => 'required|string|max:50',
            'firstname' => 'required|string|max:50',
            'middlename' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:100',
            'mobile_no' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $createdBy = auth()->user()->user_id;

        // Check if student exists, otherwise create a new instance
        $student = SrmStudents::firstOrNew(
            ['application_number' => $request->application_number]
        );

        // If creating new, set created_by and created_at
        if (! $student->exists) {
            $student->created_by = $createdBy;
            $student->created_at = now();
        }

        // Fill/update all other fields
        $student->fill($request->only([
            'lastname', 'firstname', 'middlename', 'department_id', 'program_id',
            'studentstatus_id', 'year_level_id', 'year_entry', 'gender', 'mobile_no',
            'email', 'no_of_siblings', 'dob', 'birthplace', 'religion', 'nationality',
            'province_id', 'city_id', 'barangay', 'staying_in', 'current_province_id',
            'current_city_id', 'current_barangay', 'elem_school_name', 'elem_address',
            'elem_school_year_attended', 'jhs_name', 'jhs_address', 'jhs_year_attended',
            'awards', 'organization', 'position', 'father_name', 'father_occupation',
            'father_age', 'father_education', 'father_mobile_no', 'father_status',
            'father_placework', 'father_ofw_status', 'mother_name', 'mother_occupation',
            'mother_age', 'mother_education', 'mother_mobile_no', 'mother_status',
            'mother_placework', 'mother_ofw_status', 'guardian_name', 'guardian_occupation',
            'guardian_number', 'parents_marital_status', 'monthly_family_income',
            'family_living_arrangement', 'others_specify', 'is_pwd', 'is_pwd_yes',
            'is_scholar', 'is_scholar_type', 'is_scholar_yes_others'
        ]));

        $student->save();

        // Auto-enroll subjects for Term 1 only
        $termId = 1;

        if ($student->year_level_id) {

            // Fetch all active curriculum subjects for Term 1 and this student's year level
            $currSubjects = SrmCurriculumSubject::where('year_level_details_id', $student->year_level_id)
                ->where('term_id', $termId)
                ->where('is_active', 1)
                ->get();

            // Debug: check if subjects exist
            if ($currSubjects->isEmpty()) {
                \Log::info('No curriculum subjects found for auto-enroll', [
                    'year_level_id' => $student->year_level_id,
                    'term_id' => $termId
                ]);
            }

            foreach ($currSubjects as $currSubj) {
                SrmEnrolledSubjects::updateOrCreate(
                    [
                        'application_number' => $student->application_number,
                        'subject_id' => $currSubj->subject_id,
                        'term_id' => $currSubj->term_id,
                    ],
                    [
                        'year' => $student->year_entry,
                        'student_number' => $student->student_number ?? null,
                        'is_active' => 1,
                        'created_by' => auth()->user()->user_id,
                    ]
                );
            }
        }

        return redirect()->route('registrar.studentmanagement.edit', $student->application_number)->with([
            'show_success_save_modal' => true,
        ]);

    }

    public function edit($applicationNumber)
    {
        $auth = auth()->user();

        if ($auth && $auth->usertype != 2) {
            abort(403, 'YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE.');
        }

        $student = SrmStudents::where('application_number', $applicationNumber)->firstOrFail();
        $department = SysDepartment::whereIn('academicgroup_id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->where('is_active', 1)->get();
        $programs = SrmProgram::all();
        $studentstatus = SysStudentStatus::where('is_active', 1)->get();

        $defaultDept = $department->first();

        $yearLevelDetails = SysYearLevelsDetails::whereHas('yearLevel', function ($q) use ($defaultDept) {
            $q->where('academic_group_id', $defaultDept->academicgroup_id)
            ->where('is_active', 1);
        });

        if(isset($student) && $student->year_level_id) {
            $yearLevelDetails->orWhere('id', $student->year_level_id);
        }

        $yearLevelDetails = $yearLevelDetails->orderBy('order')->get();

        $provinces = SysProvinces::all();
        $towns = SysTowns::all();
        $collegeRequirements = null;
        if (str_ends_with($applicationNumber, 'C')) {
            $collegeRequirements = RequirementsCollege::firstOrCreate(
                ['application_number' => $applicationNumber],
                [
                    'has_college_result' => 0,
                    'has_college_report_card' => 0,
                    'has_college_good_moral' => 0,
                    'has_college_psa' => 0,
                    'has_college_pic' => 0,
                    'has_college_envelope' => 0,
                ]
            );
        }
        $shsRequirements = null;
        if (str_ends_with($applicationNumber, 'S')) {
            $shsRequirements = RequirementsShs::firstOrCreate(
                ['application_number' => $applicationNumber],
                [
                    'has_shs_result' => 0,
                    'has_shs_report_card' => 0,
                    'has_shs_good_moral' => 0,
                    'has_shs_psa' => 0,
                    'has_shs_completion_cert' => 0,
                    'has_shs_pic' => 0,
                    'has_shs_envelope' => 0,
                ]
            );
        }
        $jhsRequirements = null;
        if (str_ends_with($applicationNumber, 'J')) {
            $jhsRequirements = RequirementsJhs::firstOrCreate(
                ['application_number' => $applicationNumber],
                [
                    'has_jhs_result' => 0,
                    'has_jhs_report_card' => 0,
                    'has_jhs_good_moral' => 0,
                    'has_jhs_psa' => 0,
                    'has_jhs_pic' => 0,
                    'has_jhs_income' => 0,
                    'has_jhs_envelope' => 0,
                    'has_jhs_folder' => 0,
                ]
            );
        }
        $pseRequirements = null;
        if (str_ends_with($applicationNumber, 'P')) {
            $pseRequirements = RequirementsPse::firstOrCreate(
                ['application_number' => $applicationNumber],
                [
                    'has_pse_result' => 0,
                    'has_pse_report_card' => 0,
                    'has_pse_good_moral' => 0,
                    'has_pse_psa' => 0,
                    'has_pse_pic' => 0,
                    'has_pse_pic1' => 0,
                    'has_pse_medcert' => 0,
                    'has_pse_envelope' => 0,
                ]
            );
        }

        return view('Registrar.StudentManagement.edit', compact('auth', 'student', 'department', 'programs', 'studentstatus', 'yearLevelDetails', 'provinces', 'towns', 'collegeRequirements', 'shsRequirements', 'jhsRequirements', 'pseRequirements'));
    }

    public function update(Request $request, $applicationNumber)
    {
        $student = SrmStudents::where('application_number', $applicationNumber)->firstOrFail();

        $validated = $request->validate([

            // Basic Information
            'lastname' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:sys_departments,id',
            'program_id' => 'nullable|exists:srm_programs,id',
            'gender' => 'nullable|in:Male,Female',
            'mobile_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'no_of_siblings' => 'nullable|integer|min:0',
            'dob' => 'nullable|date',
            'birthplace' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',

            // Permanent address
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'staying_in' => 'nullable|string|max:255',

            // Current address
            'current_province' => 'nullable|string|max:255',
            'current_city' => 'nullable|string|max:255',
            'current_barangay' => 'nullable|string|max:255',

            // Family background
            'father_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'father_age' => 'nullable|integer|min:0',
            'father_education' => 'nullable|string|max:255',
            'father_mobile_no' => 'nullable|string|max:20',
            'father_status' => 'nullable|string|max:50',
            'father_placework' => 'nullable|string|max:255',
            'father_ofw_status' => 'nullable|string|max:50',

            'mother_name' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_age' => 'nullable|integer|min:0',
            'mother_education' => 'nullable|string|max:255',
            'mother_mobile_no' => 'nullable|string|max:20',
            'mother_status' => 'nullable|string|max:50',
            'mother_placework' => 'nullable|string|max:255',
            'mother_ofw_status' => 'nullable|string|max:50',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_occupation' => 'nullable|string|max:255',
            'guardian_number' => 'nullable|string|max:20',

            'parents_marital_status' => 'nullable|string|max:255',
            'monthly_family_income' => 'nullable|string|max:255',
            'family_living_arrangement' => 'nullable|string|max:255',

            // Other Info
            'is_pwd' => 'nullable|boolean',
            'is_pwd_yes' => 'nullable|string|max:255',
            'is_scholar' => 'nullable|boolean',
            'is_scholar_type' => 'nullable|string|max:255',
            'is_scholar_yes_others' => 'nullable|string|max:255',
        ]);

        // Handle Family Living Arrangement
        if (isset($validated['family_living_arrangement'])) {
            $arrangement = $validated['family_living_arrangement'];

            if (in_array($arrangement, ['Living with relatives (please specify)', 'Living with others (please specify)'])) {
                $validated['others_specify'] = $request->input('others_specify', null);
            } else {
                $validated['others_specify'] = null;
            }
        }

        // Handle is_pwd
        if (isset($validated['is_pwd']) && $validated['is_pwd'] === 0) {
            $validated['is_pwd'] = null;
            $validated['is_pwd_yes'] = null;
        }

        // Handle is_scholar
        if (isset($validated['is_scholar'])) {
            $isScholar = (int) $validated['is_scholar'];
            if ($isScholar === 0) {
                $validated['is_scholar_type'] = null;
                $validated['is_scholar_yes_others'] = null;
                $validated['is_scholar'] = 0;
            } elseif ($isScholar === 1) {
                if (isset($validated['is_scholar_type']) && $validated['is_scholar_type'] !== 'Others') {
                    $validated['is_scholar_yes_others'] = null;
                }
            }
        }

        $validated['updated_by'] = auth()->user()->user_id ?? null;
        $student->update($validated);

        return redirect()->route('registrar.studentmanagement.edit', $applicationNumber)
            ->with('show_success_save_modal', true);
    }

    public function collegeRequirementUpdate(Request $request, $applicationNumber)
    {
        $collegeRequirements = RequirementsCollege::where('application_number', $applicationNumber)->firstOrFail();
        $user = auth()->user();

        $fields = [
            'college_result' => 'has_college_result',
            'college_report_card' => 'has_college_report_card',
            'college_good_moral' => 'has_college_good_moral',
            'college_psa' => 'has_college_psa',
            'college_pic' => 'has_college_pic',
            'college_envelope' => 'has_college_envelope',
        ];

        foreach ($fields as $key => $column) {
            if ($request->has($column)) {
                $newValue = $request->input($column);
                $oldValue = $collegeRequirements->{$column};

                // Update the status
                $collegeRequirements->{$column} = $newValue;

                $signedByField = "{$key}_signed_by";
                $signedAtField = "{$key}_signed_at";
                $updatedByField = "{$key}_updated_by";
                $updatedAtField = "{$key}_updated_at";

                if ($newValue == 1) {
                    // If it was previously 0 or signed_by is empty, update signed info
                    if (! $collegeRequirements->{$signedByField} || $oldValue == 0) {
                        $collegeRequirements->{$signedByField} = $user->user_id;
                        $collegeRequirements->{$signedAtField} = now();
                    }
                } else {
                    // When status == pending, log the update
                    $collegeRequirements->{$updatedByField} = $user->user_id;
                    $collegeRequirements->{$updatedAtField} = now();
                }
            }
        }

        $collegeRequirements->save();

        return redirect()
            ->back()
            ->with('show_success_save_modal', true);
    }

    public function shsRequirementUpdate(Request $request, $applicationNumber)
    {
        $shsRequirements = RequirementsShs::where('application_number', $applicationNumber)->firstOrFail();
        $user = auth()->user();

        // Map form keys to actual SHS columns
        $fields = [
            'shs_result' => 'has_shs_result',
            'shs_report_card' => 'has_shs_report_card',
            'shs_good_moral' => 'has_shs_good_moral',
            'shs_psa' => 'has_shs_psa',
            'shs_completion_cert' => 'has_shs_completion_cert',
            'shs_pic' => 'has_shs_pic',
            'shs_esc' => 'has_shs_esc',
            'shs_envelope' => 'has_shs_envelope',
            'shs_folder' => 'has_shs_folder',
        ];

        foreach ($fields as $key => $column) {
            if ($request->has($column)) {
                $newValue = $request->input($column);
                $oldValue = $shsRequirements->{$column};

                // Update the status
                $shsRequirements->{$column} = $newValue;

                $signedByField = "{$key}_signed_by";
                $signedAtField = "{$key}_signed_at";
                $updatedByField = "{$key}_updated_by";
                $updatedAtField = "{$key}_updated_at";

                if ($newValue == 1) {
                    // If previously 0 or signed_by is empty, update signed info
                    if (! $shsRequirements->{$signedByField} || $oldValue == 0) {
                        $shsRequirements->{$signedByField} = $user->user_id;
                        $shsRequirements->{$signedAtField} = now();
                    }
                } else {
                    // When status is pending, log the update
                    $shsRequirements->{$updatedByField} = $user->user_id;
                    $shsRequirements->{$updatedAtField} = now();
                }
            }
        }

        $shsRequirements->save();

        return redirect()
            ->back()
            ->with('show_success_save_modal', true);
    }

    public function jhsRequirementUpdate(Request $request, $applicationNumber)
    {
        $shsRequirements = RequirementsJhs::where('application_number', $applicationNumber)->firstOrFail();
        $user = auth()->user();

        // Map form keys to actual JHS columns
        $fields = [
            'jhs_result' => 'has_jhs_result',
            'jhs_report_card' => 'has_jhs_report_card',
            'jhs_good_moral' => 'has_jhs_good_moral',
            'jhs_psa' => 'has_jhs_psa',
            'jhs_pic' => 'has_jhs_pic',
            'jhs_income' => 'has_jhs_income',
            'jhs_envelope' => 'has_jhs_envelope',
            'jhs_folder' => 'has_jhs_folder',
        ];

        foreach ($fields as $key => $column) {
            if ($request->has($column)) {
                $newValue = $request->input($column);
                $oldValue = $shsRequirements->{$column};

                // Update the status
                $shsRequirements->{$column} = $newValue;

                $signedByField = "{$key}_signed_by";
                $signedAtField = "{$key}_signed_at";
                $updatedByField = "{$key}_updated_by";
                $updatedAtField = "{$key}_updated_at";

                if ($newValue == 1) {
                    // If previously 0 or signed_by is empty, update signed info
                    if (! $shsRequirements->{$signedByField} || $oldValue == 0) {
                        $shsRequirements->{$signedByField} = $user->user_id;
                        $shsRequirements->{$signedAtField} = now();
                    }
                } else {
                    // When status is pending, log the update
                    $shsRequirements->{$updatedByField} = $user->user_id;
                    $shsRequirements->{$updatedAtField} = now();
                }
            }
        }

        $shsRequirements->save();

        return redirect()
            ->back()
            ->with('show_success_save_modal', true);
    }

    public function pseRequirementUpdate(Request $request, $applicationNumber)
    {
        $pseRequirements = RequirementsPse::where('application_number', $applicationNumber)->firstOrFail();
        $user = auth()->user();

        // Map form keys to actual JHS columns
        $fields = [
            'pse_result' => 'has_pse_result',
            'pse_report_card' => 'has_pse_report_card',
            'pse_good_moral' => 'has_pse_good_moral',
            'pse_psa' => 'has_pse_psa',
            'pse_pic' => 'has_pse_pic',
            'pse_pic1' => 'has_pse_pic1',
            'pse_medcert' => 'has_pse_medcert',
            'pse_envelope' => 'has_pse_envelope',
        ];

        foreach ($fields as $key => $column) {
            if ($request->has($column)) {
                $newValue = $request->input($column);
                $oldValue = $pseRequirements->{$column};

                // Update the status
                $pseRequirements->{$column} = $newValue;

                $signedByField = "{$key}_signed_by";
                $signedAtField = "{$key}_signed_at";
                $updatedByField = "{$key}_updated_by";
                $updatedAtField = "{$key}_updated_at";

                if ($newValue == 1) {
                    // If previously 0 or signed_by is empty, update signed info
                    if (! $pseRequirements->{$signedByField} || $oldValue == 0) {
                        $pseRequirements->{$signedByField} = $user->user_id;
                        $pseRequirements->{$signedAtField} = now();
                    }
                } else {
                    // When status is pending, log the update
                    $pseRequirements->{$updatedByField} = $user->user_id;
                    $pseRequirements->{$updatedAtField} = now();
                }
            }
        }

        $pseRequirements->save();

        return redirect()
            ->back()
            ->with('show_success_save_modal', true);
    }
}
