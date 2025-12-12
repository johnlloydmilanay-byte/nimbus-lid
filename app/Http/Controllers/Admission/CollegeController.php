<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Admission\CollegeAdmission;
use App\Models\Admission\CollegeAdmissionShsPrograms;
use App\Models\Admission\CollegeScoreTransmutation;
use App\Models\Admission\CollegeSubtest;
use App\Models\Admission\CollegeSubtestResult;
use App\Models\Elogs;
use App\Models\System\SrmProgram;
use App\Models\System\SysDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollegeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = CollegeAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $totalApplicants = CollegeAdmission::where('is_active', 1)->count();
        $totalApplicantsBySem = CollegeAdmission::where('is_active', 1)->whereNotIn('status', [0, 1])->count();
        $totalEnrolledbySem = CollegeAdmission::where('is_active', 1)->where('status', 3)->count();
        $totalUnscheduledPaidApplicants = CollegeAdmission::where('status', 1)->where('exam_schedule_date', null)->count();

        return view('Admission.College.index', compact(
            'applications',
            'totalApplicants',
            'totalApplicantsBySem',
            'totalEnrolledbySem',
            'totalUnscheduledPaidApplicants'
        ));
    }

    public function create()
    {
        $strand = CollegeAdmissionShsPrograms::where('is_active', 1)->orderBy('program', 'asc')->get();

        $programs = SrmProgram::with('department')
            ->where('is_active', 1)
            ->whereHas('department', function ($query) {
                $query->where('academicgroup_id', 1);
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

        return view('Admission.College.create', compact('strand', 'programs', 'elog'));
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

            'strand_id' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:500',
            'school_zip' => 'required|string|max:20',

            'choice_first' => 'required|string|max:255',
            'choice_second' => 'required|string|max:255',
            'choice_third' => 'required|string|max:255',
        ]);

        $schoolYear = date('y');
        $semester = '01';

        $lastApp = CollegeAdmission::where('application_number', 'like', "SY{$schoolYear}{$semester}%")
                    ->orderBy('id', 'desc')
                    ->first();

        if ($lastApp) {
            $lastNumber = (int) substr($lastApp->application_number, 6, 4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $applicationNumber = "SY{$schoolYear}{$semester}{$newNumber}C";

        CollegeAdmission::create($request->all() + [
            'application_number' => $applicationNumber,
            'created_by' => Auth::id(),
            'year' => $schoolYear,
            'term' => $semester,
        ]);

        if ($request->filled('elog_id')) {
            Elogs::where('id', $request->elog_id)->update(['is_used' => 1]);
        }

        return redirect()->route('admission.college.create')->with([
            'show_success_modal' => true,
            'application_number' => $applicationNumber,
        ]);
    }

    public function totalapplicants(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = CollegeAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.College.totalapplicants', compact('applications'));
    }

    public function totalenrolled(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = CollegeAdmission::where('is_active', 1)
            ->when($search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%");
            })
            ->whereNotIn('status', [0, 1, 2])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Admission.College.totalenrolled', compact('applications'));
    }

    public function unsched(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $applications = CollegeAdmission::where('is_active', 1)
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

        return view('Admission.College.unsched', compact('applications'));
    }

    public function edit(Request $request, $applicationNumber)
    {
        $strand = CollegeAdmissionShsPrograms::where('is_active', 1)->get();

        // Fetch the application
        $application = CollegeAdmission::with(['collection'])
            ->where('application_number', $applicationNumber)
            ->firstOrFail();

        // Fetch programs
        $programs = SrmProgram::with('department')
            ->where('is_active', 1)
            ->whereHas('department', function ($query) {
                $query->where('academicgroup_id', 1);
            })
            ->select('srm_programs.*')
            ->addSelect(['dcode' => SysDepartment::select('code')
                ->whereColumn('sys_departments.id', 'srm_programs.department_id')
                ->limit(1)])
            ->orderBy(SysDepartment::select('code')
                ->whereColumn('sys_departments.id', 'srm_programs.department_id')
                ->limit(1))
            ->orderBy('srm_programs.code')
            ->get();

        // Fetch subtests (type 11)
        $subtests = CollegeSubtest::where('type', 11)->get();

        // Attach existing results if any
        foreach ($subtests as $subtest) {
            $existingResult = CollegeSubtestResult::where('application_number_id', $application->application_number)
                ->where('subtest_id', $subtest->id)
                ->first();

            $subtest->result = (object) [
                'rs' => $existingResult->rawscore ?? null,
                'hg' => $existingResult->hs_grade ?? null,
                'trans' => $existingResult->transmutation ?? null,
                'api' => $existingResult->api ?? null,
            ];
        }

        return view('Admission.College.edit', compact('strand', 'application', 'programs', 'subtests'));
    }

    // public function update(Request $request, $applicationNumber)
    // {
    //     $college = CollegeAdmission::where('application_number', $applicationNumber)->firstOrFail();

    //     // Update applicant info + exam_taken + updated_by
    //     $college->update($request->only([
    //         'lastname', 'firstname', 'middlename', 'suffix',
    //         'gender', 'mobile_no', 'email', 'dob', 'age',
    //         'nationality', 'religion', 'address', 'zip_code',
    //         'contact_person', 'contact_number',
    //         'strand_id', 'school_name', 'school_address', 'school_zip',
    //         'choice_first', 'choice_second', 'choice_third',
    //         'year_level', 'applicant_status', 'exam_schedule_date',
    //         'certifier_name', 'certifier_designation',
    //         'verifier_name', 'verifier_designation',
    //     ]) + [
    //         'exam_taken' => $request->boolean('exam_taken') ? 1 : 0,
    //         'updated_by' => Auth::id(),
    //     ]);

    //     // If exam taken == 1 → compute and store results
    //     if ($college->exam_taken == 1) {
    //         $rs = $request->input('rs', []);
    //         $hg = $request->input('hg', []);
    //         $subtests = CollegeSubtest::where('type', 11)->get();

    //         // Remove existing results before inserting new
    //         CollegeSubtestResult::where('application_number_id', $college->application_number)->delete();

    //         $totalRawScore = 0;
    //         $totalApi = 0;
    //         $countApi = 0;

    //         foreach ($subtests as $subtest) {
    //             $rawScore = (float) ($rs[$subtest->id] ?? 0);
    //             $hgScore = (float) ($hg[$subtest->id] ?? 0);

    //             // find transmutation value
    //             $transmutation = CollegeScoreTransmutation::where('totalscore', $subtest->ts)
    //                 ->where('is_active', 1)
    //                 ->where('rawscore', $rawScore)
    //                 ->value('transmutation') ?? 0;

    //             // compute API
    //             if ($college->applicant_status === 'Freshman') {
    //                 $api = (0.6 * $transmutation) + (0.4 * $hgScore);
    //             } else {
    //                 $api = $transmutation;
    //             }

    //             // accumulate totals
    //             $totalRawScore += $rawScore;
    //             if ($api != 0) {
    //                 $totalApi += $api;
    //                 $countApi++;
    //             }

    //             CollegeSubtestResult::create([
    //                 'application_number_id' => $college->application_number,
    //                 'name' => $subtest->name,
    //                 'subtest' => $subtest->slug,
    //                 'subtest_id' => $subtest->id,
    //                 'ts' => $subtest->ts,
    //                 'rawscore' => $rawScore,
    //                 'transmutation' => $transmutation,
    //                 'hs_grade' => $hgScore,
    //                 'api' => $api,
    //             ]);
    //         }

    //         // Compute average API (exclude zeros)
    //         $averageApi = $countApi > 0 ? round($totalApi / $countApi, 2) : 0;

    //         // Update totals and status
    //         $college->update([
    //             'total_rs' => $totalRawScore,
    //             'total_ave_api' => $averageApi,
    //             'status' => 2,
    //         ]);
    //     } else {
    //         // If exam not taken, reset results
    //         CollegeSubtestResult::where('application_number_id', $college->application_number)->delete();

    //         $college->update([
    //             'status' => 1,
    //             'exam_taken' => 0,
    //             'total_rs' => null,
    //             'total_ave_api' => null,
    //         ]);
    //     }

    //     // Ensure status is 2 for completed updates
    //     CollegeAdmission::where('application_number', $applicationNumber)->update(['status' => 2]);

    //     return redirect()->route('admission.college.edit', $applicationNumber)
    //         ->with([
    //             'show_success_modal' => true,
    //             'application_number' => $applicationNumber,
    //         ]);
    // }

    public function update(Request $request, $applicationNumber)
    {
        $college = CollegeAdmission::where('application_number', $applicationNumber)->firstOrFail();

        // Update applicant info + exam_taken + updated_by
        $college->update($request->only([
            'lastname', 'firstname', 'middlename', 'suffix',
            'gender', 'mobile_no', 'email', 'dob', 'age',
            'nationality', 'religion', 'address', 'zip_code',
            'contact_person', 'contact_number',
            'strand_id', 'school_name', 'school_address', 'school_zip',
            'choice_first', 'choice_second', 'choice_third',
            'year_level', 'applicant_status', 'exam_schedule_date',
            'certifier_name', 'certifier_designation',
            'verifier_name', 'verifier_designation',
        ]) + [
            'exam_taken' => $request->boolean('exam_taken') ? 1 : 0,
            'updated_by' => Auth::id(),
        ]);

        // If exam taken == 1 → compute and store results
        if ($college->exam_taken == 1) {
            $rs = $request->input('rs', []);
            $hg = $request->input('hg', []);
            $subtests = CollegeSubtest::where('type', 11)->get();

            // Remove existing results before inserting new
            CollegeSubtestResult::where('application_number_id', $college->application_number)->delete();

            $totalRawScore = 0;
            $totalApi = 0;
            $countApi = 0;

            foreach ($subtests as $subtest) {
                $rawScore = (float) ($rs[$subtest->id] ?? 0);
                $hgScore = (float) ($hg[$subtest->id] ?? 0);

                // find transmutation value
                $transmutation = CollegeScoreTransmutation::where('totalscore', $subtest->ts)
                    ->where('is_active', 1)
                    ->where('rawscore', $rawScore)
                    ->value('transmutation') ?? 0;

                // compute API
                if ($college->applicant_status === 'Freshman') {
                    $api = (0.6 * $transmutation) + (0.4 * $hgScore);
                } else {
                    $api = $transmutation;
                }

                // accumulate totals
                $totalRawScore += $rawScore;
                if ($api != 0) {
                    $totalApi += $api;
                    $countApi++;
                }

                CollegeSubtestResult::create([
                    'application_number_id' => $college->application_number,
                    'name' => $subtest->name,
                    'subtest' => $subtest->slug,
                    'subtest_id' => $subtest->id,
                    'ts' => $subtest->ts,
                    'rawscore' => $rawScore,
                    'transmutation' => $transmutation,
                    'hs_grade' => $hgScore,
                    'api' => $api,
                ]);
            }

            // Compute average API (exclude zeros)
            $averageApi = $countApi > 0 ? round($totalApi / $countApi, 2) : 0;

            // Check if qualified
            $firstChoiceProgram = SrmProgram::join('sys_departments', 'srm_programs.department_id', '=', 'sys_departments.id')
                ->where('srm_programs.id', $college->choice_first)
                ->select('srm_programs.*', 'sys_departments.name as dname')
                ->first();

            $remarks = null;
            if ($firstChoiceProgram && $averageApi >= $firstChoiceProgram->api) {
                $remarks = 'QUALIFIED';
            }

            // Update totals and status
            $college->update([
                'total_rs' => $totalRawScore,
                'total_ave_api' => $averageApi,
                'status' => 2,
                'remarks' => $remarks,
            ]);
        } else {
            // If exam not taken, reset results
            CollegeSubtestResult::where('application_number_id', $college->application_number)->delete();

            $college->update([
                'status' => 1,
                'exam_taken' => 0,
                'total_rs' => null,
                'total_ave_api' => null,
                'remarks' => null,
            ]);
        }

        // Ensure status is 2 for completed updates
        CollegeAdmission::where('application_number', $applicationNumber)->update(['status' => 2]);

        return redirect()->route('admission.college.edit', $applicationNumber)
            ->with([
                'show_success_modal' => true,
                'application_number' => $applicationNumber,
            ]);
    }

    public function destroy($applicationNumber)
    {
        $application = CollegeAdmission::where('application_number', $applicationNumber)->firstOrFail();

        $application->is_active = 0;
        $application->deleted_by = Auth::id();
        $application->save();

        return redirect()
            ->route('admission.college.index')
            ->with('success', 'Applicant deactivated successfully.');
    }

    public function view_print($applicationNumber)
    {
        $result = CollegeAdmission::with(['collection'])->where('application_number', $applicationNumber)->firstOrFail();
        $result->strand = CollegeAdmissionShsPrograms::find($result->strand_id);

        function getChoiceProgram($programId)
        {
            return SrmProgram::join('sys_departments', 'srm_programs.department_id', '=', 'sys_departments.id')
                ->where('srm_programs.id', $programId)
                ->select('srm_programs.*', 'sys_departments.name as dname')
                ->first();
        }

        $result->choice_first_program = getChoiceProgram($result->choice_first);
        $result->choice_second_program = getChoiceProgram($result->choice_second);
        $result->choice_third_program = getChoiceProgram($result->choice_third);

        $result->condition = '<strong style="font-size: 16px;">SUBJECT FOR ENTRY REQUIREMENT</strong><br>
            Note: The applicant must visit the College Dean of their First Choice Program for an interview.';

        if ($result->choice_first_program && $result->total_ave_api >= $result->choice_first_program->api) {
            $result->condition = '<strong style="font-size: 16px;">QUALIFIED</strong>';
        }

        $subtests = CollegeSubtestResult::where('application_number_id', $result->application_number)->get();

        return view('Admission.College.viewprint', compact('result', 'subtests'));
    }
}
