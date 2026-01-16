<?php

namespace App\Http\Controllers\PPFMO;

use App\Http\Controllers\Controller;
use App\Models\PPFMO\ServiceRequest;
use App\Models\PPFMO\PpfmoUser;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $serviceRequests = ServiceRequest::orderBy('created_at', 'desc')->get();
        return view('ppfmo.service-requests.index', compact('serviceRequests'));
    }

    public function create()
    {
        $requestTypes = ServiceRequest::REQUEST_TYPES;
        $specificReports = ServiceRequest::SPECIFIC_REPORTS;
        
        return view('ppfmo.service-requests.create', compact('requestTypes', 'specificReports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_reported' => 'required|date',
            'request_type' => 'required|in:' . implode(',', array_keys(ServiceRequest::REQUEST_TYPES)),
            'specific_report' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'remarks' => 'nullable|string|max:1000',
            'reported_by' => 'required|string|max:255',
        ]);

        $validated['status'] = 'Pending';
        $validated['received_by'] = null;
        $validated['endorsed_to'] = null;
        $validated['attested_by'] = null;
        $validated['date_completed'] = null;

        $validated['request_number'] = ServiceRequest::generateRequestNumber();

        ServiceRequest::create($validated);

        return redirect()->route('ppfmo.service-requests.index')
            ->with('success', 'Service request created successfully.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        return view('ppfmo.service-requests.show', compact('serviceRequest'));
    }

    /**
     * Quickly update the status and date completed from the Show view.
     */
    public function quickUpdate(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(ServiceRequest::STATUSES)),
            'date_completed' => 'nullable|date|required_if:status,Completed',
        ]);

        // Logic: If status is NOT completed, ensure date_completed is null in DB
        if ($validated['status'] !== 'Completed') {
            $validated['date_completed'] = null;
        } else {
            // Ensure date_completed is set if status is completed
            if (empty($validated['date_completed'])) {
                $validated['date_completed'] = now()->format('Y-m-d');
            }
        }

        $serviceRequest->update($validated);

        return back()->with('success', 'Status updated successfully.');
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $requestTypes = ServiceRequest::REQUEST_TYPES;
        $specificReports = ServiceRequest::SPECIFIC_REPORTS;
        $statuses = ServiceRequest::STATUSES;
        
        // Fetch PPFMO Personnel (Department 18) for the dropdown
        // We format the array as ['Name' => 'Name (Designation)'] for better UX in the select box
        $ppfmoUsers = PpfmoUser::active()
            ->where('department_id', 18)
            ->get()
            ->mapWithKeys(function ($user) {
                // Key is the Name (for database storage), Value is Name (Designation) (for display)
                return [$user->name => $user->name . ' (' . $user->designation_name . ')'];
            })
            ->toArray();
        
        return view('ppfmo.service-requests.edit', compact('serviceRequest', 'requestTypes', 'specificReports', 'statuses', 'ppfmoUsers'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        // Validation rules for the Admin Form (Includes all fields)
        $validated = $request->validate([
            'date_reported' => 'required|date',
            'request_type' => 'required|in:' . implode(',', array_keys(ServiceRequest::REQUEST_TYPES)),
            'specific_report' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'remarks' => 'nullable|string|max:1000',
            'status' => 'required|in:' . implode(',', array_keys(ServiceRequest::STATUSES)), // Required for Admin
            'reported_by' => 'required|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'endorsed_to' => 'nullable|string|max:255',
            'attested_by' => 'nullable|string|max:255',
            'date_completed' => 'nullable|date'
        ]);

        $serviceRequest->update($validated);

        return redirect()->route('ppfmo.service-requests.index')
            ->with('success', 'Service request updated successfully.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();

        return redirect()->route('ppfmo.service-requests.index')
            ->with('success', 'Service request deleted successfully.');
    }

    public function getReportsByType(Request $request)
    {
        $type = $request->get('type');
        $reports = ServiceRequest::SPECIFIC_REPORTS[$type] ?? [];
        
        return response()->json($reports);
    }
}