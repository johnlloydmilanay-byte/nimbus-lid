<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\SysDepartment;
use App\Models\System\SysTerms;
use App\Models\System\SrmCurriculum;
use App\Models\System\SysYearLevels;
use App\Models\System\SysYearLevelsDetails;
use App\Models\System\SrmProgram;
use App\Models\System\SrmCurriculumYear;
use App\Models\System\SrmSubject;
use App\Models\System\SrmCurriculumSubject;
use App\Models\System\SrmCurriculumPrerequisite;

class CurriculumManagementController extends Controller
{
    public function index(Request $request)
    {
        $deptorder = [4, 5, 31, 6, 7, 8, 9, 10, 11];
        $departments = SysDepartment::where('is_academic', 1)->where('is_active', 1)->orderByRaw("FIELD(id, " . implode(',', $deptorder) . ")")->get();
        $programs = collect();

        if ($request->filled('department_id')) {
            $programs = SrmProgram::where('department_id', $request->department_id)
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get();
        }

        $terms = SysTerms::where('is_active', 1)->get();
        $curriculumyear = collect();
        if ($request->filled('program_id')) {
            $curriculumyear = SrmCurriculumYear::where('program_id', $request->program_id)
                ->where('is_active', 1)
                ->get();
        }

        return view('Enrollment.CurriculumManagement.index',compact ('departments', 'programs', 'terms', 'curriculumyear'));
    }

    public function getPrograms($deptId)
    {
        $programs = SrmProgram::where('department_id', $deptId)->where('is_active', 1)->orderBy('name', 'asc')->get();
        return response()->json($programs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id'    => 'required|exists:sys_departments,id',
            'program_id'       => 'required|exists:srm_programs,id',
            'curriculum_year'  => 'required|integer',
        ]);

