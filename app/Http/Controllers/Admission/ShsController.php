<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Admission\ShsAdmission;
use App\Models\Admission\ShsAdmissionScoreTransmutation;
use App\Models\Admission\ShsAdmissionStrands;
use App\Models\Admission\ShsAdmissionSubtest;
use App\Models\Admission\ShsAdmissionSubtestResult;
use App\Models\Elogs;
use App\Models\System\SrmProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = ShsAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $totalApplicants = ShsAdmission::where('is_active', 1)->count();
        $totalApplicantsBySem = ShsAdmission::where('is_active', 1)->whereNotIn('status', [0, 1])->count();
        $totalEnrolledbySem = ShsAdmission::where('is_active', 1)->where('status', 3)->count();
        $totalUnscheduledPaidApplicants = ShsAdmission::where('status', 1)->where('exam_schedule_date', null)->count();

        return view('Admission.SHS.index', compact(
            'applications',
            'totalApplicants',
            'totalApplicantsBySem',
            'totalEnrolledbySem',
            'totalUnscheduledPaidApplicants'
        ));
    }

    public function create()
    {
        $programs = SrmProgram::where('department_id', 31)->where('is_active', 1)->orderBy('name')->get();
        $elog = Elogs::where('is_used', 0)->latest()->first();

        return view('Admission.SHS.create', compact('programs', 'elog'));
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

            'choice_first' => 'required|string|max:255',
            'choice_second' => 'required|string|max:255',
        ]);

        $schoolYear = date('y');
        $semester = '01';

        $lastApp = ShsAdmission::where('application_number', 'like', "SY{$schoolYear}{$semester}%")
                    ->orderBy('id', 'desc')
                    ->first();

        if ($lastApp) {
            $lastNumber = (int) substr($lastApp->application_number, 6, 4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $applicationNumber = "SY{$schoolYear}{$semester}{$newNumber}S";

        ShsAdmission::create($request->all() + [
            'application_number' => $applicationNumber,
            'created_by' => Auth::id(),
            'year' => $schoolYear,
            'term' => $semester,
        ]);

        if ($request->filled('elog_id')) {
            Elogs::where('id', $request->elog_id)->update(['is_used' => 1]);
        }

        return redirect()->route('admission.shs.create')->with([
            'show_success_modal' => true,
            'application_number' => $applicationNumber,
        ]);
    }

    public function totalapplicants(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = ShsAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.SHS.totalapplicants', compact('applications'));
    }

    public function totalenrolled(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = ShsAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1, 2])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.SHS.totalenrolled', compact('applications'));
    }

    public function unsched(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = ShsAdmission::where('is_active', 1)
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

        return view('Admission.SHS.unsched', compact('applications'));
    }

    public function edit(Request $request, $applicationNumber)
    {
        $application = ShsAdmission::with(['collection'])->where('application_number', $applicationNumber)->firstOrFail();

        $programs = SrmProgram::where('department_id', 31)->where('is_active', 1)->orderBy('name')->get();

        $subtests = ShsAdmissionSubtest::where('is_active', 1)
            ->orderBy('name')
            ->get()
            ->groupBy('subtest_group');

        foreach ($subtests as $group => $groupSubtests) {
            foreach ($groupSubtests as $subtest) {
                $existingResult = ShsAdmissionSubtestResult::where('application_number_id', $application->application_number)
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

        return view('Admission.SHS.edit', compact('application', 'programs', 'subtests'));
    }

    public function update(Request $request, $applicationNumber)
    {
        $shs = ShsAdmission::where('application_number', $applicationNumber)->firstOrFail();
        $strands = ShsAdmissionStrands::orderBy('name')->get();

        // Update applicant info including exam_taken toggle
        $shs->update($request->only([
            'lastname', 'firstname', 'middlename', 'suffix', 'gender', 'mobile_no', 'email',
            'dob', 'age', 'nationality', 'religion', 'address', 'zip_code',
            'contact_person', 'contact_number', 'strand', 'school_name', 'school_address',
            'school_zip', 'choice_first', 'choice_second', 'year_level', 'applicant_status',
            'exam_schedule_date', 'certifier_name', 'certifier_designation',
            'verifier_name', 'verifier_designation',
        ]) + [
            'exam_taken' => $request->boolean('exam_taken') ? 1 : 0,
            'updated_by' => Auth::id(),
        ]);

        $totalRawScore = 0;
        $totalApi = 0;
        $countApi = 0;

        // if exam taken == 1 → compute and save results
        if ($shs->exam_taken == 1) {
            $rs = $request->input('rs', []);
            $hg = $request->input('hg', []);
            $subtests = ShsAdmissionSubtest::where('is_active', 1)->get();

            // remove previous results first
            ShsAdmissionSubtestResult::where('application_number_id', $shs->application_number)->delete();

            foreach ($subtests as $subtest) {
                $rawscore = (int) ($rs[$subtest->id] ?? 0);
                $hgValue = $hg[$subtest->id] ?? null;

                $totalRawScore += $rawscore;

                $transmutationRow = ShsAdmissionScoreTransmutation::where('subtest_id', $subtest->id)
                    ->where('rawscore', '<=', $rawscore)
                    ->orderByDesc('rawscore')
                    ->first();

                $transmutedGrade = $transmutationRow?->equivalent ?? 0;

                if ($subtest->subtest_group == 1) {
                    // Weighted computation (60% transmutation + 40% HS grade)
                    $equivalent = (0.6 * $transmutedGrade) + (0.4 * ($hgValue ?? 0));
                } else {
                    $equivalent = $transmutedGrade;
                }

                if ($equivalent != 0) {
                    $totalApi += $equivalent;
                    $countApi++;
                }

                // Save or update subtest result
                ShsAdmissionSubtestResult::updateOrCreate(
                    [
                        'application_number_id' => $shs->application_number,
                        'subtest_id' => $subtest->id,
                    ],
                    [
                        'subtest_name' => $subtest->name,
                        'ts' => $subtest->totalscore,
                        'rawscore' => $rawscore,
                        'transmutation' => $transmutedGrade,
                        'hs_grade' => $hgValue,
                        'api' => $equivalent,
                    ]
                );
            }

            $averageApi = $countApi > 0 ? round($totalApi / $countApi, 2) : 0;

            // Determine remarks based on FIRST CHOICE strand
            $remarks = null;
            $firstChoiceId = $shs->choice_first;

            if ($firstChoiceId == 17) {
                $remarks = $averageApi >= 80 ? 'QUALIFIED' : 'NOT QUALIFIED';
            } elseif (in_array($firstChoiceId, [15, 16, 18])) {
                $remarks = $averageApi >= 76 ? 'QUALIFIED' : 'NOT QUALIFIED';
            } else { // subject for changed ; not computation given
                $remarks = $averageApi >= 76 ? 'QUALIFIED' : 'NOT QUALIFIED';
            }

            $shs->update([
                'total_rs' => $totalRawScore,
                'total_ave_api' => $averageApi,
                'remarks' => $remarks,
            ]);
        }

        ShsAdmission::where('application_number', $applicationNumber)->update(['status' => 2]);

        return redirect()->route('admission.shs.edit', $applicationNumber)
            ->with([
                'show_success_modal' => true,
                'application_number' => $applicationNumber,
            ]);
    }

    public function destroy($applicationNumber)
    {
        $application = ShsAdmission::where('application_number', $applicationNumber)->firstOrFail();

        $application->is_active = 0;
        $application->deleted_by = Auth::id();
        $application->save();

        return redirect()
            ->route('admission.shs.index')
            ->with('success', 'Applicant deactivated successfully.');
    }

    public function view_print($applicationNumber)
    {
        $result = ShsAdmission::with(['collection'])
            ->where('application_number', $applicationNumber)
            ->firstOrFail();

        // Get strand names from database
        $firstChoiceStrand = SrmProgram::where('id', $result->choice_first)->first();

        // Add strand names to result object for easy access in view
        $result->first_choice_name = 'Academic: '.($firstChoiceStrand->name ?? 'N/A').' ('.($firstChoiceStrand->code ?? '').')';

        // Get subtest results for this application
        $subtests = ShsAdmissionSubtestResult::where('application_number_id', $applicationNumber)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->subtest_name,
                    'ts' => $item->ts,
                    'rawscore' => $item->rawscore,
                    'api' => $item->api,
                ];
            });

        return view('Admission.SHS.viewprint', compact('result', 'subtests'));
    }
}
