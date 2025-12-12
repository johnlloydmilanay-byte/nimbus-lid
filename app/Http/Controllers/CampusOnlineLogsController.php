<?php

namespace App\Http\Controllers;

use App\Models\Elogs;
use App\Models\System\SysDepartment;
use Illuminate\Http\Request;

class CampusOnlineLogsController extends Controller
{
    public function index()
    {
        // $departments = SysDepartment::all();
        $departments = SysDepartment::where('id', 14)->get();
        $layout = auth()->check() ? 'layouts.master' : 'layouts.child';

        return view('CampusOnlineLogs.index', compact('departments', 'layout'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'suffix' => 'nullable|string|max:20',
            'department_id' => 'required|exists:sys_departments,id',
            'purpose' => 'required|string|max:150',
        ]);

        Elogs::create($validated);

        // return redirect()->back()->with('success', 'Log entry saved successfully!');
        return redirect()->route('elogs.index')->with([
            'show_success_modal' => true,
        ]);
    }

    public function list(Request $request)
    {
        $departments = SysDepartment::all();
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        // Query Elogs with optional search
        $applications = Elogs::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('purpose', 'like', "%{$search}%");
            });
        })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'per_page' => $perPage]);

        return view('CampusOnlineLogs.list', compact('departments', 'applications'));
    }
}
