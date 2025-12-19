@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Laboratory Reservations Dashboard</h2>
        <a href="{{ route('lid.reservations.create') }}" class="btn btn-primary btn-sm mt-2 mt-md-0">
            <i class="bi bi-plus-circle"></i> New Reservation
        </a>
    </div><br>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Reservations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReservations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
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
                                Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedReservations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingReservations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Special Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pdeaRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Reservations</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="bi bi-funnel"></i> Toggle Filters
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('lid.reservations.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Borrower Name</label>
                            <input type="text" name="borrower_name" class="form-control" value="{{ request('borrower_name') }}" placeholder="Search by name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Borrower Type</label>
                            <select name="borrower_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Faculty Member" {{ request('borrower_type') == 'Faculty Member' ? 'selected' : '' }}>Faculty Member</option>
                                <option value="Student" {{ request('borrower_type') == 'Student' ? 'selected' : '' }}>Student</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" value="{{ request('reference_number') }}" placeholder="Search by reference">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-9 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm me-2">Apply Filters</button>
                            <a href="{{ route('lid.reservations.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Laboratory Reservations</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reservationsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Borrower Name</th>
                            <th>Type</th>
                            <th>Faculty Reference</th>
                            <th>Purpose</th>
                            <th>Date Requested</th>
                            <th>Reservation Dates</th>
                            <th>Status</th>
                            <th>Special Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $reservation)
                            <tr>
                                <td>{{ $reservation->reference_number }}</td>
                                <td>{{ $reservation->borrower_name }}</td>
                                <td>
                                    <span class="badge bg-{{ $reservation->borrower_type == 'Faculty Member' ? 'primary' : 'info' }}">
                                        {{ $reservation->borrower_type }}
                                    </span>
                                </td>
                                <td>
                                    @if($reservation->faculty_reference_id)
                                        <a href="#" onclick="viewReservation({{ $reservation->faculty_reference_id }})">{{ $reservation->facultyReservation->reference_number }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $reservation->purpose }}</td>
                                <td>{{ \Carbon\Carbon::parse($reservation->date_requested)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reservation->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('M d, Y') }}</td>
                                <td>
                                    @if($reservation->status == 'Approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($reservation->status == 'Pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($reservation->status == 'Rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $reservation->status ?? 'Draft' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($reservation->pdeaRequests->count() > 0)
                                            <span class="badge bg-danger mb-1">PDEA ({{ $reservation->pdeaRequests->count() }})</span>
                                        @endif
                                        @if($reservation->equipmentRequests->count() > 0)
                                            <span class="badge bg-warning mb-1">Equipment ({{ $reservation->equipmentRequests->count() }})</span>
                                        @endif
                                        @if($reservation->glasswareRequests->count() > 0)
                                            <span class="badge bg-info mb-1">Glassware ({{ $reservation->glasswareRequests->count() }})</span>
                                        @endif
                                        @if($reservation->pdeaRequests->count() == 0 && $reservation->equipmentRequests->count() == 0 && $reservation->glasswareRequests->count() == 0)
                                            <span class="badge bg-secondary">None</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Details" onclick="viewReservation({{ $reservation->id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Edit" onclick="editReservation({{ $reservation->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if($reservation->status == 'Pending')
                                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Approve" onclick="updateStatus({{ $reservation->id }}, 'Approved')">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Reject" onclick="updateStatus({{ $reservation->id }}, 'Rejected')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete" onclick="deleteReservation({{ $reservation->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No reservations found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Reservation Modal -->
<div class="modal fade" id="viewReservationModal" tabindex="-1" aria-labelledby="viewReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReservationModalLabel">Reservation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reservationDetails">
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
    $('#reservationsTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "pageLength": 10
    });
});

function viewReservation(id) {
    $.ajax({
        url: `{{ route('lid.reservations.show', ':id') }}`.replace(':id', id),
        type: 'GET',
        beforeSend: function() {
            // Show a loading indicator
            $('#reservationDetails').html('<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $('#viewReservationModal').modal('show');
        },
        success: function(response) {
            $('#reservationDetails').html(response);
        },
        error: function(xhr) {
            // Show a more detailed error message
            let errorMsg = 'Error loading reservation details.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            $('#reservationDetails').html(`<div class="alert alert-danger m-3">${errorMsg}</div>`);
        }
    });
}

function editReservation(id) {
    window.location.href = `/lid/reservations/${id}/edit`;
}

function updateStatus(id, status) {
    if (confirm(`Are you sure you want to ${status.toLowerCase()} this reservation?`)) {
        $.ajax({
            url: `/lid/reservations/${id}/status`,
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'status': status
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error updating reservation status');
            }
        });
    }
}

function deleteReservation(id) {
    if (confirm('Are you sure you want to delete this reservation? This action cannot be undone.')) {
        $.ajax({
            url: `/lid/reservations/${id}`,
            type: 'DELETE',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting reservation');
            }
        });
    }
}
</script>

@endsection