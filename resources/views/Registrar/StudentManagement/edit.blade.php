@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h3 class="mt-4 fw-bold mb-4">Student Management</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('registrar.studentmanagement.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <button id="editButton" type="button" class="btn btn-custom">
                <i class="bi bi-pencil-square me-1"></i> Edit Information
            </button>

            @if(Str::endsWith($collegeRequirements?->application_number, 'C')) <!-- College -->
                <button id="checkListButtonCollege" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checklistModalCollege">
                    <i class="bi bi-card-checklist me-1"></i> Requirement Checklist
                </button>
            @elseif(Str::endsWith($shsRequirements?->application_number, 'S')) <!-- SHS -->
                <button id="checkListButtonShs" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checklistModalShs">
                    <i class="bi bi-card-checklist me-1"></i> Requirement Checklist
                </button>
            @elseif(Str::endsWith($jhsRequirements?->application_number, 'J')) <!-- SHS -->
                <button id="checkListButtonJhs" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checklistModalJhs">
                    <i class="bi bi-card-checklist me-1"></i> Requirement Checklist
                </button>
            @elseif(Str::endsWith($pseRequirements?->application_number, 'P')) <!-- PSE -->
                <button id="checkListButtonPse" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checklistModalPse">
                    <i class="bi bi-card-checklist me-1"></i> Requirement Checklist
                </button>
            @endif
        </div>
    </div>

    <div class="card-body">

        <form id="studentForm" action="{{ route('registrar.studentmanagement.update', $student->application_number) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Basic Information -->
            <h5><strong>Basic Information</strong></h5>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="lastname" value="{{ $student->lastname }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" name="firstname" value="{{ $student->firstname }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Middle Name</label>
                    <input type="text" name="middlename" value="{{ $student->middlename }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="department_id" class="form-label fw-semibold">College / Department</label>
                    <select name="department_id" id="department_id" class="form-select bg-light" disabled required>
                        <option value="" disabled>Select Department</option>
                        @foreach($department as $dept)
                            <option value="{{ $dept->id }}"
                                {{ (old('department_id', $student->departmentRelation->id ?? $student->department_id) == $dept->id) ? 'selected' : '' }}>
                                {{ $dept->code }} : {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="program_id" class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                    <select name="program_id" id="program_id" class="form-select bg-light" disabled required>
                        <option value="" selected disabled>Select Program</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Student Status</label>
                     <select name="studentstatus_id" id="studentstatus_id" class="form-select bg-light" disabled>
                        <option value=""></option>
                        @foreach($studentstatus as $studentstat)
                            <option value="{{ $studentstat->id }}"
                                {{ $student->studentstatus_id == $studentstat->id ? 'selected' : '' }}>
                                {{ $studentstat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Grade / Year Level</label>
                     <select name="year_level_id" id="year_level_id" class="form-select bg-light" disabled>
                        <option value=""></option>
                        @foreach($yearLevelDetails as $yl)
                            <option value="{{ $yl->id }}"
                                {{ $student->year_level_id == $yl->id ? 'selected' : '' }}>
                                {{ $yl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Year Entry</label>
                    <input type="text" value="{{ $student->year_entry ?? '' }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="gender" class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                    <select name="gender" id="gender" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->gender) ? 'selected' : '' }}>Select Gender</option>
                        <option value="Male" {{ old('gender', $student->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $student->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" value="{{ $student->mobile_no ?? '' }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="text" value="{{ $student->email ?? '' }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Siblings</label>
                    <input type="text" value="{{ $student->no_of_siblings ?? '' }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date of Birth</label>
                    <input type="date" value="{{ \Carbon\Carbon::parse($student->dob)->format('Y-m-d') }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Birthplace</label>
                    <input type="text" value="{{ $student->birthplace ?? '' }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Religion</label>
                    <input type="text" value="{{ $student->religion ?? '' }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nationality</label>
                    <input type="text" value="{{ $student->nationality ?? '' }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <hr class="border-2 border-warning mt-4">

            <!-- Permanent Address -->
            <h5><strong>Permanent Address</strong></h5>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="province_id" class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
                    <select name="province_id" id="province_id" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ old('province_id', $student->province_id ?? '') == '' ? 'selected' : '' }}>Select Province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ old('province_id', $student->province_id ?? '') == $prov->id ? 'selected' : '' }}>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="city_id" class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                    <select name="city_id" id="city_id" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ old('city_id', $student->city_id ?? '') == '' ? 'selected' : '' }}>Select City / Town</option>
                        @foreach($towns as $town)
                            <option value="{{ $town->id }}" {{ old('city_id', $student->city_id ?? '') == $town->id ? 'selected' : '' }}>
                                {{ $town->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="barangay" class="form-label fw-semibold">Barangay</label>
                    <input type="text" name="barangay" id="barangay" value="{{ old('barangay', $student->barangay ?? '') }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-3">
                    <label for="staying_in" class="form-label fw-semibold">Staying in</label>
                    <input type="text" name="staying_in" id="staying_in" value="{{ old('staying_in', $student->staying_in ?? '') }}" class="form-control bg-light" disabled>
                </div>
            </div><br>

            <!-- Current Address -->
            <h5><strong>Current Address</strong></h5>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="current_province_id" class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
                    <select name="current_province_id" id="current_province_id" class="form-select bg-light" disabled>
                        <option value="" disabled {{ old('current_province_id', $student->current_province_id ?? '') == '' ? 'selected' : '' }}>Select Province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ old('current_province_id', $student->current_province_id ?? '') == $prov->id ? 'selected' : '' }}>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="current_city_id" class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                    <select name="current_city_id" id="current_city_id" class="form-select bg-light" disabled>
                        <option value="" disabled {{ old('current_city_id', $student->current_city_id ?? '') == '' ? 'selected' : '' }}>Select City / Town</option>
                        @foreach($towns as $town)
                            <option value="{{ $town->id }}" {{ old('current_city_id', $student->current_city_id ?? '') == $town->id ? 'selected' : '' }}>
                                {{ $town->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Barangay</label>
                    <input type="text" value="{{ $student->current_barangay ?? '' }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <hr class="border-2 border-warning mt-4">

            <!-- Educational Background -->
            <h5><strong>Educational Background</strong></h5>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Elementary School</label>
                    <input type="text" value="{{ $student->elem_school_name }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" value="{{ $student->elem_address }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">S.Y. Attended</label>
                    <input type="text" value="{{ $student->elem_school_year_attended }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Junior High School</label>
                    <input type="text" value="{{ $student->jhs_name }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" value="{{ $student->jhs_address }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">S.Y. Attended</label>
                    <input type="text" value="{{ $student->jhs_year_attended }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Senior High School</label>
                    <input type="text" value="{{ $student->shs_name }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" value="{{ $student->shs_address }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">S.Y. Attended</label>
                    <input type="text" value="{{ $student->shs_year_attended }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Award(s) Recognition Received</label>
                    <input type="text" value="{{ $student->awards }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Organizations/Club previously affiliated</label>
                    <input type="text" value="{{ $student->organization }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Position</label>
                    <input type="text" value="{{ $student->position }}" class="form-control bg-light" disabled>
                </div>
            </div>

            <hr class="border-2 border-warning mt-4">

            <!-- Family Background -->
            <h5><strong>Family Background</strong></h5>

            <div class="row g-3 mt-2">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Father Name</label>
                    <input type="text" value="{{ $student->father_name }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Occupation</label>
                    <input type="text" value="{{ $student->father_occupation }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" value="{{ $student->father_age }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Highes Educational Attainment</label>
                    <input type="text" value="{{ $student->father_education }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" value="{{ $student->father_mobile_no }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="father_status" id="father_status" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->father_status) ? 'selected' : '' }}>Select Gender</option>
                        <option value="Living" {{ old('father_status', $student->father_status ?? '') == 'Living' ? 'selected' : '' }}>Living</option>
                        <option value="Deceased" {{ old('father_status', $student->father_status ?? '') == 'Deceased' ? 'selected' : '' }}>Deceased</option>
                    </select>
                </div>
                <div class="col-md-10">
                    <label class="form-label fw-semibold">Place of Work</label>
                    <input type="text" value="{{ $student->father_placework }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Work Status</label>
                    <select name="father_ofw_status" id="father_ofw_status" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->father_ofw_status) ? 'selected' : '' }}>Select Gender</option>
                        <option value="OFW" {{ old('father_ofw_status', $student->father_ofw_status ?? '') == 'OFW' ? 'selected' : '' }}>OFW</option>
                        <option value="Not OFW" {{ old('father_ofw_status', $student->father_ofw_status ?? '') == 'Not OFW' ? 'selected' : '' }}>Not OFW</option>
                    </select>
                </div>
            </div><br>
            
            <div class="w-100 d-block" style="height: 2px; background-color: #dee2e6;"></div>

            <div class="row g-3 mt-2">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Mother Name</label>
                    <input type="text" value="{{ $student->mother_name }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Occupation</label>
                    <input type="text" value="{{ $student->mother_occupation }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" value="{{ $student->mother_age }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Highes Educational Attainment</label>
                    <input type="text" value="{{ $student->mother_education }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" value="{{ $student->mother_mobile_no }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="mother_status" id="mother_status" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->mother_status) ? 'selected' : '' }}>Select Gender</option>
                        <option value="Living" {{ old('mother_status', $student->mother_status ?? '') == 'Living' ? 'selected' : '' }}>Living</option>
                        <option value="Deceased" {{ old('mother_status', $student->mother_status ?? '') == 'Deceased' ? 'selected' : '' }}>Deceased</option>
                    </select>
                </div>
                <div class="col-md-10">
                    <label class="form-label fw-semibold">Place of Work</label>
                    <input type="text" value="{{ $student->mother_placework }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Work Status</label>
                    <select name="mother_ofw_status" id="mother_ofw_status" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->mother_ofw_status) ? 'selected' : '' }}>Select Gender</option>
                        <option value="OFW" {{ old('mother_ofw_status', $student->mother_ofw_status ?? '') == 'OFW' ? 'selected' : '' }}>OFW</option>
                        <option value="Not OFW" {{ old('mother_ofw_status', $student->mother_ofw_status ?? '') == 'Not OFW' ? 'selected' : '' }}>Not OFW</option>
                    </select>
                </div>
            </div><br>
            
            <div class="w-100 d-block" style="height: 2px; background-color: #dee2e6;"></div>

            <div class="row g-3 mt-2">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Guardian Name</label>
                    <input type="text" value="{{ $student->guardian_name }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Occupation</label>
                    <input type="text" value="{{ $student->guardian_occupation }}" class="form-control bg-light" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" value="{{ $student->guardian_number }}" class="form-control bg-light" disabled>
                </div>
            </div><br>
            
            <div class="w-100 d-block" style="height: 2px; background-color: #dee2e6;"></div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Marital Status of Parent</label>
                    <select name="parents_marital_status" id="parents_marital_status" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->parents_marital_status) ? 'selected' : '' }}>Select Status</option>
                        <option value="Living together" {{ old('parents_marital_status', $student->parents_marital_status ?? '') == 'Living together' ? 'selected' : '' }}>Living together</option>
                        <option value="Separated" {{ old('parents_marital_status', $student->parents_marital_status ?? '') == 'Separated' ? 'selected' : '' }}>Separated</option>
                        <option value="Single Parent" {{ old('parents_marital_status', $student->parents_marital_status ?? '') == 'Single Parent' ? 'selected' : '' }}>Single Parent</option>
                        <option value="Widow/Widower" {{ old('parents_marital_status', $student->parents_marital_status ?? '') == 'Widow/Widower' ? 'selected' : '' }}>Widow/Widower</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Monthly Family Income</label>
                    <select id="monthly_family_income" name="monthly_family_income" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->monthly_family_income) ? 'selected' : '' }}>Select Income Range</option>
                        <option value="below P10,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'below P10,000' ? 'selected' : '' }}>Below P10,000</option>
                        <option value="P10,000 - below P21,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P10,000 - below P21,000' ? 'selected' : '' }}>P10,000 - below P21,000</option>
                        <option value="P21,000 - below P31,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P21,000 - below P31,000' ? 'selected' : '' }}>P21,000 - below P31,000</option>
                        <option value="P31,000 - below P41,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P31,000 - below P41,000' ? 'selected' : '' }}>P31,000 - below P41,000</option>
                        <option value="P41,000 - below P51,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P41,000 - below P51,000' ? 'selected' : '' }}>P41,000 - below P51,000</option>
                        <option value="P51,000 - below P61,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P51,000 - below P61,000' ? 'selected' : '' }}>P51,000 - below P61,000</option>
                        <option value="P61,000 - below P71,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P61,000 - below P71,000' ? 'selected' : '' }}>P61,000 - below P71,000</option>
                        <option value="P71,000 - below P81,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P71,000 - below P81,000' ? 'selected' : '' }}>P71,000 - below P81,000</option>
                        <option value="P81,000 - below P91,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P81,000 - below P91,000' ? 'selected' : '' }}>P81,000 - below P91,000</option>
                        <option value="P91,000 - below P101,000" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P91,000 - below P101,000' ? 'selected' : '' }}>P91,000 - below P101,000</option>
                        <option value="P101,000 and above" {{ old('monthly_family_income', $student->monthly_family_income ?? '') == 'P101,000 and above' ? 'selected' : '' }}>P101,000 and above</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Family Living Arrangement</label>
                    <select id="family_living_arrangement" name="family_living_arrangement" class="form-select bg-light" disabled required>
                        <option value="" disabled {{ empty($student->family_living_arrangement) ? 'selected' : '' }}>Select Living Arrangement</option>
                        <option value="Living with parents" {{ old('family_living_arrangement', $student->family_living_arrangement ?? '') == 'Living with parents' ? 'selected' : '' }}>Living with parents</option>
                        <option value="Living with grandparents" {{ old('family_living_arrangement', $student->family_living_arrangement ?? '') == 'Living with grandparents' ? 'selected' : '' }}>Living with grandparents</option>
                        <option value="Living with sibling/s" {{ old('family_living_arrangement', $student->family_living_arrangement ?? '') == 'Living with sibling/s' ? 'selected' : '' }}>Living with sibling/s</option>
                        <option value="Living alone" {{ old('family_living_arrangement', $student->family_living_arrangement ?? '') == 'Living alone' ? 'selected' : '' }}>Living alone</option>
                        <option value="Living with relatives (please specify)" {{ old('family_living_arrangement', $student->family_living_arrangement ?? '') == 'Living with relatives (please specify)' ? 'selected' : '' }}>Living with relatives (please specify)</option>
                        <option value="Living with others (please specify)" {{ old('family_living_arrangement', $student->family_living_arrangement ?? '') == 'Living with others (please specify)' ? 'selected' : '' }}>Living with others (please specify)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div id="others_specify_box" class="mt-2" style="display: {{ in_array(old('family_living_arrangement', $student->family_living_arrangement ?? ''), ['Living with relatives (please specify)', 'Living with others (please specify)']) ? 'block' : 'none' }};">
                        <label for="others_specify" class="d-none d-md-block">-</label>
                        <input type="text" id="others_specify" name="others_specify" class="form-control" placeholder="Please specify" value="{{ old('others_specify', $student->others_specify ?? '') }}">
                    </div>
                </div>

            </div>

            <hr class="border-2 border-warning mt-4">

            <!-- Other Info -->
            <h5><strong>Other Information</strong></h5>

