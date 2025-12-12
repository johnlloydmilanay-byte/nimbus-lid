<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Admission\PseAdmission;
use App\Models\Admission\PseElemSubtest;
use App\Models\Admission\PseElemSubtestG1Rating;
use App\Models\Admission\PseElemSubtestResult;
use App\Models\Elogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PSEController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = PseAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $totalApplicants = PseAdmission::where('is_active', 1)->count();
        $totalApplicantsBySem = PseAdmission::where('is_active', 1)->whereNotIn('status', [0, 1])->count();
        $totalEnrolledbySem = PseAdmission::where('is_active', 1)->where('status', 3)->count();
        $totalUnscheduledPaidApplicants = PseAdmission::where('status', 1)->where('exam_schedule_date', null)->count();

        return view('Admission.PSE.index', compact(
            'applications',
            'totalApplicants',
            'totalApplicantsBySem',
            'totalEnrolledbySem',
            'totalUnscheduledPaidApplicants'
        ));
    }

    public function create()
    {
        $elog = Elogs::where('is_used', 0)->latest()->first();

        return view('Admission.PSE.create', compact('elog'));
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

            'school_name' => 'nullable|string|max:255',
            'lrn' => 'nullable|string|max:50',
            'school_address' => 'nullable|string|max:500',
            'school_zip' => 'nullable|string|max:20',

            'program' => 'required|string|max:255',
        ]);

        $schoolYear = date('y');
        $semester = '01';

        $lastApp = PseAdmission::where('application_number', 'like', "SY{$schoolYear}{$semester}%")
                    ->orderBy('id', 'desc')
                    ->first();

        if ($lastApp) {
            $lastNumber = (int) substr($lastApp->application_number, 6, 4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $applicationNumber = "SY{$schoolYear}{$semester}{$newNumber}P";

        PseAdmission::create($request->all() + [
            'application_number' => $applicationNumber,
            'created_by' => Auth::id(),
            'year' => $schoolYear,
            'term' => $semester,
        ]);

        if ($request->filled('elog_id')) {
            Elogs::where('id', $request->elog_id)->update(['is_used' => 1]);
        }

        return redirect()->route('admission.pse.create')->with([
            'show_success_modal' => true,
            'application_number' => $applicationNumber,
        ]);
    }

    public function totalapplicants(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = PseAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.PSE.totalapplicants', compact('applications'));
    }

    public function totalenrolled(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = PseAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1, 2])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.PSE.totalenrolled', compact('applications'));
    }

    public function unsched(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = PseAdmission::where('is_active', 1)
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

        return view('Admission.PSE.unsched', compact('applications'));
    }

    public function edit($applicationNumber)
    {
        $application = PseAdmission::with(['collection'])->where('application_number', $applicationNumber)->firstOrFail();
        $subtestsincoming = PseElemSubtest::where('is_active', 1)->where('type', 1)->get();
        $subteststransferee = PseElemSubtest::where('is_active', 1)->where('type', 2)->get();

        foreach ($subtestsincoming as $subtest) {
            $existingResult = PseElemSubtestResult::where('application_number_id', $application->application_number)
                ->where('subtest_id', $subtest->id)
                ->first();

            $subtest->result = (object) [
                'rs' => $existingResult->rs ?? null,
                'api' => $existingResult->percentage ?? null,
            ];
        }

        foreach ($subteststransferee as $subtest) {
            $existingResult = PseElemSubtestResult::where('application_number_id', $application->application_number)
                ->where('subtest_id', $subtest->id)
                ->first();

            $subtest->result = (object) [
                'rs' => $existingResult->rs ?? null,
                'api' => $existingResult->percentage ?? null,
            ];
        }

        return view('Admission.PSE.edit', compact('application', 'subtestsincoming', 'subteststransferee'));
    }

    public function update(Request $request, $applicationNumber)
    {
        $pse = PseAdmission::where('application_number', $applicationNumber)->firstOrFail();

        // Update applicant info
        $pse->update([
            'lastname' => $request->input('lastname'),
            'firstname' => $request->input('firstname'),
            'middlename' => $request->input('middlename'),
            'suffix' => $request->input('suffix'),

            'gender' => $request->input('gender'),
            'mobile_no' => $request->input('mobile_no'),
            'email' => $request->input('email'),
            'dob' => $request->input('dob'),
            'age' => $request->input('age'),
            'nationality' => $request->input('nationality'),
            'religion' => $request->input('religion'),
            'address' => $request->input('address'),
            'zip_code' => $request->input('zip_code'),
            'contact_person' => $request->input('contact_person'),
            'contact_number' => $request->input('contact_number'),

            'school_name' => $request->input('school_name'),
            'lrn' => $request->input('lrn'),
            'school_address' => $request->input('school_address'),
            'school_zip' => $request->input('school_zip'),

            'program' => $request->input('program'),

            'applicant_status' => $request->input('applicant_status'),
            'exam_schedule_date' => $request->input('exam_schedule_date'),

            'exam_taken' => $request->input('exam_taken', 0) ? 1 : 0,

            'interviewer_remarks' => $request->input('interviewer_remarks'),
            'placement' => $request->input('placement'),
            'remarks' => $request->input('remarks'),

            'certifier_name' => $request->input('certifier_name'),
            'certifier_designation' => $request->input('certifier_designation'),
            'verifier_name' => $request->input('verifier_name'),
            'verifier_designation' => $request->input('verifier_designation'),

            'updated_by' => Auth::id(),
        ]);

        if ($pse->applicant_status === 'Transferee (Grade 2-6)') {
            $subtestIds = $request->input('transferee_subtest_id', []);
            $rawscores = $request->input('transferee_rs', []);
            $totalscores = $request->input('transferee_ts', []);
            $names = $request->input('transferee_subtest_name', []);

            foreach ($subtestIds as $i => $subtestId) {
                $rs = is_numeric($rawscores[$i]) ? (float) $rawscores[$i] : 0;
                $ts = isset($totalscores[$i]) && is_numeric($totalscores[$i]) ? (float) $totalscores[$i] : 0;
                $name = $names[$i] ?? 'N/A';
                $percentage = $ts > 0 ? ($rs / $ts) * 100 : 0;

                PseElemSubtestResult::updateOrCreate(
                    [
                        'application_number_id' => $pse->application_number,
                        'subtest_id' => $subtestId,
                    ],
                    [
                        'rs' => $rs,
                        'ts' => $ts,
                        'name' => $name,
                        'percentage' => $percentage,
                    ]
                );
            }
        } elseif ($pse->applicant_status === 'Incoming Grade 1') {
            $subtestIds = $request->input('incoming_subtest_id', []);
            $rawscores = $request->input('incoming_rs', []);
            $totalscores = $request->input('incoming_ts', []);
            $names = $request->input('incoming_subtest_name', []);

            $totalRs = 0;

            foreach ($subtestIds as $i => $subtestId) {
                $rs = is_numeric($rawscores[$i]) ? (int) $rawscores[$i] : 0;
                $ts = isset($totalscores[$i]) && is_numeric($totalscores[$i]) ? (int) $totalscores[$i] : 0;
                $name = $names[$i] ?? 'N/A';

                $ratingRow = PseElemSubtestG1Rating::where('subtest_id', $subtestId)
                    ->where('score', $rs)
                    ->first();

                $percentage = $ratingRow ? $ratingRow->rating : 0;

                PseElemSubtestResult::updateOrCreate(
                    [
                        'application_number_id' => $pse->application_number,
                        'subtest_id' => $subtestId,
                    ],
                    [
                        'rs' => $rs,
                        'ts' => $ts,
                        'name' => $name,
                        'percentage' => $percentage,
                    ]
                );

                $totalRs += $rs; // accumulate total raw score
            }

            // Determine total_rating based on total raw scores
            if ($totalRs >= 111 && $totalRs <= 126) {
                $totalRating = 'High Degree of Readiness';
            } elseif ($totalRs >= 102 && $totalRs <= 110) {
                $totalRating = 'Above Average Degree of Readiness';
            } elseif ($totalRs >= 75 && $totalRs <= 101) {
                $totalRating = 'Average Degree of Readiness';
            } elseif ($totalRs >= 62 && $totalRs <= 74) {
                $totalRating = 'Marginally Ready';
            } elseif ($totalRs >= 38 && $totalRs <= 61) {
                $totalRating = 'Not Ready Without Special Attention';
            } elseif ($totalRs >= 1 && $totalRs <= 37) {
                $totalRating = 'Not Ready Without Much Attention';
            } else {
                $totalRating = 'No Score';
            }

            // Save to the main PSE record
            $pse->total_rs = $totalRs;
            $pse->total_rating = $totalRating;
            $pse->save();
        } else {
            // Nursery, Kinder, Preparatory → skip saving subtest results
        }

        // Update status = 2
        PseAdmission::where('application_number', $applicationNumber)->update(['status' => 2]);

        return redirect()->route('admission.pse.edit', $applicationNumber)->with([
            'show_success_modal' => true,
            'application_number' => $applicationNumber,
        ]);
    }

    public function destroy($applicationNumber)
    {
        $application = PseAdmission::where('application_number', $applicationNumber)->firstOrFail();

        $application->is_active = 0;
        $application->deleted_by = Auth::id();
        $application->save();

        return redirect()
            ->route('admission.pse.index')
            ->with('success', 'Applicant deactivated successfully.');
    }

    public function view_print($applicationNumber)
    {
        // Get the main application data
        $result = PseAdmission::where('application_number', $applicationNumber)->firstOrFail();

        // Get subtests data based on applicant status
        $subtests = collect();

        if ($result->applicant_status === 'Transferee (Grade 2-6)') {
            // For transferees, get type 2 subtests with their results
            $subtestData = PseElemSubtest::where('is_active', 1)
                ->where('type', 2)
                ->get();

            foreach ($subtestData as $subtest) {
                $existingResult = PseElemSubtestResult::where('application_number_id', $result->application_number)
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
        } elseif ($result->applicant_status === 'Incoming Grade 1') {
            // For incoming Grade 1, get type 1 subtests with their results
            $subtestData = PseElemSubtest::where('is_active', 1)
                ->where('type', 1)
                ->get();

            foreach ($subtestData as $subtest) {
                $existingResult = PseElemSubtestResult::where('application_number_id', $result->application_number)
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
        // For Nursery, Preparatory, Kinder - no subtests needed

        return view('Admission.PSE.viewprint', compact('result', 'subtests'));
    }
}
