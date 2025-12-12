<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\SrmFeesManagement;
use App\Models\System\SysTerms;
use App\Models\System\SysAcademicGroups;
use App\Models\System\SysDepartment;
use App\Models\System\SrmFeesTuition;

use App\Models\System\SysFeesType;
use App\Models\System\Chartmaster;
use App\Models\System\SrmClassSchedule;
use App\Models\System\SrmProgram;
use App\Models\System\SysStudentStatus;
use App\Models\System\SrmFeesPost;


class FeesManagementController extends Controller
{
    public function index(Request $request)
    {
        $feesname = SrmFeesManagement::where('is_active', 1)->get();
        $terms = SysTerms::where('is_active', 1)->get();
        $acadorder = [3, 2, 6, 1, 5, 4, 8, 9, 10, 7];
        $academicgroup = SysAcademicGroups::where('is_active', 1)->orderByRaw("FIELD(id, " . implode(',', $acadorder) . ")")->get();
        $deptorder = [4, 5, 31, 6, 7, 8, 9, 10, 11];
        $departments = SysDepartment::where('is_academic', 1)->where('is_active', 1)->orderByRaw("FIELD(id, " . implode(',', $deptorder) . ")")->get();

        $year = $request->input('year');
        $academicgroup_id = $request->input('academicgroup_id');
        $department_id = $request->input('department_id');

        // Tuition Fees
        $tuitionPrograms = collect();
        if ($department_id) {
            $tuitionPrograms = SrmProgram::where('is_active', 1)
                ->where('department_id', $department_id)
                ->get();
        }
        $glAccounts = Chartmaster::where('group', 'TUITION FEES')->get();
        $othersAccounts = Chartmaster::where('group', 'OTHER RECEIPTS')->get();
        $labAccounts = Chartmaster::where('group', 'SPECIAL FUND RECEIPTS')->get();
        $accounts = Chartmaster::get();
        $classschedule = SrmClassSchedule::whereHas('subject', function($query) {$query->where('is_lab', 0);})->with('subject')->get();
        $labSchedule = SrmClassSchedule::whereHas('subject', function($query) {$query->where('is_lab', 1);})->with('subject')->get();


        // Miscellanous & Other Fees
        $department = collect();
        $programs = collect();
        if ($academicgroup_id) {
            $department = SysDepartment::where('academicgroup_id', $academicgroup_id)
                ->where('is_active', 1)
                ->orderBy('code', 'asc')->get();

            $programs = SrmProgram::whereIn('department_id', $department->pluck('id')->toArray())
                ->where('is_active', 1)
                ->orderBy('name', 'asc')->get();
        }

        $studentstatus = SysStudentStatus::where('is_active', 1)->get();

        $selected_feesname = null;
        if ($request->feesname) {
            $selected_feesname = SrmFeesManagement::find($request->feesname);
        }

        // Fee Types
        $feestypes = collect();
        if ($selected_feesname) {
            $feestypes = SysFeesType::where('is_active', 1)
                ->where('fee_management_id', $selected_feesname->id)
                ->get();
        }

        // Contents: load based on fee type
        $contents = collect();

        if ($selected_feesname) {
            
            // 1️⃣ Tuition Fees
            if ($selected_feesname->id == 1 && $department_id) {
                $tuitionFees = SrmFeesTuition::with('program')
                    ->whereIn('program_id', $tuitionPrograms->pluck('id')->toArray())
                    ->where('is_active', 1)
                    ->when($year, fn($q) => $q->where('year', $year))
                    ->get();

                $tuitionContents = $tuitionFees->groupBy(function($item){
                    return $item->setup_type . '|' . $item->rate_regular . '|' . $item->rate_major . '|' . $item->ar_account . '|' . ($item->feeType?->name ?? '');
                })->map(function($group){
                    return [
                        'programs' => $group->pluck('program.name')->implode('<br>'),
                        'program_ids' => $group->pluck('program_id')->implode(','), // Add program IDs for edit modal
                        'setup_type' => $group->first()->setup_type,
                        'rate_regular' => $group->first()->rate_regular,
                        'rate_major' => $group->first()->rate_major,
                        'ar_account' => $group->first()->ar_account,
                        'gl_account' => $group->first()->gl_account, // Make sure GL account is included
                        'fee_type_name' => $group->first()->feeType?->name ?? 'Tuition',
                        'id' => $group->pluck('id')->implode(',')
                    ];
                });

                $contents = $contents->merge($tuitionContents);
            }

            // 2️⃣ Other Fees (SrmFeesPost)
            $otherFees = SrmFeesPost::with(['program', 'classSchedule.subject', 'feeType', 'arAccount'])
                ->when($academicgroup_id, fn($q) => $q->where('academicgroup_id', $academicgroup_id))
                ->when($department_id, fn($q) => $q->where('department_id', $department_id))
                ->when($year, fn($q) => $q->where('year', $year))
                ->when($selected_feesname, function($q) use ($selected_feesname) {
                    $q->whereHas('feeType', fn($q2) => $q2->where('fee_management_id', $selected_feesname->id));
                })
                ->orderBy('fee_types_id')
                ->orderBy('rate')
                ->get();


            // Group and format contents
            $otherContents = $otherFees->groupBy(function($item){
                return $item->fee_name . '|' . ($item->fee_types_id ?? 0) . '|' . $item->rate . '|' . $item->ar_account . '|' . $item->gl_account;
            })->map(function($group){

                $programs = $group->map(function($fee){
                    $names = $fee->program ? $fee->program->name : '';
                    if ($fee->classSchedule && $fee->classSchedule->subject) {
                        $names .= ($names ? '<br>' : '').$fee->classSchedule->subject->code.' : '.$fee->classSchedule->section;
                    }
                    return $names;
                })->filter()->implode('<br>');

                $statuses = $group->map(function($fee){
                    return $fee->studentStatus?->name;
                })
                    ->filter()
                    ->unique()
                    ->map(fn($name) => $name . ' Student')
                    ->implode('<br>');

                $firstFee = $group->first();

                return [
                    'fee_name' => $firstFee->fee_name,
                    'fee_types_id' => $firstFee->fee_types_id,
                    'fee_type_name' => $firstFee->feeType?->name ?? '',
                    'rate' => $firstFee->rate,
                    'others_applies_to' => $programs,
                    'others_ar_account' => $firstFee->arAccount?->accountname ?? $firstFee->ar_account,
                    'others_studentstatus' => $statuses,
                    'studentStatus' => $firstFee->studentStatus,
                    'id' => $group->pluck('id')->implode(','),
                ];
            });

            // Lab Fees (selected_feesname->id == 4)
            if ($selected_feesname->id == 4) {
                $labFees = SrmFeesPost::with('classSchedule.subject')
                    ->where('year', $year)
                    ->where('term', $request->term)
                    ->where('fee_types_id', null)
                    ->where('is_active', 1)
                    ->get();

                $labContents = $labFees->groupBy(function($item){
                    return $item->rate.'|'.$item->deposit.'|'.$item->ar_account;
                })->map(function($group){
                    $first = $group->first();

                    // Combine all subjects + sections for this group
                    $combinedSubjects = $group->map(function($item){
                        if ($item->classSchedule && $item->classSchedule->subject) {
                            return $item->classSchedule->subject->code . ' : ' . $item->classSchedule->section;
                        }
                        return null;
                    })->filter()->unique()->implode('<br>');

                    return [
                        'year' => $first->year,
                        'term' => $first->term,
                        'rate' => $first->rate,
                        'deposit' => $first->deposit,
                        'ar_account' => $first->arAccount?->accountname ?? $first->ar_account,
                        'combined_subject' => $combinedSubjects,
                        'id' => $group->pluck('id')->implode(','),
                    ];
                });

                $contents = $contents->merge($labContents);
            } else {
                $contents = $contents->merge($otherContents);
            }
        }

        return view('Accounting.FeesManagement.index', compact('feesname', 'terms', 'academicgroup', 'departments',
            'year', 'academicgroup_id', 'department_id',
            'feestypes', 'accounts', 'classschedule', 'labSchedule',
            'tuitionPrograms', 'glAccounts', 'othersAccounts', 'labAccounts',
            'department', 'programs',
            'studentstatus', 'selected_feesname', 'contents'));
    }