<div class="row g-3 mt-2">
    <div class="col-md-6">
        <label for="is_pwd" class="form-label fw-semibold">Is the student with Disability (PWD)</label>
        <select id="is_pwd" name="is_pwd" class="form-select" required>
            <option value="" disabled {{ old('is_pwd', $student->is_pwd ?? '') === null ? 'selected' : '' }}>Select PWD Status</option>
            <option value="1" {{ old('is_pwd', $student->is_pwd ?? '') == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('is_pwd', $student->is_pwd ?? '') == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>
    <div class="col-md-6">
        <div id="is_pwd_specify_box" class="mt-2" style="display: {{ old('is_pwd', $student->is_pwd ?? '') == 1 ? 'block' : 'none' }};">
            <label for="is_pwd_yes" class="d-none d-md-block">-</label>
            <input type="text" id="is_pwd_yes" name="is_pwd_yes" class="form-control" placeholder="Please specify" value="{{ old('is_pwd_yes', $student->is_pwd_yes ?? '') }}">
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-6">
        <label for="is_scholar" class="form-label fw-semibold">Is the student a scholar? <span class="text-danger">*</span></label>
        <select id="is_scholar" name="is_scholar" class="form-select" required>
            <option value="" disabled {{ old('is_scholar', $student->is_scholar ?? '') === null ? 'selected' : '' }}>Select Scholar Status</option>
            <option value="1" {{ old('is_scholar', $student->is_scholar ?? '') == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('is_scholar', $student->is_scholar ?? '') == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>
    <div class="col-md-6">
        <div id="is_scholar_type_box" style="display: {{ old('is_scholar', $student->is_scholar ?? '') == 1 ? 'block' : 'none' }};">
            <label for="is_scholar_type" class="form-label">If yes, which type of scholarship? <span class="text-danger">*</span></label>
            <select id="is_scholar_type" name="is_scholar_type" class="form-select">
                <option value="" disabled {{ old('is_scholar_type', $student->is_scholar_type ?? '') == '' ? 'selected' : '' }}>Select Type</option>
                <option value="Entrance" {{ old('is_scholar_type', $student->is_scholar_type ?? '') == 'Entrance' ? 'selected' : '' }}>Entrance</option>
                <option value="ESC" {{ old('is_scholar_type', $student->is_scholar_type ?? '') == 'ESC' ? 'selected' : '' }}>ESC</option>
                <option value="Special Grant" {{ old('is_scholar_type', $student->is_scholar_type ?? '') == 'Special Grant' ? 'selected' : '' }}>Special Grant</option>
                <option value="Voucher Program" {{ old('is_scholar_type', $student->is_scholar_type ?? '') == 'Voucher Program' ? 'selected' : '' }}>Voucher Program</option>
                <option value="Others" {{ old('is_scholar_type', $student->is_scholar_type ?? '') == 'Others' ? 'selected' : '' }}>Others (pls. specify)</option>
            </select>
        </div>
        <div id="is_scholar_others_box" class="mt-2" style="display: {{ old('is_scholar_type', $student->is_scholar_type ?? '') == 'Others' ? 'block' : 'none' }};">
            <input type="text" id="is_scholar_yes_others" name="is_scholar_yes_others" class="form-control" placeholder="If yes, please specify" value="{{ old('is_scholar_yes_others', $student->is_scholar_yes_others ?? '') }}">
        </div>
    </div>
</div>


            <div class="mt-4 d-none d-flex justify-content-end gap-2" id="saveCancelButtons">
                <button type="button" id="cancelEdit" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Cancel</button>
                <button type="submit" class="btn btn-custom"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
            </div>
        </form>

    </div>
</div>

@include('Components.Registrar.edit-success-modal')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.getElementById('editButton');
    const cancelButton = document.getElementById('cancelEdit');
    const saveCancelButtons = document.getElementById('saveCancelButtons');
    const form = document.getElementById('studentForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    const departmentSelect = document.getElementById('department_id');
    const programSelect = document.getElementById('program_id');
    let isEditable = false;

    // Disable all fields initially
    inputs.forEach(field => {
        field.setAttribute('disabled', true);
        field.classList.add('bg-light');
    });

    // Load programs dynamically
    function loadPrograms(departmentId, selectedProgramId = null) {
        programSelect.innerHTML = '<option value="" selected disabled>Loading...</option>';

        fetch(`{{ url('/registrar/get-programs') }}/${departmentId}`)
            .then(res => res.json())
            .then(data => {
                programSelect.innerHTML = '<option value="" selected disabled>Select Program</option>';
                data.forEach(program => {
                    const opt = document.createElement('option');
                    opt.value = program.id;
                    opt.textContent = `${program.code} : ${program.name}`;
                    if (selectedProgramId && program.id == selectedProgramId) opt.selected = true;
                    programSelect.appendChild(opt);
                });

                if (!isEditable) programSelect.setAttribute('disabled', true);
            })
            .catch(() => {
                programSelect.innerHTML = '<option value="" disabled>Error loading programs</option>';
            });
    }

    // Preload current program
    const existingDepartmentId = "{{ $student->departmentRelation->id ?? $student->department_id ?? '' }}";
    const existingProgramId = "{{ $student->programRelation->id ?? $student->program_id ?? '' }}";
    if (existingDepartmentId) loadPrograms(existingDepartmentId, existingProgramId);

    // When Edit Information is clicked
    editButton.addEventListener('click', function () {
        isEditable = true;
        inputs.forEach(field => {
            field.removeAttribute('disabled');
            field.classList.remove('bg-light');
        });

        saveCancelButtons.classList.remove('d-none');
        editButton.disabled = true;

        // Disable Requirement Checklist button College
        const checkListButton = document.getElementById('checkListButtonCollege');
        if (checkListButton) checkListButton.disabled = true;

        // Disable Requirement Checklist button Shs
        const checkListButtonShs = document.getElementById('checkListButtonShs');
        if (checkListButtonShs) checkListButtonShs.disabled = true;

        // Disable Requirement Checklist button Jhs
        const checkListButtonJhs = document.getElementById('checkListButtonJhs');
        if (checkListButtonJhs) checkListButtonJhs.disabled = true;

        // Disable Requirement Checklist button Pse
        const checkListButtonPse = document.getElementById('checkListButtonPse');
        if (checkListButtonPse) checkListButtonPse.disabled = true;
    });

    // When Cancel is clicked
    cancelButton.addEventListener('click', function () {
        isEditable = false;
        inputs.forEach(field => {
            field.setAttribute('disabled', true);
            field.classList.add('bg-light');
        });

        saveCancelButtons.classList.add('d-none');
        editButton.disabled = false;

        // Re-enable Requirement Checklist button College
        const checkListButton = document.getElementById('checkListButtonCollege');
        if (checkListButton) checkListButton.disabled = false;

        // Re-enable Requirement Checklist button Shs
        const checkListButtonShs = document.getElementById('checkListButtonShs');
        if (checkListButtonShs) checkListButtonShs.disabled = false;

        // Re-enable Requirement Checklist button Jhs
        const checkListButtonJhs = document.getElementById('checkListButtonJhs');
        if (checkListButtonJhs) checkListButtonJhs.disabled = false;

        // Re-enable Requirement Checklist button Pse
        const checkListButtonPse = document.getElementById('checkListButtonPse');
        if (checkListButtonPse) checkListButtonPse.disabled = false;

        // Reload current program/department data
        if (existingDepartmentId) loadPrograms(existingDepartmentId, existingProgramId);
    });

    // Reload programs when department changes (only editable mode)
    departmentSelect.addEventListener('change', function () {
        if (isEditable && this.value) {
            loadPrograms(this.value);
        }
    });
});
</script>

<!-- Family Living Arrangments == Others -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const familySelect = document.getElementById('family_living_arrangement');
    const othersBox = document.getElementById('others_specify_box');

    function toggleOthersBox() {
        const value = familySelect.value;
        if (['Living with relatives (please specify)', 'Living with others (please specify)'].includes(value)) {
            othersBox.style.display = 'block';
        } else {
            othersBox.style.display = 'none';
            document.getElementById('others_specify').value = '';
        }
    }

    familySelect.addEventListener('change', toggleOthersBox);

    // Initialize on page load
    toggleOthersBox();
});
</script>

<!-- is_pwd == yes  -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isPwdSelect = document.getElementById('is_pwd');
    const specifyBox = document.getElementById('is_pwd_specify_box');

    function toggleSpecifyBox() {
        if (isPwdSelect.value === '1') {
            specifyBox.style.display = 'block';
        } else {
            specifyBox.style.display = 'none';
            document.getElementById('is_pwd_yes').value = '';
        }
    }

    isPwdSelect.addEventListener('change', toggleSpecifyBox);

    // Initialize visibility on page load
    toggleSpecifyBox();
});
</script>

