@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">PPFMO Service Requests</h2>
        <a href="{{ route('ppfmo.service-requests.create') }}" class="btn btn-primary btn-sm mt-2 mt-md-0">
            <i class="bi bi-plus-circle"></i> Create New Request
        </a>
    </div><br>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    @php
        // Calculate stats dynamically from the collection
        $totalRequests = $serviceRequests->count();
        $pendingRequests = $serviceRequests->where('status', 'Pending')->count();
        $inProgressRequests = $serviceRequests->where('status', 'In Progress')->count();
        $completedRequests = $serviceRequests->where('status', 'Completed')->count();
    @endphp

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                In Progress</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProgressRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check2-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Requests</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="bi bi-funnel"></i> Toggle Filters
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('ppfmo.service-requests.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Reported By</label>
                            <input type="text" name="reported_by" class="form-control" value="{{ request('reported_by') }}" placeholder="Search by name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm me-2">Apply Filters</button>
                            <a href="{{ route('ppfmo.service-requests.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Service Requests Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Service Requests List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="serviceRequestsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Date Reported</th>
                            <th>Type</th>
                            <th>Specific Report</th>
                            <th>Location</th>
                            <th>Reported By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRequests as $request)
                            <tr>
                                <td><strong>{{ $request->request_number }}</strong></td>
                                <td>{{ $request->date_reported->format('M d, Y') }}</td>
                                <td><span class="badge bg-info text-dark">{{ $request->request_type }}</span></td>
                                <td>{{ $request->specific_report }}</td>
                                <td>{{ Str::limit($request->location, 30) }}</td>
                                <td>{{ $request->reported_by }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Pending' => 'warning',
                                            'In Progress' => 'primary',
                                            'Completed' => 'success',
                                            'Cancelled' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$request->status] ?? 'secondary' }}">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Details" onclick="viewRequest({{ $request->id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="{{ route('ppfmo.service-requests.edit', $request) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete" onclick="deleteRequest({{ $request->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No service requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRequestModalLabel">Service Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="requestDetails">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
 $(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialize DataTable
    $('#serviceRequestsTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "pageLength": 10,
        "order": [[1, "desc"]] // Sort by Date Reported column by default
    });
});

function viewRequest(id) {
    $.ajax({
        url: `{{ route('ppfmo.service-requests.show', ':id') }}`.replace(':id', id),
        type: 'GET',
        beforeSend: function() {
            // Show loading spinner
            $('#requestDetails').html('<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $('#viewRequestModal').modal('show');
        },
        success: function(response) {
            $('#requestDetails').html(response);
        },
        error: function(xhr) {
            let errorMsg = 'Error loading request details.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            $('#requestDetails').html(`<div class="alert alert-danger m-3">${errorMsg}</div>`);
        }
    });
}

function deleteRequest(id) {
    if (confirm('Are you sure you want to delete this request? This action cannot be undone.')) {
        $.ajax({
            url: `{{ route('ppfmo.service-requests.destroy', ':id') }}`.replace(':id', id),
            type: 'DELETE',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting service request');
            }
        });
    }
}
</script>

@endsection