    public function store_tuition(Request $request)
    {
        $request->validate([
            'programs' => 'required|array',
            'rate_regular' => 'required|numeric|min:0',
            'rate_major' => 'required|numeric|min:0',
            'setup_type' => 'required|boolean',
            'ar_account' => 'required|string',
            'gl_account' => 'required|string',
            'year' => 'required|integer|min:2000',
        ]);

        foreach ($request->programs as $program_id) {
            SrmFeesTuition::create([
                'year' => $request->year,
                'program_id' => $program_id,
                'rate_regular' => $request->rate_regular,
                'rate_major' => $request->rate_major,
                'setup_type' => $request->setup_type,
                'ar_account' => $request->ar_account,
                'gl_account' => $request->gl_account,
                'created_by' => auth()->user()->user_id,
            ]);
        }

        return redirect()->back()->with('success', 'Tuition fees saved successfully!');
    }

    public function import_tuition(Request $request, $year, $department_id)
    {
        $request->validate([
            'year_source' => 'required|integer|min:2000',
        ]);

        $year_source = $request->year_source;
        $year_target = $year;

        if ($year_source == $year_target) {
            return redirect()->back()->with('error', 'Source and target school years cannot be the same.');
        }

        $sourceTuitionFees = SrmFeesTuition::with('program')
            ->where('year', $year_source)
            ->where('is_active', 1)
            ->whereHas('program', function ($query) use ($department_id) {
                $query->where('department_id', $department_id);
            })
            ->get();

        if ($sourceTuitionFees->isEmpty()) {
            return redirect()->back()->with('error', 'No tuition fees found for the selected source school year.');
        }

        $imported_count = 0;

        foreach ($sourceTuitionFees as $sourceFee) {
            $existingFee = SrmFeesTuition::where('year', $year_target)
                ->where('program_id', $sourceFee->program_id)
                ->where('setup_type', $sourceFee->setup_type)
                ->where('is_active', 1)
                ->first();

            if (! $existingFee) {
                SrmFeesTuition::create([
                    'year' => $year_target,
                    'program_id' => $sourceFee->program_id,
                    'rate_regular' => $sourceFee->rate_regular,
                    'rate_major' => $sourceFee->rate_major,
                    'setup_type' => $sourceFee->setup_type,
                    'ar_account' => $sourceFee->ar_account,
                    'gl_account' => $sourceFee->gl_account,
                    'created_by' => auth()->user()->user_id,
                ]);
                $imported_count++;
            }
        }

        // return redirect()->back()->with('success', "Successfully imported {$imported_count} tuition fees from SY {$year_source}-".($year_source + 1)." to SY {$year_target}-".($year_target + 1));

        return redirect()->back()
                ->with('message', 'Successfully imported!')
                ->with('message_type', 'success');
    }

