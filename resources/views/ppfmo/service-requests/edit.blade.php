@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Edit Service Request #{{ $serviceRequest->request_number }}</h2>
        <a href="{{ route('ppfmo.service-requests.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Admin: Update Request Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('ppfmo.service-requests.update', $serviceRequest) }}" method="POST">
                @csrf
                @method('PUT')

                <h5 class="text-primary mt-3 mb-3 border-bottom pb-2">Original Request Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="request_number_display" class="form-label fw-bold">Request Number</label>
                            <input type="text" class="form-control bg-light" id="request_number_display" 
                                   value="{{ $serviceRequest->request_number }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date_reported" class="form-label fw-bold">Date Reported <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" class="form-control @error('date_reported') is-invalid @enderror" id="date_reported" 
                                       name="date_reported" value="{{ old('date_reported', $serviceRequest->date_reported->format('Y-m-d')) }}" required>
                            </div>
                            @error('date_reported')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Current Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach($statuses as $key => $status)
                                    <option value="{{ $key }}" 
                                        {{ old('status', $serviceRequest->status) == $key ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="request_type" class="form-label fw-bold">Request Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('request_type') is-invalid @enderror" id="request_type" name="request_type" required>
                                <option value="">Select Type</option>
                                @foreach($requestTypes as $key => $type)
                                    <option value="{{ $key }}" 
                                        {{ old('request_type', $serviceRequest->request_type) == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('request_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="specific_report" class="form-label fw-bold">Specific Report <span class="text-danger">*</span></label>
                            <select class="form-select @error('specific_report') is-invalid @enderror" id="specific_report" name="specific_report" required>
                                <option value="">Select Report Type</option>
                            </select>
                            @error('specific_report')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="location" class="form-label fw-bold">Location <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('location') is-invalid @enderror" id="location" name="location" 
                                      rows="2" required placeholder="Enter specific location or room number">{{ old('location', $serviceRequest->location) }}</textarea>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" 
                                      rows="3" placeholder="Additional details...">{{ old('remarks', $serviceRequest->remarks) }}</textarea>
                        </div>
                    </div>
                </div>

                <h5 class="text-primary mt-4 mb-3 border-bottom pb-2">Workflow & Personnel Assignment</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="reported_by" class="form-label fw-bold">Reported By</label>
                            <input type="text" class="form-control bg-light" id="reported_by" 
                                   value="{{ old('reported_by', $serviceRequest->reported_by) }}" readonly>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="received_by" class="form-label fw-bold">Received By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('received_by') is-invalid @enderror" id="received_by" 
                                   name="received_by" value="{{ old('received_by', $serviceRequest->received_by) }}" placeholder="Staff name">
                            @error('received_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="endorsed_to" class="form-label fw-bold">Endorsed To</label>
                            <select class="form-select @error('endorsed_to') is-invalid @enderror" id="endorsed_to" name="endorsed_to">
                                <option value="">Select Personnel</option>
                                @foreach($ppfmoUsers as $name => $displayName)
                                    <option value="{{ $name }}" 
                                        {{ old('endorsed_to', $serviceRequest->endorsed_to) === $name ? 'selected' : '' }}>
                                        {{ $displayName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('endorsed_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="attested_by" class="form-label fw-bold">Attested By</label>
                            <input type="text" class="form-control" id="attested_by" 
                                   name="attested_by" value="{{ old('attested_by', $serviceRequest->attested_by) }}" placeholder="Supervisor">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date_completed" class="form-label fw-bold">Date Completed</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" class="form-control @error('date_completed') is-invalid @enderror" id="date_completed" 
                                       name="date_completed" 
                                       value="{{ old('date_completed', $serviceRequest->date_completed ? $serviceRequest->date_completed->format('Y-m-d') : '') }}">
                            </div>
                            @error('date_completed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" id="date_completed_help">Required if status is Completed.</small>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3">
                    <a href="{{ route('ppfmo.service-requests.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const requestTypeSelect = document.getElementById('request_type');
        const specificReportSelect = document.getElementById('specific_report');
        
        // Define specific reports passed from controller
        const specificReports = @json($specificReports);
        
        // Store the current value from the database (or old input) to restore selection
        const currentSpecificReport = "{{ old('specific_report', $serviceRequest->specific_report) }}";
        
        // Function to update specific reports dropdown
        function updateSpecificReports() {
            const selectedType = requestTypeSelect.value;
            
            // Reset dropdown
            specificReportSelect.innerHTML = '<option value="">Select Report Type</option>';
            
            if (selectedType && specificReports[selectedType]) {
                specificReports[selectedType].forEach(function(report) {
                    const option = document.createElement('option');
                    option.value = report;
                    option.textContent = report;
                    
                    // Select the option if it matches the stored value
                    if (report === currentSpecificReport) {
                        option.selected = true;
                    }
                    
                    specificReportSelect.appendChild(option);
                });
            }
        }
        
        // Update specific reports when request type changes
        requestTypeSelect.addEventListener('change', function() {
            updateSpecificReports();
        });
        
        // Initialize on page load to populate options and select the correct value
        if (requestTypeSelect.value) {
            updateSpecificReports();
        }
        
        // Show/hide date completed requirement based on status
        const statusSelect = document.getElementById('status');
        const dateCompletedInput = document.getElementById('date_completed');
        const dateCompletedHelp = document.getElementById('date_completed_help');
        
        function toggleDateCompleted() {
            if (statusSelect.value === 'Completed') {
                dateCompletedInput.required = true;
                dateCompletedHelp.classList.remove('text-muted');
                dateCompletedHelp.classList.add('text-primary');
            } else {
                dateCompletedInput.required = false;
                dateCompletedHelp.classList.add('text-muted');
                dateCompletedHelp.classList.remove('text-primary');
            }
        }
        
        statusSelect.addEventListener('change', toggleDateCompleted);
        toggleDateCompleted(); // Initial check
    });
</script>
@endsection