@extends('layouts.master')

@section('content')

<style>
    .form-label {
        font-weight: 600;
    }
</style>

<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Employee Information</h2>
        <a href="{{ route('hr.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
        </a>
    </div>

    <form action="{{ route('hr.store') }}" method="POST">
        @csrf

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- BASIC INFORMATION -->
        <h5 class="fw-bold mb-3">BASIC INFORMATION</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Time Keeping ID:</label>
                <input type="text" class="form-control" name="tk_id">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Last Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="lastname" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">First Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="firstname" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Middle Name:</label>
                <input type="text" class="form-control" name="middlename">
            </div>
            <div class="col-md-3">
                <label class="form-label">Suffix (Jr., Sr.)</label>
                <input type="text" class="form-control" name="suffix">
            </div>
            <div class="col-md-3">
                <label class="form-label">Prefix (Dr., Atty., Rev Fr)</label>
                <input type="text" class="form-control" name="prefix">
            </div>
            <div class="col-md-3">
                <label class="form-label">Extension (MBA, PHD, O.P.)</label>
                <input type="text" class="form-control" name="extension">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Gender: <span class="text-danger">*</span></label>
                <select class="form-select" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Birthdate: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="birthdate" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Birth Place: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="birth_place" required>
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- WORK INFORMATION -->
        <h5 class="fw-bold mb-3">WORK INFORMATION</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">College/Department: <span class="text-danger">*</span></label>
                <select class="form-select" name="department_id">                
                    @foreach($departments as $key=>$dept)
                        <option value="{{$dept->id}}" @if($key==0) selected @endif>{{$dept->code}} : {{$dept->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Designation: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="designation">
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Position: <span class="text-danger">*</span></label>
                <select class="form-select" name="position_id">                
                    @foreach($position as $key=>$pos)
                        <option value="{{$pos->id}}" @if($key==0) selected @endif>{{$pos->employee_position}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Employment Date: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="employment_date" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Rank (Faculty):</label>
                <select class="form-select" name="rank_faculty_id">      
                    <option value="" selected></option>          
                    @foreach($rank as $key=>$rank)
						<option value="{{$rank->id}}">{{$rank->employee_rank}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Employee Type: <span class="text-danger">*</span></label>
                <select class="form-select" name="employee_type_id">     
                    @foreach($type as $key=>$type)
						<option value="{{$type->id}}"@if($key==5) selected @endif>{{$type->employee_type}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Employment Status: <span class="text-danger">*</span></label>
                <select class="form-select" name="employment_status_id">     
                    @foreach($status as $key=>$status)
						<option value="{{$status->id}}" @if($key==2) selected @endif>{{$status->employee_status}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Save Button -->
        <div class="col-12">
            <button type="submit" class="btn btn-custom px-4 w-100">
                <i class="bi bi-save"></i> Save Applicant
            </button>
        </div>
    </form>
</div>

<!-- Include Success Modal -->
@include('Components.HumanResources.success-modal')

<!-- Auto-open modal if session exists -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('show_success_modal'))
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>

@endsection