    public function update_tuition(Request $request)
    {
        $request->validate([
            'tuition_ids' => 'required|string',
            'programs' => 'required|array', 
            'rate_regular' => 'required|numeric|min:0',
            'rate_major' => 'required|numeric|min:0',
            'setup_type' => 'required|boolean',
            'ar_account' => 'required|string',
            'gl_account' => 'required|string',
            'year' => 'required|integer|min:2000',
        ]);

        $tuitionIds = explode(',', $request->tuition_ids);

        foreach ($tuitionIds as $tuitionId) {
            $tuitionFee = SrmFeesTuition::find($tuitionId);
            if ($tuitionFee) {
                $tuitionFee->update([
                    'is_active' => 0,
                    'updated_by' => auth()->user()->user_id,
                ]);
            }
        }

        foreach ($request->programs as $program_id) {
            SrmFeesTuition::create([
                'year' => $request->year,
                'program_id' => $program_id,
                'rate_regular' => $request->rate_regular,
                'rate_major' => $request->rate_major,
                'setup_type' => $request->setup_type,
                'ar_account' => $request->ar_account,
                'gl_account' => $request->gl_account,
                'created_by' => auth()->user()->user_id,
            ]);
        }

        return redirect()->back()
                ->with('message', 'Tuition fees updated successfully!')
                ->with('message_type', 'success');
    }

