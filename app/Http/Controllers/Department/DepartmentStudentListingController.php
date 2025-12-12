<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Admission\CollegeAdmission;
use App\Models\Admission\CollegeAdmissionShsPrograms;
use App\Models\Admission\CollegeSubtestResult;
use App\Models\Admission\JhsAdmission;
use App\Models\Admission\JhsAdmissionSubtest;
use App\Models\Admission\JhsAdmissionSubtestResult;
use App\Models\Admission\PseAdmission;
use App\Models\Admission\PseElemSubtest;
use App\Models\Admission\PseElemSubtestResult;
use App\Models\Admission\ShsAdmission;
use App\Models\Admission\ShsAdmissionSubtest;
use App\Models\Admission\ShsAdmissionSubtestResult;
use App\Models\System\SrmProgram;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class DepartmentStudentListingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $deptId = Auth::user()->department_id;

        if (! $deptId) {
            return view('Department.StudentListing.index', [
                'qualifiedStudents' => collect(),
                'notQualifiedStudents' => collect(),
                'search' => $search,
                'department' => null,
            ]);
        }

        $programIds = SrmProgram::where('department_id', $deptId)->pluck('id')->toArray();

        $getProgram = function ($programId) {
            return SrmProgram::join('sys_departments', 'srm_programs.department_id', '=', 'sys_departments.id')
                ->where('srm_programs.id', $programId)
                ->select('srm_programs.*', 'sys_departments.name as dname')
                ->first();
        };

        $fetchStudents = function ($model, $programField) use ($search, $programIds, $getProgram) {
            return $model::with('department')
                ->when($search, fn ($q) => $q->where(function ($query) use ($search) {
                    $query->where('lastname', 'like', "%{$search}%")
                          ->orWhere('firstname', 'like', "%{$search}%")
                          ->orWhere('application_number', 'like', "%{$search}%");
                }))
                ->whereIn($programField, $programIds)
                ->where('status', '!=', 0)
                ->get()
                ->map(fn ($student) => tap($student, fn ($s) => $s->program_info = $getProgram($s->{$programField})));
        };

        $college = $fetchStudents(CollegeAdmission::class, 'choice_first');
        $shs = $fetchStudents(ShsAdmission::class, 'choice_first');
        $jhs = $fetchStudents(JhsAdmission::class, 'program');
        $pse = $fetchStudents(PseAdmission::class, 'program');

        $allStudents = collect()
            ->merge($college)
            ->merge($shs)
            ->merge($jhs)
            ->merge($pse);

        $qualifiedCollection = $allStudents->filter(fn ($s) => strtoupper(trim($s->remarks ?? '')) === 'QUALIFIED')->values();
        $notQualifiedCollection = $allStudents->filter(fn ($s) => strtoupper(trim($s->remarks ?? '')) !== 'QUALIFIED')->values();

        // Pagination parameters
        $perPage = 5;

        // Get page numbers from request (use custom names to paginate both independently)
        $qualifiedPage = (int) $request->input('qualified_page', 1);
        $notQualifiedPage = (int) $request->input('notqualified_page', 1);

        // Slice collections for current page
        $qualifiedSlice = $qualifiedCollection->forPage($qualifiedPage, $perPage)->values();
        $notQualifiedSlice = $notQualifiedCollection->forPage($notQualifiedPage, $perPage)->values();

        // Build LengthAwarePaginator for each (include path and query so links keep other params)
        $qualifiedPaginated = new LengthAwarePaginator(
            $qualifiedSlice,
            $qualifiedCollection->count(),
            $perPage,
            $qualifiedPage,
            [
                'pageName' => 'qualified_page',
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $notQualifiedPaginated = new LengthAwarePaginator(
            $notQualifiedSlice,
            $notQualifiedCollection->count(),
            $perPage,
            $notQualifiedPage,
            [
                'pageName' => 'notqualified_page',
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('Department.StudentListing.index', [
            'qualifiedStudents' => $qualifiedPaginated,
            'notQualifiedStudents' => $notQualifiedPaginated,
            'search' => $search,
            'department' => $deptId,
        ]);
    }

    public function view($applicationNumber)
    {
        // Try to find the student across all levels
        $student =
            CollegeAdmission::with('collection')->where('application_number', $applicationNumber)->first()
            ?? ShsAdmission::with('collection')->where('application_number', $applicationNumber)->first()
            ?? JhsAdmission::with('collection')->where('application_number', $applicationNumber)->first()
            ?? PseAdmission::with('collection')->where('application_number', $applicationNumber)->first();

        if (! $student) {
            abort(404, 'Student not found.');
        }

        // ========== COLLEGE VIEW LOGIC ==========
        if ($student instanceof \App\Models\Admission\CollegeAdmission) {
            $student->strand = CollegeAdmissionShsPrograms::find($student->strand_id);

            // Helper to get program with department name
            function getChoiceProgram($programId)
            {
                return SrmProgram::join('sys_departments', 'srm_programs.department_id', '=', 'sys_departments.id')
                    ->where('srm_programs.id', $programId)
                    ->select('srm_programs.*', 'sys_departments.name as dname')
                    ->first();
            }

            $student->choice_first_program = getChoiceProgram($student->choice_first);
            $student->choice_second_program = getChoiceProgram($student->choice_second);
            $student->choice_third_program = getChoiceProgram($student->choice_third);

            $subtests = CollegeSubtestResult::where('application_number_id', $student->application_number)->get();

            return view('Department.StudentListing.view', compact('student', 'subtests'));
        }

        // ========== SHS VIEW LOGIC ==========
        if ($student instanceof \App\Models\Admission\ShsAdmission) {
            $firstChoiceStrand = SrmProgram::find($student->choice_first);
            $student->first_choice_name = 'Academic: '.($firstChoiceStrand->name ?? 'N/A').' ('.($firstChoiceStrand->code ?? '').')';

            $academicSubtests = [];
            $iqSubtests = [];
            $interviewSubtest = null;

            $allSubtests = ShsAdmissionSubtestResult::where('application_number_id', $student->application_number)->get();
            $subtestInfo = ShsAdmissionSubtest::get()->keyBy('id');

            foreach ($allSubtests as $subtest) {
                $info = $subtestInfo->get($subtest->subtest_id);
                if (! $info) {
                    continue;
                }

                $subtestData = (object) [
                    'name' => $subtest->subtest_name,
                    'ts' => $subtest->ts ?? null,
                    'rawscore' => $subtest->rawscore ?? null,
                    'api' => $subtest->api ?? null,
                    'equivalent' => $subtest->equivalent ?? null,
                    'diq' => $subtest->diq ?? null,
                    'description' => $subtest->description ?? null,
                    'transmutation' => $subtest->transmutation ?? null,
                ];

                if ($info->id == 6) {
                    $interviewSubtest = $subtestData;
                } elseif ($info->subtest_group == 1) {
                    $academicSubtests[] = $subtestData;
                } elseif ($info->subtest_group == 2) {
                    $iqSubtests[] = $subtestData;
                }
            }

            return view('Department.StudentListing.view', compact(
                'student',
                'academicSubtests',
                'iqSubtests',
                'interviewSubtest'
            ));
        }

        // ========== JHS VIEW LOGIC ==========
        if ($student instanceof \App\Models\Admission\JhsAdmission) {
            $program = SrmProgram::with('department')->where('id', $student->program)->first();
            $student->program_name = $program && $program->department
                ? $program->department->code.' - '.$program->name
                : ($student->choice_first ?? 'N/A');

            $academicSubtests = [];
            $iqSubtests = [];
            $interviewSubtest = null;

            $allSubtests = JhsAdmissionSubtestResult::where('application_number_id', $student->application_number)->get();
            $subtestInfo = JhsAdmissionSubtest::get()->keyBy('id');

            foreach ($allSubtests as $subtest) {
                $info = $subtestInfo->get($subtest->subtest_id);
                if (! $info) {
                    continue;
                }

                $subtestData = (object) [
                    'name' => $subtest->subtest_name,
                    'ts' => $subtest->ts,
                    'rawscore' => $subtest->rawscore,
                    'api' => $subtest->api,
                    'equivalent' => $subtest->equivalent,
                    'diq' => $subtest->diq,
                    'description' => $subtest->description,
                    'percentage' => $subtest->percentage,
                    'transmutation' => $subtest->transmutation,
                ];

                if ($info->id == 6) {
                    $interviewSubtest = $subtestData;
                } elseif ($info->subtest_group == 1) {
                    $academicSubtests[] = $subtestData;
                } elseif ($info->subtest_group == 2) {
                    $iqSubtests[] = $subtestData;
                }
            }

            return view('Department.StudentListing.view', compact(
                'student',
                'academicSubtests',
                'iqSubtests',
                'interviewSubtest'
            ));
        }

        // ========== PSE VIEW LOGIC ==========
        if ($student instanceof \App\Models\Admission\PseAdmission) {
            $subtests = collect();

            if ($student->applicant_status === 'Transferee (Grade 2-6)') {
                // Transferees: type 2 subtests
                $subtestData = PseElemSubtest::where('is_active', 1)
                    ->where('type', 2)
                    ->get();

                foreach ($subtestData as $subtest) {
                    $existingResult = PseElemSubtestResult::where('application_number_id', $student->application_number)
                        ->where('subtest_id', $subtest->id)
                        ->first();

                    if ($existingResult) {
                        $subtests->push((object) [
                            'name' => $subtest->name,
                            'ts' => $subtest->total_score ?? $existingResult->ts,
                            'rawscore' => $existingResult->rs,
                            'equivalent' => $existingResult->percentage,
                        ]);
                    }
                }
            } elseif ($student->applicant_status === 'Incoming Grade 1') {
                // Incoming Grade 1: type 1 subtests
                $subtestData = PseElemSubtest::where('is_active', 1)
                    ->where('type', 1)
                    ->get();

                foreach ($subtestData as $subtest) {
                    $existingResult = PseElemSubtestResult::where('application_number_id', $student->application_number)
                        ->where('subtest_id', $subtest->id)
                        ->first();

                    if ($existingResult) {
                        $subtests->push((object) [
                            'name' => $subtest->name,
                            'ts' => $subtest->total_score ?? $existingResult->ts,
                            'rawscore' => $existingResult->rs,
                            'equivalent' => $existingResult->percentage,
                        ]);
                    }
                }
            }

            // Nursery, Prep, Kinder — no subtests
            return view('Department.StudentListing.view', compact('student', 'subtests'));
        }
    }

    public function updateRemarks(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|in:QUALIFIED,NOT QUALIFIED',
        ]);

        // Try to find the student in all models
        $student = CollegeAdmission::find($id)
            ?? ShsAdmission::find($id)
            ?? JhsAdmission::find($id)
            ?? PseAdmission::find($id);

        if (! $student) {
            return redirect()->back()->with('error', 'Student not found!');
        }

        $student->remarks = $request->remarks;
        $student->save();

        return redirect()->back()->with('success', 'Remarks updated successfully!');
    }
}
