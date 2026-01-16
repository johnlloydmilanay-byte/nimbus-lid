{{-- 
  This file is intended to be included inside a <div class="modal-body"> 
  via your index.blade.php or loaded via AJAX.
--}}
<div class="vstack gap-3">
    
    {{-- NEW: Admin Quick Actions Section --}}
    <div class="card border-warning shadow-sm bg-light mb-2">
        <div class="card-header bg-warning bg-opacity-10 py-2 px-3 d-flex justify-content-between align-items-center">
            <strong class="text-warning-emphasis"><i class="fas fa-bolt"></i> Admin Quick Actions</strong>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('ppfmo.service-requests.quick-update', $serviceRequest) }}" method="POST">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label for="quick_status" class="small text-muted fw-bold">Update Status</label>
                        <select name="status" id="quick_status" class="form-select form-select-sm shadow-sm">
                            @foreach(\App\Models\PPFMO\ServiceRequest::STATUSES as $key => $statusLabel)
                                <option value="{{ $key }}" {{ $serviceRequest->status == $key ? 'selected' : '' }}>
                                    {{ $statusLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Only show Date Completed if status is 'Completed' --}}
                    <div class="col-md-4 d-none" id="quick_date_container">
                        <label for="quick_date_completed" class="small text-muted fw-bold">Date Done</label>
                        <input type="date" name="date_completed" id="quick_date_completed" 
                               class="form-control form-control-sm shadow-sm"
                               value="{{ $serviceRequest->date_completed ? $serviceRequest->date_completed->format('Y-m-d') : old('date_completed') }}">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100 shadow-sm">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Header Section: ID & Status --}}
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
        <div>
            <small class="text-muted d-block">Service Request</small>
            <h5 class="mb-0 fw-bold text-primary">
                #{{ $serviceRequest->request_number }}
            </h5>
        </div>
        <div class="text-end">
            @php
                $statusColors = [
                    'Pending' => 'warning',
                    'In Progress' => 'primary',
                    'Completed' => 'success',
                    'Cancelled' => 'danger'
                ];
            @endphp
            <span class="badge bg-{{ $statusColors[$serviceRequest->status] ?? 'secondary' }} fs-6 px-3 py-2">
                {{ $serviceRequest->status }}
            </span>
        </div>
    </div>

    {{-- Section: Request Details --}}
    <div>
        <h6 class="text-uppercase text-muted fs-6 fw-bold ls-1 mb-2">Request Information</h6>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="small text-muted mb-0">Request Type</label>
                <div><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $serviceRequest->request_type }}</span></div>
            </div>
            <div class="col-md-6">
                <label class="small text-muted mb-0">Date Reported</label>
                <div class="fw-bold">{{ $serviceRequest->date_reported->format('F d, Y') }}</div>
            </div>
            <div class="col-12">
                <label class="small text-muted mb-0">Specific Report</label>
                <div>{{ $serviceRequest->specific_report }}</div>
            </div>
            <div class="col-12">
                <label class="small text-muted mb-0">Location</label>
                <div>{{ $serviceRequest->location }}</div>
            </div>
        </div>
    </div>

    {{-- Section: Personnel & Tracking --}}
    <div>
        <h6 class="text-uppercase text-muted fs-6 fw-bold ls-1 mb-2">Personnel & Tracking</h6>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="small text-muted mb-0">Reported By</label>
                <div class="fw-bold">{{ $serviceRequest->reported_by }}</div>
            </div>
            <div class="col-md-6">
                <label class="small text-muted mb-0">Received By</label>
                <div>{{ $serviceRequest->received_by ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <label class="small text-muted mb-0">Endorsed To</label>
                <div>{{ $serviceRequest->endorsed_to ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <label class="small text-muted mb-0">Attested By</label>
                <div>{{ $serviceRequest->attested_by ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <label class="small text-muted mb-0">Date Completed</label>
                <div>{{ $serviceRequest->date_completed ? $serviceRequest->date_completed->format('F d, Y') : 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- Section: Remarks --}}
    <div>
        <h6 class="text-uppercase text-muted fs-6 fw-bold ls-1 mb-2">Remarks</h6>
        <div class="bg-light p-2 rounded border">
            {{ $serviceRequest->remarks ?? '<span class="text-muted fst-italic">No remarks provided</span>' }}
        </div>
    </div>

    {{-- Section: Meta Timestamps --}}
    <div class="border-top pt-2">
        <div class="row text-center small text-muted">
            <div class="col-6 border-end">
                <small>Created</small><br>
                {{ $serviceRequest->created_at->format('M d, Y') }}
            </div>
            <div class="col-6">
                <small>Updated</small><br>
                {{ $serviceRequest->updated_at->format('M d, Y') }}
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-end gap-2 mt-2 pt-2 border-top">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Close
        </button>
        <a href="{{ route('ppfmo.service-requests.edit', $serviceRequest) }}" 
           class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Full Edit
        </a>
    </div>
</div>

<script>
    // Simple script to handle the Quick Update Date visibility
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('quick_status');
        const dateContainer = document.getElementById('quick_date_container');
        const dateInput = document.getElementById('quick_date_completed');

        function toggleDate() {
            if (statusSelect.value === 'Completed') {
                dateContainer.classList.remove('d-none');
                dateInput.required = true;
            } else {
                dateContainer.classList.add('d-none');
                dateInput.required = false;
                dateInput.value = ''; // Clear value if hidden
            }
        }

        statusSelect.addEventListener('change', toggleDate);
        toggleDate(); // Initialize on load
    });
</script>