    public function destroy_tuition(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids)) {
            return redirect()->back()
                ->with('message', 'No item selected for deletion!')
                ->with('message_type', 'error');
        }

        $allIds = [];
        foreach ($ids as $id) {
            if (strpos($id, ',') !== false) {
                $allIds = array_merge($allIds, explode(',', $id));
            } else {
                $allIds[] = $id;
            }
        }

        $allIds = array_map('intval', array_unique($allIds));

        try {
            $items = SrmFeesTuition::whereIn('id', $allIds)->get();

            foreach ($items as $item) {
                $item->is_active = 0;
                $item->deleted_by = auth()->user()->user_id;
                $item->save();
                $item->delete();
            }

            $message = count($allIds) > 1 
                ? 'Tuition Fees deleted successfully!' 
                : 'Tuition Fee deleted successfully!';

            return redirect()->back()
                ->with('message', $message)
                ->with('message_type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Failed to delete item(s)!')
                ->with('message_type', 'error');
        }
    }

    public function store_post(Request $request)
    {
        $selected_feesname_id = $request->input('selected_feesname_id'); 

        // Base validation rules
        $rules = [
            'year' => 'required|integer|min:2000',
            'term' => 'required|integer',
            'rate' => 'required|numeric|min:0',
            'ar_account' => 'nullable|exists:chartmaster,accountcode',
            'gl_account' => 'nullable|exists:chartmaster,accountcode',
            'department_id' => 'nullable|exists:sys_departments,id',
            'class_schedule_ids' => 'nullable|array',
            'class_schedule_ids.*' => 'exists:srm_class_schedules,id',
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'exists:srm_programs,id',
            'studentstatus_id' => 'nullable|exists:sys_studentstatus,id',
            'year_level' => 'nullable|integer',
            'year_entry' => 'nullable|integer|min:1900',
        ];

        // Conditional rules per fee type
        if (in_array($selected_feesname_id, [2,3])) {
            $rules['fee_name'] = 'required|string|max:255';
            $rules['fee_types_id'] = 'required|exists:sys_fees_types,id';
            $rules['academicgroup_id'] = 'required|exists:sys_academicgroups,id';
        }

        if ($selected_feesname_id == 4) {
            $rules['fee_types_id'] = 'required|exists:srm_class_schedules,id';
            $rules['deposit'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Default empty arrays for optional checkboxes
        $classIds = $request->input('class_schedule_ids', []);
        $programIds = $request->input('program_ids', []);

        // If no class/program selected, insert at least one record with nulls
        if (empty($classIds)) $classIds = [null];
        if (empty($programIds)) $programIds = [null];

        // Loop through combinations and save
        foreach ($classIds as $classId) {
            foreach ($programIds as $programId) {
                SrmFeesPost::create([
                    'fee_name' => $request->input('fee_name'),
                    'fee_types_id' => $request->input('fee_types_id'),
                    'rate' => $request->input('rate'),
                    'deposit' => $request->input('deposit', 0),
                    'ar_account' => $request->input('ar_account'),
                    'gl_account' => $request->input('gl_account'),
                    'academicgroup_id' => $request->input('academicgroup_id'),
                    'department_id' => $request->input('department_id'),
                    'class_schedule_id' => $classId,
                    'program_id' => $programId,
                    'studentstatus_id' => $request->input('studentstatus_id'),
                    'year_level' => $request->input('year_level'),
                    'year_entry' => $request->input('year_entry'),
                    'year' => $request->input('year'),
                    'term' => $request->input('term'),
                    'created_by' => auth()->user()->user_id,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Fee successfully added.');
    }

    public function import_post(Request $request, $year)
    {
        $request->validate([
            'year_source' => 'required|integer|min:2000',
            'selected_feesname_id' => 'required|exists:srm_fees_management,id',
            'term' => 'required|integer',
            'academicgroup_id' => 'nullable|exists:sys_academicgroups,id',
            'department_id' => 'nullable|exists:sys_departments,id',
        ]);

        $year_source = $request->year_source;
        $year_target = $year;
        $term_target = $request->term;
        $fee_management_id = $request->selected_feesname_id;

        if ($year_source == $year_target) {
            return redirect()->back()->with('error', 'Source and target school years cannot be the same.');
        }

        $sourceFeesQuery = SrmFeesPost::with(['program', 'classSchedule.subject', 'feeType', 'arAccount'])
            ->where('year', $year_source)
            ->where('term', $term_target)
            ->where('is_active', 1);

        if (in_array($fee_management_id, [2, 3])) {
            $sourceFeesQuery->whereHas('feeType', function ($q) use ($fee_management_id) {
                $q->where('fee_management_id', $fee_management_id);
            });
        } elseif ($fee_management_id == 4) {
            $sourceFeesQuery->where('fee_types_id', null);
        }

        if ($request->academicgroup_id) {
            $sourceFeesQuery->where('academicgroup_id', $request->academicgroup_id);
        }
        if ($request->department_id) {
            $sourceFeesQuery->where('department_id', $request->department_id);
        }

        $sourceFees = $sourceFeesQuery->get();

        if ($sourceFees->isEmpty()) {
            return redirect()->back()->with('error', 'No fees found for the selected source school year and criteria.');
        }

        $imported_count = 0;

        foreach ($sourceFees as $sourceFee) {
            $existingFee = SrmFeesPost::where('year', $year_target)
                ->where('term', $term_target)
                ->where('fee_name', $sourceFee->fee_name)
                ->where('fee_types_id', $sourceFee->fee_types_id)
                ->where('rate', $sourceFee->rate)
                ->where('academicgroup_id', $sourceFee->academicgroup_id)
                ->where('department_id', $sourceFee->department_id)
                ->where('class_schedule_id', $sourceFee->class_schedule_id)
                ->where('program_id', $sourceFee->program_id)
                ->where('studentstatus_id', $sourceFee->studentstatus_id)
                ->where('is_active', 1)
                ->first();

            if (! $existingFee) {
                SrmFeesPost::create([
                    'fee_name' => $sourceFee->fee_name,
                    'fee_types_id' => $sourceFee->fee_types_id,
                    'rate' => $sourceFee->rate,
                    'deposit' => $sourceFee->deposit,
                    'ar_account' => $sourceFee->ar_account,
                    'gl_account' => $sourceFee->gl_account,
                    'academicgroup_id' => $sourceFee->academicgroup_id,
                    'department_id' => $sourceFee->department_id,
                    'class_schedule_id' => $sourceFee->class_schedule_id,
                    'program_id' => $sourceFee->program_id,
                    'studentstatus_id' => $sourceFee->studentstatus_id,
                    'year_level' => $sourceFee->year_level,
                    'year_entry' => $sourceFee->year_entry,
                    'year' => $year_target,
                    'term' => $term_target,
                    'created_by' => auth()->user()->user_id,
                ]);
                $imported_count++;
            }
        }

        $feeTypeName = SrmFeesManagement::find($fee_management_id)->name;

        // return redirect()->back()->with('success', "Successfully imported {$imported_count} {$feeTypeName} from SY {$year_source}-".($year_source + 1)." to SY {$year_target}-".($year_target + 1));
        
        return redirect()->back()
                ->with('message', 'Successfully imported!')
                ->with('message_type', 'success');
    }

    public function update_post(Request $request)
    {
        $selected_feesname_id = $request->input('selected_feesname_id');
        $fee_ids = $request->input('fee_ids');
        $fee_ids_array = explode(',', $fee_ids);

        // Base validation rules
        $rules = [
            'year' => 'required|integer|min:2000',
            'term' => 'required|integer',
            'rate' => 'required|numeric|min:0',
            'ar_account' => 'nullable|exists:chartmaster,accountcode',
            'gl_account' => 'nullable|exists:chartmaster,accountcode',
            'department_id' => 'nullable|exists:sys_departments,id',
            'class_schedule_ids' => 'nullable|array',
            'class_schedule_ids.*' => 'exists:srm_class_schedules,id',
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'exists:srm_programs,id',
            'studentstatus_id' => 'nullable|exists:sys_studentstatus,id',
            'year_level' => 'nullable|integer',
            'year_entry' => 'nullable|integer|min:1900',
        ];

        // Conditional rules per fee type
        if (in_array($selected_feesname_id, [2, 3])) {
            $rules['fee_name'] = 'required|string|max:255';
            $rules['fee_types_id'] = 'required|exists:sys_fees_types,id';
            $rules['academicgroup_id'] = 'required|exists:sys_academicgroups,id';
        }

        if ($selected_feesname_id == 4) {
            $rules['deposit'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Get current fee records to compare
        $current_fees = SrmFeesPost::whereIn('id', $fee_ids_array)->get();

        // Determine which records to keep, update, or delete
        $classIds = $request->input('class_schedule_ids', []);
        $programIds = $request->input('program_ids', []);

        // NEW LOGIC: If both class schedules and programs are empty, delete all records
        if (empty($classIds) && empty($programIds)) {
            $current_time = now();
            
            // Delete all current fee records
            foreach ($current_fees as $current_fee) {
                $current_fee->is_active = 0;
                $current_fee->deleted_by = auth()->user()->user_id;
                $current_fee->deleted_at = $current_time;
                $current_fee->updated_by = auth()->user()->user_id;
                $current_fee->save();
            }
            
            return redirect()->back()->with('success', 'All fee records have been deleted.');
        }
        
        // Original logic for when at least one of classIds or programIds is not empty
        if (empty($classIds)) {
            $classIds = [null];
        } elseif (empty($programIds)) {
            $programIds = [null];
        }

        // Track which combinations already exist to avoid duplicates
        $existing_combinations = [];
        $updated_ids = [];
        $current_time = now();

        // First: Identify and delete records that are no longer selected
        foreach ($current_fees as $current_fee) {
            $combination_key = ($current_fee->class_schedule_id ?? 'null').'|'.($current_fee->program_id ?? 'null');

            $should_keep = false;

            // Check if this combination is still in the selected checkboxes
            foreach ($classIds as $classId) {
                foreach ($programIds as $programId) {
                    $selected_combination_key = ($classId ?? 'null').'|'.($programId ?? 'null');
                    if ($combination_key === $selected_combination_key) {
                        $should_keep = true;
                        break 2;
                    }
                }
            }

            // If not in selected checkboxes, delete this record
            if (! $should_keep) {
                $current_fee->is_active = 0;
                $current_fee->deleted_by = auth()->user()->user_id;
                $current_fee->deleted_at = $current_time;
                $current_fee->updated_by = auth()->user()->user_id;
                $current_fee->save();
            }
        }

        // Second: Update existing records or create new ones for selected combinations
        foreach ($classIds as $classId) {
            foreach ($programIds as $programId) {
                $combination_key = ($classId ?? 'null').'|'.($programId ?? 'null');

                // Check if this combination already exists in current fees
                $existing_fee = $current_fees->first(function ($fee) use ($classId, $programId) {
                    $fee_class_id = $fee->class_schedule_id;
                    $fee_program_id = $fee->program_id;

                    // Handle null comparisons properly
                    $class_match = ($fee_class_id == $classId) || (is_null($fee_class_id) && is_null($classId));
                    $program_match = ($fee_program_id == $programId) || (is_null($fee_program_id) && is_null($programId));

                    return $class_match && $program_match && $fee->is_active == 1;
                });

                if ($existing_fee) {
                    // Update existing record
                    $existing_fee->update([
                        'fee_name' => $request->input('fee_name'),
                        'fee_types_id' => $request->input('fee_types_id'),
                        'rate' => $request->input('rate'),
                        'deposit' => $request->input('deposit', 0),
                        'ar_account' => $request->input('ar_account'),
                        'gl_account' => $request->input('gl_account'),
                        'academicgroup_id' => $request->input('academicgroup_id'),
                        'department_id' => $request->input('department_id'),
                        'studentstatus_id' => $request->input('studentstatus_id'),
                        'year_level' => $request->input('year_level'),
                        'year_entry' => $request->input('year_entry'),
                        'year' => $request->input('year'),
                        'term' => $request->input('term'),
                        'updated_by' => auth()->user()->user_id,
                        'deleted_by' => null, // Clear deletion if reactivating
                        'deleted_at' => null, // Clear deletion timestamp
                    ]);
                    $updated_ids[] = $existing_fee->id;
                } else {
                    // Create new record only if combination doesn't exist
                    $new_fee = SrmFeesPost::create([
                        'fee_name' => $request->input('fee_name'),
                        'fee_types_id' => $request->input('fee_types_id'),
                        'rate' => $request->input('rate'),
                        'deposit' => $request->input('deposit', 0),
                        'ar_account' => $request->input('ar_account'),
                        'gl_account' => $request->input('gl_account'),
                        'academicgroup_id' => $request->input('academicgroup_id'),
                        'department_id' => $request->input('department_id'),
                        'class_schedule_id' => $classId,
                        'program_id' => $programId,
                        'studentstatus_id' => $request->input('studentstatus_id'),
                        'year_level' => $request->input('year_level'),
                        'year_entry' => $request->input('year_entry'),
                        'year' => $request->input('year'),
                        'term' => $request->input('term'),
                        'created_by' => auth()->user()->user_id,
                    ]);
                    $updated_ids[] = $new_fee->id;
                }

                $existing_combinations[] = $combination_key;
            }
        }

        return redirect()->back()->with('success', 'Fee successfully updated.');
    }

    public function destroy_post(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids)) {
            return redirect()->back()
                ->with('message', 'No item selected for deletion!')
                ->with('message_type', 'error');
        }

        $allIds = [];
        foreach ($ids as $id) {
            if (strpos($id, ',') !== false) {
                $allIds = array_merge($allIds, explode(',', $id));
            } else {
                $allIds[] = $id;
            }
        }

        $allIds = array_map('intval', array_unique($allIds));

        try {
            $items = SrmFeesPost::whereIn('id', $allIds)->get();

            foreach ($items as $item) {
                $item->is_active = 0;
                $item->deleted_by = auth()->user()->user_id;
                $item->save();
                $item->delete();
            }

            $message = count($allIds) > 1 
                ? 'Tuition Fees deleted successfully!' 
                : 'Tuition Fee deleted successfully!';

            return redirect()->back()
                ->with('message', $message)
                ->with('message_type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Failed to delete item(s)!')
                ->with('message_type', 'error');
        }
    }

}
