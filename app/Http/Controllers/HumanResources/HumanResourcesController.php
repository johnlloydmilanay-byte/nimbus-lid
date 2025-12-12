<?php

namespace App\Http\Controllers\HumanResources;

use App\Http\Controllers\Controller;
use App\Models\System\SrmEmployee;
use App\Models\System\SrmUser;
use App\Models\System\SysDepartment;
use App\Models\System\SysEmployeePosition;
use App\Models\System\SysEmployeeRank;
use App\Models\System\SysEmployeeStatus;
use App\Models\System\SysEmployeeType;
use App\Models\System\SysProvinces;
use App\Models\System\SysTowns;
use Illuminate\Http\Request;

class HumanResourcesController extends Controller
{
    public function index(Request $request)
    {
        $auth = auth()->user();
        if ($auth && $auth->usertype != 2) {
            abort(403, 'YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE.');
        }

        $totalEmployee = SrmEmployee::where('is_active', 1)->count();

        return view('HumanResources.index', compact('auth', 'totalEmployee'));
    }

    public function create()
    {
        $auth = auth()->user();
        if ($auth && $auth->usertype != 2) {
            abort(403, 'YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE.');
        }

        $departments = SysDepartment::orderBy('code')->get();
        $provinces = SysProvinces::all();
        $towns = SysTowns::where('province_id', 5)->get();
        $position = SysEmployeePosition::where('is_active', 1)->get();
        $rank = SysEmployeeRank::where('is_active', 1)->get();
        $type = SysEmployeeType::where('is_active', 1)->get();
        $status = SysEmployeeStatus::where('is_active', 1)->get();

        return view('HumanResources.create', compact('auth', 'departments', 'provinces', 'towns', 'position', 'rank', 'type', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tk_id' => 'nullable|string|max:50',
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'prefix' => 'nullable|string|max:50',
            'extension' => 'nullable|string|max:50',
            'department_id' => 'required|string|max:50',
            'designation' => 'nullable|string|max:50',
            'position_id' => 'required|string|max:50',
            'employment_date' => 'required|string|max:50',
            'rank_faculty_id' => 'nullable|string|max:50',
            'employee_type_id' => 'required|string|max:50',
            'employment_status_id' => 'required|string|max:50',
        ]);

        $createdBy = auth()->user()->username ?? null;

        $datePart = now()->format('mdy');
        $lastUser = SrmUser::where('user_id', 'like', $datePart.'%')
            ->orderBy('user_id', 'desc')
            ->first();

        if ($lastUser) {
            $lastLetter = substr($lastUser->user_id, -1);
            $nextLetter = chr(ord($lastLetter) + 1);
            if ($nextLetter > 'Z') {
                $nextLetter = 'A';
            }
        } else {
            $nextLetter = 'A';
        }

        $user_id = $datePart.$nextLetter;

        $srmUser = SrmUser::create([
            'user_id' => $user_id,
            'username' => $user_id,
            'password' => bcrypt($user_id),
            'usertype' => 2,
            'created_by' => $createdBy,
            'created_at' => now(),
        ]);

        $employee = SrmEmployee::create([
            'employee_id' => $user_id,
            'tk_id' => $request->tk_id,
            'lastname' => strtoupper($request->lastname),
            'firstname' => strtoupper($request->firstname),
            'middlename' => strtoupper($request->middlename),
            'suffix' => strtoupper($request->suffix),
            'prefix' => strtoupper($request->prefix),
            'extension' => strtoupper($request->extension),
            'department_id' => $request->department_id,
            'designation' => strtoupper($request->designation),
            'position_id' => $request->position_id,
            'employment_date' => $request->employment_date,
            'rank_faculty_id' => $request->rank_faculty_id,
            'employee_type_id' => $request->employee_type_id,
            'employment_status_id' => $request->employment_status_id,
            'created_by' => $createdBy,
            'created_at' => now(),
        ]);

        if ((int) $request->position_id === 3 && (int) $request->employee_type_id === 1) {
            SysDepartment::where('id', $request->department_id)
                ->update(['emp_id' => $employee->employee_id]);

            SrmUser::where('user_id', $user_id)
                ->update(['department_id' => $request->department_id]);
        }

        return redirect()->route('hr.create')->with(['show_success_modal' => true, 'user_id' => $user_id]);
    }
}
