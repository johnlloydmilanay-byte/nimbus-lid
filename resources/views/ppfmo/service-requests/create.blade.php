@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Submit a Service Request</h2>
        <a href="{{ route('ppfmo.service-requests.index') }}" class="btn btn-secondary btn-sm mt-2 mt-md-0">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Request Details</h6>
        </div>
        <div class="card-body">
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ppfmo.service-requests.store') }}" method="POST">
                @csrf

                <h5 class="text-primary mt-3 mb-3 border-bottom pb-2">Incident Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_reported" class="form-label fw-bold">Date Reported <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_reported') is-invalid @enderror" id="date_reported" 
                                   name="date_reported" value="{{ old('date_reported', date('Y-m-d')) }}" required>
                            @error('date_reported')
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
                                    <option value="{{ $key }}" {{ old('request_type') == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('request_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="specific_report" class="form-label fw-bold">Specific Issue/Report <span class="text-danger">*</span></label>
                            <select class="form-select @error('specific_report') is-invalid @enderror" id="specific_report" name="specific_report" required>
                                <option value="">Select Request Type First</option>
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
                                      rows="2" required placeholder="Enter specific location (e.g., Room 304, Main Lobby)">{{ old('location') }}</textarea>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Detailed Description</label>
                            <textarea class="form-control" id="remarks" name="remarks" 
                                      rows="4" placeholder="Please provide more details about the issue...">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>

                <h5 class="text-primary mt-4 mb-3 border-bottom pb-2">Contact Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reported_by" class="form-label fw-bold">Reported By (Name) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('reported_by') is-invalid @enderror" id="reported_by" 
                                   name="reported_by" value="{{ old('reported_by') }}" required placeholder="Enter your full name">
                            @error('reported_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Removed Received By, Endorsed To, Attested To, Date Completed, and Status as these are for Admin use only -->
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3">
                    <a href="{{ route('ppfmo.service-requests.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-paper-plane"></i> Submit Request
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
        
        // Update specific reports when request type changes
        requestTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            specificReportSelect.innerHTML = '<option value="">Select Specific Issue</option>';
            
            if (selectedType && specificReports[selectedType]) {
                specificReports[selectedType].forEach(function(report) {
                    const option = document.createElement('option');
                    option.value = report;
                    option.textContent = report;
                    specificReportSelect.appendChild(option);
                });
            }
        });
        
        // Initialize if there's already a selected value (on validation error)
        if (requestTypeSelect.value) {
            requestTypeSelect.dispatchEvent(new Event('change'));
            
            // Set previously selected specific report
            const oldValue = "{{ old('specific_report') }}";
            if (oldValue) {
                setTimeout(function() {
                    specificReportSelect.value = oldValue;
                }, 100);
            }
        }
        
        // Removed status logic as clients do not set status
    });
</script>
@endsection