<!-- is_scholar == yes && is_scholar_type == others -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isScholarSelect = document.getElementById('is_scholar');
    const scholarTypeBox = document.getElementById('is_scholar_type_box');
    const scholarTypeSelect = document.getElementById('is_scholar_type');
    const scholarOthersBox = document.getElementById('is_scholar_others_box');
    const scholarOthersInput = document.getElementById('is_scholar_yes_others');

    function toggleScholarFields() {
        if (isScholarSelect.value === '1') {
            scholarTypeBox.style.display = 'block';
        } else {
            scholarTypeBox.style.display = 'none';
            scholarTypeSelect.value = '';
            scholarOthersBox.style.display = 'none';
            scholarOthersInput.value = '';
        }
    }

    function toggleScholarOthers() {
        if (scholarTypeSelect.value === 'Others') {
            scholarOthersBox.style.display = 'block';
        } else {
            scholarOthersBox.style.display = 'none';
            scholarOthersInput.value = '';
        }
    }

    isScholarSelect.addEventListener('change', toggleScholarFields);
    scholarTypeSelect.addEventListener('change', toggleScholarOthers);

    // Initialize visibility on page load
    toggleScholarFields();
    toggleScholarOthers();
});
</script>

@php $lastChar = substr($student->application_number, -1); @endphp

