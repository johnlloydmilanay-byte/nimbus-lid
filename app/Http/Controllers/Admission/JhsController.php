<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Admission\JhsAdmission;
use App\Models\Admission\JhsAdmissionScoreTransmutation;
use App\Models\Admission\JhsAdmissionSubtest;
use App\Models\Admission\JhsAdmissionSubtestPercentage;
use App\Models\Admission\JhsAdmissionSubtestResult;
use App\Models\Elogs;
use App\Models\System\SrmProgram;
use App\Models\System\SysDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JhsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = JhsAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $totalApplicants = JhsAdmission::where('is_active', 1)->count();
        $totalApplicantsBySem = JhsAdmission::where('is_active', 1)->whereNotIn('status', [0, 1])->count();
        $totalEnrolledbySem = JhsAdmission::where('is_active', 1)->where('status', 3)->count();
        $totalUnscheduledPaidApplicants = JhsAdmission::where('status', 1)->where('exam_schedule_date', null)->count();

        return view('Admission.JHS.index', compact(
            'applications',
            'totalApplicants',
            'totalApplicantsBySem',
            'totalEnrolledbySem',
            'totalUnscheduledPaidApplicants'
        ));
    }

    public function create()
    {
        $programs = SrmProgram::with('department')
            ->where('is_active', 1)
            ->whereHas('department', function ($query) {
                $query->where('academicgroup_id', 2);
            })
            ->select('srm_programs.*')
            ->orderBy(
                SysDepartment::select('code')
                    ->whereColumn('sys_departments.id', 'srm_programs.department_id')
                    ->limit(1)
            )
            ->orderBy('srm_programs.code')
            ->get()
            ->map(function ($p) {
                $p->dcode = $p->department?->code ?? '';

                return $p;
            });

        $elog = Elogs::where('is_used', 0)->latest()->first();

        return view('Admission.JHS.create', compact('programs', 'elog'));
    }

    public function searchStudents(Request $request)
    {
        $query = $request->get('query');

        $students = Elogs::where('department_id', 14)
            ->where(function ($q) use ($query) {
                $q->where('last_name', 'LIKE', "%{$query}%")
                ->orWhere('first_name', 'LIKE', "%{$query}%")
                ->orWhere('middle_name', 'LIKE', "%{$query}%");
            })
            ->where('is_used', 0)
            ->select('id', 'last_name', 'first_name', 'middle_name', 'suffix')
            ->limit(10)
            ->get();

        return response()->json($students);
    }

    public function getStudentDetails($id)
    {
        $student = Elogs::where('department_id', 14)
            ->where('id', $id)
            ->where('is_used', 0)
            ->select('id', 'last_name', 'first_name', 'middle_name', 'suffix')
            ->first();

        if ($student) {
            return response()->json($student);
        }

        return response()->json(['error' => 'Student not found'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|string',
            'mobile_no' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'dob' => 'required|date',
            'age' => 'required|integer',
            'nationality' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',

            'school_name' => 'required|string|max:255',
            'lrn' => 'required|string|max:255',
            'school_address' => 'required|string|max:500',
            'school_zip' => 'required|string|max:20',

            'program' => 'required|string|max:255',
        ]);

        $schoolYear = date('y');
        $semester = '01';

        $lastApp = JhsAdmission::where('application_number', 'like', "SY{$schoolYear}{$semester}%")
                    ->orderBy('id', 'desc')
                    ->first();

        if ($lastApp) {
            $lastNumber = (int) substr($lastApp->application_number, 6, 4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $applicationNumber = "SY{$schoolYear}{$semester}{$newNumber}J";

        JhsAdmission::create($request->all() + [
            'application_number' => $applicationNumber,
            'created_by' => Auth::id(),
            'year' => $schoolYear,
            'term' => $semester,
        ]);

        if ($request->filled('elog_id')) {
            Elogs::where('id', $request->elog_id)->update(['is_used' => 1]);
        }

        return redirect()->route('admission.jhs.create')->with([
            'show_success_modal' => true,
            'application_number' => $applicationNumber,
        ]);
    }

    public function totalapplicants(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = JhsAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.JHS.totalapplicants', compact('applications'));
    }

    public function totalenrolled(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = JhsAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1, 2])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.JHS.totalenrolled', compact('applications'));
    }

    public function unsched(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = JhsAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->where('status', 1)
            ->where('exam_schedule_date', null)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.JHS.unsched', compact('applications'));
    }

    public function edit(Request $request, $applicationNumber)
    {
        $application = JhsAdmission::with(['collection'])
            ->where('application_number', $applicationNumber)
            ->firstOrFail();

        $programs = SrmProgram::with('department')
            ->where('is_active', 1)
            ->whereHas('department', function ($query) {
                $query->where('academicgroup_id', 2);
            })
            ->select('srm_programs.*')
            ->orderBy(
                SysDepartment::select('code')
                    ->whereColumn('sys_departments.id', 'srm_programs.department_id')
                    ->limit(1)
            )
            ->orderBy('srm_programs.code')
            ->get()
            ->map(function ($p) {
                $p->dcode = $p->department?->code ?? '';

                return $p;
            });

        // Fetch subtests grouped
        $subtests = JhsAdmissionSubtest::where('is_active', 1)
            ->orderBy('name')
            ->get()
            ->groupBy('subtest_group');

        // Attach existing results to each subtest
        foreach ($subtests as $group => $groupSubtests) {
            foreach ($groupSubtests as $subtest) {
                $existingResult = JhsAdmissionSubtestResult::where('application_number_id', $application->application_number)
                    ->where('subtest_id', $subtest->id)
                    ->first();

                $subtest->result = (object) [
                    'rs' => $existingResult->rawscore ?? null,
                    'hg' => $existingResult->hs_grade ?? null,
                    'trans' => $existingResult->transmutation ?? null,
                    'api' => $existingResult->api ?? null,
                ];
            }
        }

        return view('Admission.JHS.edit', compact('application', 'programs', 'subtests'));
    }

    public function update(Request $request, $applicationNumber)
    {
        $jhs = JhsAdmission::where('application_number', $applicationNumber)->firstOrFail();

        // Prepare base data for update
        $data = $request->only([
            'lastname', 'firstname', 'middlename', 'suffix', 'gender',
            'mobile_no', 'email', 'dob', 'age', 'nationality', 'religion',
            'address', 'zip_code', 'contact_person', 'contact_number',
            'strand', 'school_name', 'school_address', 'school_zip',
            'program', 'year_level', 'applicant_status', 'exam_schedule_date',
        ]);

        // Keep existing certifier/verifier fields if not provided
        $data['certifier_name'] = $request->filled('certifier_name')
            ? $request->input('certifier_name')
            : $jhs->certifier_name;

        $data['certifier_designation'] = $request->filled('certifier_designation')
            ? $request->input('certifier_designation')
            : $jhs->certifier_designation;

        $data['verifier_name'] = $request->filled('verifier_name')
            ? $request->input('verifier_name')
            : $jhs->verifier_name;

        $data['verifier_designation'] = $request->filled('verifier_designation')
            ? $request->input('verifier_designation')
            : $jhs->verifier_designation;

        // Exam taken logic
        $data['exam_taken'] = $request->boolean('exam_taken') ? 1 : 0;
        $data['updated_by'] = Auth::id();

        // Update main applicant info
        $jhs->update($data);

        // Only compute subtest results if exam is taken
        if ($data['exam_taken'] == 1) {
            $totalRating = 0;

            $rs = $request->input('rs', []);
            $hg = $request->input('hg', []);
            $subtests = JhsAdmissionSubtest::get();

            // Remove old results before saving new ones
            JhsAdmissionSubtestResult::where('application_number_id', $jhs->application_number)->delete();

            foreach ($subtests as $subtest) {
                $rawScore = $rs[$subtest->id] ?? null;
                $hgScore = $hg[$subtest->id] ?? null;

                $percentage = JhsAdmissionSubtestPercentage::where('subtest_id', $subtest->id)
                    ->where('program_id', $jhs->program)
                    ->value('percentage') ?? 0;

                $transmutation = JhsAdmissionScoreTransmutation::where('subtest_id', $subtest->id)
                    ->where('rawscore', $rawScore)
                    ->first();

                $equivalent = null;
                if ($transmutation) {
                    if ($subtest->subtest_group == 1) {
                        $equivalent = (0.6 * $transmutation->equivalent) + (0.4 * ($hgScore ?? 0));
                    } else {
                        $equivalent = $transmutation->equivalent;
                    }
                }

                $api = $equivalent ? ($equivalent * $percentage) : 0;
                $totalRating += $api;

                JhsAdmissionSubtestResult::updateOrCreate(
                    [
                        'application_number_id' => $applicationNumber,
                        'subtest_id' => $subtest->id,
                    ],
                    [
                        'subtest_name' => $subtest->name,
                        'ts' => $subtest->totalscore,
                        'rawscore' => $rawScore,
                        'transmutation' => $transmutation->equivalent ?? null,
                        'hs_grade' => $hgScore,
                        'api' => $equivalent,
                        'percentage' => $percentage,
                        'diq' => $transmutation->diq ?? null,
                        'description' => $transmutation->description ?? null,
                        'equivalent' => $api,
                    ]
                );
            }

            $roundedTotal = round($totalRating, 2);
            $programId = $jhs->program;
            $remarks = null;

            switch ($programId) {
                case 3:
                    if ($roundedTotal >= 80) {
                        $remarks = 'QUALIFIED';
                    } elseif ($roundedTotal >= 78 && $roundedTotal <= 79) {
                        $remarks = 'Subject for Entry Requirement';
                    } else {
                        $remarks = 'Redirected to Special Program in the Arts (must undergo audition) or General Curriculum';
                    }
                    break;

                case 4:
                    if ($roundedTotal >= 78) {
                        $remarks = 'QUALIFIED';
                    } else {
                        $remarks = 'Subject for Entry Requirement';
                    }
                    break;

                case 50:
                    if ($roundedTotal >= 77) {
                        $remarks = 'QUALIFIED';
                    } elseif ($roundedTotal >= 75 && $roundedTotal <= 76) {
                        $remarks = 'Subject for Entry Requirement';
                    } else {
                        $remarks = null;
                    }
                    break;

                default:
                    $remarks = null;
            }

            $jhs->update([
                'total_rating' => $roundedTotal,
                'remarks' => $remarks,
            ]);
        }

        JhsAdmission::where('application_number', $applicationNumber)->update(['status' => 2]);

        return redirect()->route('admission.jhs.edit', $applicationNumber)
            ->with([
                'show_success_modal' => true,
                'application_number' => $applicationNumber,
            ]);
    }

    public function destroy($applicationNumber)
    {
        $application = JhsAdmission::where('application_number', $applicationNumber)->firstOrFail();

        $application->is_active = 0;
        $application->deleted_by = Auth::id();
        $application->save();

        return redirect()
            ->route('admission.jhs.index')
            ->with('success', 'Applicant deactivated successfully.');
    }

    public function view_print($applicationNumber)
    {
        $result = JhsAdmission::with(['collection'])->where('application_number', $applicationNumber)->firstOrFail();
        $program = SrmProgram::with('department')->where('id', $result->program)->first();

        if ($program && $program->department) {
            $result->program_name = $program->department->code.' - '.$program->name;
        } else {
            $result->program_name = $result->choice_first ?? 'N/A';
        }

        $allSubtests = JhsAdmissionSubtestResult::where('application_number_id', $applicationNumber)->get();
        $subtestInfo = JhsAdmissionSubtest::get()->keyBy('id');

        $academicSubtests = [];
        $iqSubtests = [];
        $interviewSubtest = null;

        foreach ($allSubtests as $subtest) {
            $info = $subtestInfo->get($subtest->subtest_id);

            if (! $info) {
                continue;
            }

            if ($info->id == 6) {
                $interviewSubtest = (object) [
                    'name' => $subtest->subtest_name,
                    'rawscore' => $subtest->rawscore,
                    'transmutation' => $subtest->transmutation,
                    'equivalent' => $subtest->equivalent,
                ];
            } elseif (isset($info->subtest_group)) {
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

                if ($info->subtest_group == 1) {
                    $academicSubtests[] = $subtestData;
                } elseif ($info->subtest_group == 2) {
                    $iqSubtests[] = $subtestData;
                }
            }
        }

        return view('Admission.JHS.viewprint', compact(
            'result',
            'academicSubtests',
            'iqSubtests',
            'interviewSubtest'
        ));
    }
}
