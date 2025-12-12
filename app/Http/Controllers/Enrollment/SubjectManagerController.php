<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\SrmSubject;
use App\Models\System\SysDepartment;
use App\Models\System\SrmProgram;

class SubjectManagerController extends Controller
{
    public function index(Request $request)
    {
        $subjects = SrmSubject::where('is_active', 1)->get();
        $deptorder = [4, 5, 31, 6, 7, 8, 9, 10, 11];
        $departments = SysDepartment::where('is_academic', 1)->where('is_active', 1)->orderByRaw("FIELD(id, " . implode(',', $deptorder) . ")")->get();
        return view('Enrollment.SubjectManager.index', compact('subjects', 'departments'));
    }

    public function getPrograms($deptId)
    {
        $programs = SrmProgram::where('department_id', $deptId)->where('is_active', 1)->orderBy('name', 'asc')->get();
        return response()->json($programs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:srm_subjects,code',
            'name' => 'required|string|max:255',
            'units' => 'required|numeric|min:0',
            'clock_hours' => 'required|numeric|min:0',
            'is_lab' => 'required|boolean',
            'lab_type' => 'nullable|boolean',
            'is_seminar' => 'required|boolean',
            'has_conflicts' => 'required|boolean',
            'has_energy' => 'required|boolean',
            'is_evaluated' => 'required|boolean',
            'is_graded' => 'required|boolean',
            'is_major' => 'required|boolean',
            'program_id' => 'nullable',
        ]);

        SrmSubject::create([
            'code' => $request->code,
            'name' => $request->name,
            'units' => $request->units,
            'clock_hours' => $request->clock_hours,
            'is_lab' => $request->is_lab,
            'lab_type' => $request->lab_type,
            'is_seminar' => $request->is_seminar,
            'has_conflicts' => $request->has_conflicts,
            'has_energy' => $request->has_energy,
            'is_evaluated' => $request->is_evaluated,
            'is_graded' => $request->is_graded,
            'is_major' => $request->is_major,
            'program_id' => $request->program_id,
            'is_active' => 1,
        ]);

        return back()->with('message', 'Subject added successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'units' => 'required|numeric',
            'clock_hours' => 'required|numeric',
            'is_lab' => 'required',
            'lab_type' => 'required',
            'is_seminar' => 'required',
            'has_conflicts' => 'required',
            'has_energy' => 'required',
            'is_graded' => 'required',
            'is_evaluated' => 'required',
            'is_major' => 'required',
        ]);

        $subject = SrmSubject::findOrFail($request->id);
        
        $subject->update($request->only([
            'code',
            'name',
            'units',
            'clock_hours',
            'is_lab',
            'lab_type',
            'is_seminar',
            'has_conflicts',
            'has_energy',
            'is_graded',
            'is_evaluated',
            'is_major',
        ]) + ['updated_by' => auth()->user()->user_id,]);

        return redirect()->back()->with([
            'message' => 'Subject updated successfully!',
            'message_type' => 'success'
        ]);
    }

    public function destroy($id)
    {
        $item = SrmSubject::findOrFail($id);

        try {
            $item->deleted_by = auth()->user()->user_id;
            $item->save();
            $item->delete();
            return redirect()->back()->with('message', 'Subject deleted successfully!')
                                     ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Failed to delete item!')
                                     ->with('message_type', 'error');
        }
    }

}