<!-- College Requirements Modal -->
@if($lastChar === 'C' && isset($collegeRequirements))
    @component('Registrar.StudentManagement.checklist-modal-college', ['collegeRequirements' => $collegeRequirements]) @endcomponent
@endif

<!-- SHS Requirements Modal -->
@if($lastChar === 'S' && isset($shsRequirements))
    @component('Registrar.StudentManagement.checklist-modal-shs', ['shsRequirements' => $shsRequirements]) @endcomponent
@endif

<!-- JHS Requirements Modal -->
@if($lastChar === 'J' && isset($jhsRequirements))
    @component('Registrar.StudentManagement.checklist-modal-jhs', ['jhsRequirements' => $jhsRequirements]) @endcomponent
@endif

<!-- PSE Requirements Modal -->
@if($lastChar === 'P' && isset($pseRequirements))
    @component('Registrar.StudentManagement.checklist-modal-pse', ['pseRequirements' => $pseRequirements]) @endcomponent
@endif

<script>
    document.getElementById('checkListButtonCollege').addEventListener('click', function() {
        console.log("Checklist modal opened!");
    });
    document.getElementById('checkListButtonShs').addEventListener('click', function() {
        console.log("Checklist modal opened!");
    });
    document.getElementById('checkListButtonJhs').addEventListener('click', function() {
        console.log("Checklist modal opened!");
    });
    document.getElementById('checkListButtonPse').addEventListener('click', function() {
        console.log("Checklist modal opened!");
    });
</script>
@endsection