        $exists = SrmCurriculumYear::where('department_id', $request->department_id)
            ->where('program_id', $request->program_id)
            ->where('curriculum_year', $request->curriculum_year)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return back()->with([
                'message' => 'Curriculum Year already exists!',
                'message_type' => 'warning'
            ]);
        }

        SrmCurriculumYear::create([
            'department_id'   => $request->department_id,
            'program_id'      => $request->program_id,
            'curriculum_year' => $request->curriculum_year,
            'is_active'       => 1,
            'created_by'      => auth()->user()->user_id,
        ]);

        return back()->with([
            'message' => 'Curriculum Year added successfully!',
            'message_type' => 'success'
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'department_id' => 'required',
            'program_id' => 'required',
            'curriculum_year' => 'required',
        ]);

        $curriculum = SrmCurriculumYear::findOrFail($request->id);
        
        $curriculum->update($request->only([
            'department_id',
            'program_id',
            'curriculum_year',
        ]) + ['updated_by' => auth()->user()->user_id,]);

        return redirect()->back()->with([
            'message' => 'Curriculum Year updated successfully!',
            'message_type' => 'success'
        ]);
    }

    public function manage($programId, $curriculumYear)
    {
        $departments = SysDepartment::where('is_academic', 1)->where('is_active', 1)->orderBy('name')->get();
        $programs = SrmProgram::where('is_active', 1)->get();
        $terms = SysTerms::where('is_active', 1)->get();
        $subject = SrmSubject::where('is_active', 1)->orderBy('code')
            ->where(function ($query) use ($programId) {
                if ($programId) {
                    // Major subjects for this program OR minor subjects (program_id null)
                    $query->where('program_id', $programId)->orWhereNull('program_id');
                } else {
                    // No program selected → show all subjects
                    $query->whereNotNull('id');
                }
            })
            ->get();

        $curriculumYearIds = SrmCurriculumYear::where('program_id', $programId)->where('curriculum_year', $curriculumYear)->where('is_active', 1)->pluck('id'); 
        $prerequisite = SrmCurriculumSubject::with('subject')->where('is_active', 1)->whereIn('curriculum_year_id', $curriculumYearIds)->get()->sortBy(fn($cs) => $cs->subject->code ?? '');

        $program = SrmProgram::findOrFail($programId);
        $department = $program->department;

        $yearLevelDetails = SysYearLevelsDetails::whereHas('yearLevel', function ($q) use ($department) {
            $q->where('academic_group_id', $department->academicgroup_id)
            ->where('is_active', 1);
        })
        ->orderBy('order')
        ->get();

        $curriculumyears = SrmCurriculumYear::where('program_id', $programId)
            ->where('department_id', $program->department_id)
            ->where('curriculum_year', $curriculumYear)
            ->where('is_active', 1)
            ->get();

        $curriculumSubjects = SrmCurriculumSubject::with([
                'subject',
                'yearLevelDetail',
                'term',
            ])
            ->whereHas('curriculumYear', function ($q) use ($programId, $curriculumYear) {
                $q->where('program_id', $programId)
                ->where('curriculum_year', $curriculumYear)
                ->where('is_active', 1);
            })
            ->where('is_active', 1)
            ->orderBy('created_at', 'asc')
            ->get();
        
        $groupedSubjects = [];

        foreach ($curriculumSubjects as $item) {
            $ylId = $item->year_level_details_id;
            $termId = $item->term_id;

            if (!isset($groupedSubjects[$ylId])) {
                $groupedSubjects[$ylId] = [];
            }

            if (!isset($groupedSubjects[$ylId][$termId])) {
                $groupedSubjects[$ylId][$termId] = collect();
            }

            $groupedSubjects[$ylId][$termId]->push($item);
        }

        return view('Enrollment.CurriculumManagement.manage', compact(
            'departments', 'programs', 'terms', 'yearLevelDetails', 'curriculumyears', 'program', 'department', 'curriculumYear', 'subject', 'curriculumSubjects', 'groupedSubjects', 'prerequisite'
        ));
    }

    public function manageStore(Request $request)
    {
        $request->validate([
            'curriculum_year_id' => 'required|exists:srm_curriculum_year,id',
            'subject_id' => 'required|exists:srm_subjects,id',
            'term_id' => 'required|exists:sys_terms,id',
            'year_level_id' => 'required|exists:sys_year_levels_details,id',
            'required_subject_id' => 'nullable|array',
            'required_subject_id.*' => 'exists:srm_curriculum_subject,id',
            'substitute_subject_id' => 'nullable|exists:srm_curriculum_subject,id',
        ]);

        $exists = SrmCurriculumSubject::where('curriculum_year_id', $request->curriculum_year_id)
            ->where('subject_id', $request->subject_id)
            ->where('term_id', $request->term_id)
            ->where('year_level_details_id', $request->year_level_id)
            ->first();

        if ($exists) {
            return redirect()->back()->with([
                'message' => 'This subject already exists in the curriculum!',
                'message_type' => 'warning'
            ]);
        }

        $curriculumSubject = SrmCurriculumSubject::create([
            'curriculum_year_id' => $request->curriculum_year_id,
            'subject_id' => $request->subject_id,
            'term_id' => $request->term_id,
            'year_level_details_id' => $request->year_level_id,
            'is_active' => 1,
            'created_by' => auth()->user()->user_id,
            'required_subject_id' => $request->required_subject_id,
            'substitute_subject_id' => $request->substitute_subject_id,
        ]);

        if ($request->required_subject_id) {
            foreach ($request->required_subject_id as $currSubjId) {

                $subject = SrmCurriculumSubject::find($currSubjId);

                SrmCurriculumPrerequisite::create([
                    'curriculum_subject_id' => $curriculumSubject->id,
                    'prereq_subject_id' => $subject->subject_id,   
                    'is_active' => 1,
                    'created_by' => auth()->user()->user_id,
                ]);
            }
        }


        return redirect()->back()->with([
            'message' => 'Subject added to curriculum successfully!',
            'message_type' => 'success'
        ]);
    }

    public function destroy($id)
    {
        $item = SrmCurriculumSubject::findOrFail($id);

        try {
            $item->deleted_by = auth()->user()->user_id;
            $item->is_active = 0;
            $item->save();
            $item->delete();
            return redirect()->back()->with('message', 'Curriculum item deleted successfully!')
                                     ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Failed to delete item!')
                                     ->with('message_type', 'error');
        }
    }

}
