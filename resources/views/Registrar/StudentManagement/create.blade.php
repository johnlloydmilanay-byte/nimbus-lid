@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h3 class="mt-4 fw-bold mb-4">Add Student Information</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('registrar.studentmanagement.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addStudentModal"><i class="bi bi-search"></i> Search Student</button>
        </div>
    </div>

    <div class="card-body">

        <!-- Student Information Form -->
        <form action="{{ route('registrar.studentmanagement.store') }}" method="POST" id="studentForm">
            @csrf

            <div class="row g-3 mt-2">
                <h5><strong>Basic Information</strong></h5>
                <div class="col-md-6">
                    <label for="application_number" class="form-label fw-semibold">Application Number</label>
                    <input type="text" name="application_number" id="application_number" class="form-control bg-light text-dark" required readonly>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="lastname" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="lastname" id="lastname" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="firstname" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="firstname" id="firstname" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="middlename" class="form-label fw-semibold">Middle Name <span class="text-danger">*</span></label>
                        <input type="text" name="middlename" id="middlename" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="department_id" class="form-label fw-semibold">College / Department <span class="text-danger">*</span></label>
                    <select name="department_id" id="department_id" class="form-select" required>
                        <option value="" selected disabled>Select Department</option>
                        @foreach($department as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->code }} : {{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="program_id" class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                    <select name="program_id" id="program_id" class="form-select" required>
                        <option value="" selected disabled>Select Program</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="studentstatus_id" class="form-label fw-semibold">Student Status <span class="text-danger">*</span></label>
                    <select name="studentstatus_id" id="studentstatus_id" class="form-select">
                        <option value=""></option>
                        @foreach($studentstatus as $studentstat)
                            <option value="{{ $studentstat->id }}">{{ $studentstat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year_level_id" class="form-label fw-semibold">Grade / Year Level <span class="text-danger">*</span></label>
                    <select name="year_level_id" id="year_level_id" class="form-select" required>
                        <option value=""></option>
                        @foreach($yearLevelDetails as $yl)
                            <option value="{{ $yl->id }}"
                                {{ isset($student) && $student->year_level_id == $yl->id ? 'selected' : '' }}>
                                {{ $yl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year_entry" class="form-label fw-semibold">Year Entry <span class="text-danger">*</span></label>
                    <input type="number" name="year_entry" id="year_entry" class="form-control" required>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="gender" class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                    <select name="gender" id="gender" class="form-select">
                        <option value="Male" {{ old('gender', $application->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $application->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="mobile_no" class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_no" id="mobile_no" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
                
                <div class="col-md-3">
                    <label for="no_of_siblings" class="form-label fw-semibold">No. of Siblings <span class="text-danger">*</span></label>
                    <input type="text" name="no_of_siblings" id="no_of_siblings" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="dob" class="form-label fw-semibold">Birthdate <span class="text-danger">*</span></label>
                    {{-- <input type="date" name="dob" id="dob" class="form-control"> --}}
                    <input type="date" name="dob" id="dob" class="form-control" value="{{ old('dob', isset($application) && $application->dob ? \Carbon\Carbon::parse($application->dob)->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="birthplace" class="form-label fw-semibold">Birthplace <span class="text-danger">*</span></label>
                    <input type="text" name="birthplace" id="birthplace" class="form-control">
                </div>

                <div class="col-md-6">
                    <label for="religion" class="form-label fw-semibold">Religion <span class="text-danger">*</span></label>
                    <input type="text" name="religion" id="religion" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="nationality" class="form-label fw-semibold">Nationality <span class="text-danger">*</span></label>
                    <input type="text" name="nationality" id="nationality" class="form-control">
                </div>
            </div><br>

            <hr class="border-2 border-warning">

            <div class="row g-3 mt-2">
                <h5><strong>Permanent Address</strong></h5>
                <div class="col-md-3">
                    <label for="province_id" class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
                    <select name="province_id" id="province_id" class="form-select" required>
                        <option value="" selected disabled>Select Province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="city_id" class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                    <select name="city_id" id="city_id" class="form-select" required>
                        <option value="" selected disabled>Select Province</option>
                        @foreach($towns as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="barangay" class="form-label fw-semibold">Barangay / Street <span class="text-danger">*</span></label>
                    <input type="text" name="barangay" id="barangay" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="staying_in" class="form-label fw-semibold">Staying in <span class="text-danger">*</span></label>
                    <select name="staying_in" id="staying_in" class="form-select">
                        <option value="Own House" {{ old('staying_in', $application->staying_in ?? '') == 'Own House' ? 'selected' : '' }}>Own House</option>
                        <option value="Relatives House" {{ old('staying_in', $application->staying_in ?? '') == 'Relatives House' ? 'selected' : '' }}>Relatives House</option>
                        <option value="Boarding House" {{ old('staying_in', $application->staying_in ?? '') == 'Boarding House' ? 'selected' : '' }}>Boarding House</option>
                        <option value="Dormitory" {{ old('staying_in', $application->staying_in ?? '') == 'Dormitory' ? 'selected' : '' }}>Dormitory</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <h5><strong>Current Address</strong></h5>
                <div class="col-md-3">
                    <label for="current_province_id" class="form-label fw-semibold">Province</label>
                    <select name="current_province_id" id="current_province_id" class="form-select">
                        <option value="" selected disabled>Select Province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="current_city_id" class="form-label fw-semibold">City / Town</label>
                    <select name="current_city_id" id="current_city_id" class="form-select">
                        <option value="" selected disabled>Select Province</option>
                        @foreach($towns as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="current_barangay" class="form-label fw-semibold">Barangay / Street</label>
                    <input type="text" name="current_barangay" id="current_barangay" class="form-control">
                </div>
            </div><br>

            <hr class="border-2 border-warning">

            <div class="row g-3 mt-2">
                <h5><strong>Educational Background</strong></h5>
                <div class="col-md-4">
                    <label for="elem_school_name" class="form-label fw-semibold">Elementary School <span class="text-danger">*</span></label>
                    <input type="text" name="elem_school_name" id="elem_school_name" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="elem_address" class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                    <input type="text" name="elem_address" id="elem_address" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="elem_school_year_attended" class="form-label fw-semibold">S.Y. Attended <span class="text-danger">*</span></label>
                    <input type="text" name="elem_school_year_attended" id="elem_school_year_attended" class="form-control">
                </div>

                <div class="col-md-4">
                    <label for="jhs_name" class="form-label fw-semibold">Junior High School <span class="text-danger">*</span></label>
                    <input type="text" name="jhs_name" id="jhs_name" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="jhs_address" class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                    <input type="text" name="jhs_address" id="jhs_address" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="jhs_year_attended" class="form-label fw-semibold">S.Y. Attended <span class="text-danger">*</span></label>
                    <input type="text" name="jhs_year_attended" id="jhs_year_attended" class="form-control">
                </div>

                <div class="col-md-12">
                    <label for="awards" class="form-label fw-semibold">Award(s) Recognition Received</label>
                    <textarea class="form-control" name="awards" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <label for="organization" class="form-label fw-semibold">Organizations/Club previously affiliated</label>
                    <input type="text" name="organization" id="organization" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="position" class="form-label fw-semibold">Position</label>
                    <input type="text" name="position" id="position" class="form-control">
                </div>
            </div><br>

            <hr class="border-2 border-warning">

            <div class="row g-3 mt-2">
                <h5><strong>Family Background</strong></h5>
                <div class="col-md-5">
                    <label for="father_name" class="form-label fw-semibold">Father Name <span class="text-danger">*</span></label>
                    <input type="text" name="father_name" id="father_name" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="father_occupation" class="form-label fw-semibold">Occupation <span class="text-danger">*</span></label>
                    <input type="text" name="father_occupation" id="father_occupation" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="father_age" class="form-label fw-semibold">Age <span class="text-danger">*</span></label>
                    <input type="text" name="father_age" id="father_age" class="form-control">
                </div>
                <div class="col-md-7">
                    <label for="father_education" class="form-label fw-semibold">Highest Educational Attainment <span class="text-danger">*</span></label>
                    <input type="text" name="father_education" id="father_education" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="father_mobile_no" class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" name="father_mobile_no" id="father_mobile_no" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="father_status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="father_status" id="father_status" class="form-select">
                        <option value="" disabled selected>Select Status</option>
                        <option value="Living" {{ old('father_status', $application->father_status ?? '') == 'Living' ? 'selected' : '' }}>Living</option>
                        <option value="Deceased" {{ old('father_status', $application->father_status ?? '') == 'Deceased' ? 'selected' : '' }}>Deceased</option>
                    </select>
                </div>
                <div class="col-md-10">
                    <label for="father_placework" class="form-label fw-semibold">Place of Work <span class="text-danger">*</span></label>
                    <input type="text" name="father_placework" id="father_placework" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="father_ofw_status" class="form-label fw-semibold">Work Status <span class="text-danger">*</span></label>
                    <select name="father_ofw_status" id="father_ofw_status" class="form-select">
                        <option value="" disabled selected>Select Work Status</option>
                        <option value="OFW" {{ old('father_ofw_status', $application->father_ofw_status ?? '') == 'OFW' ? 'selected' : '' }}>OFW</option>
                        <option value="Not OFW" {{ old('father_ofw_status', $application->father_ofw_status ?? '') == 'Not OFW' ? 'selected' : '' }}>Not OFW</option>
                    </select>
                </div>

                <div class="w-100 d-block" style="height: 2px; background-color: #dee2e6;"></div>

                <div class="col-md-5">
                    <label for="mother_name" class="form-label fw-semibold">Mother Name <span class="text-danger">*</span></label>
                    <input type="text" name="mother_name" id="mother_name" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="mother_occupation" class="form-label fw-semibold">Occupation <span class="text-danger">*</span></label>
                    <input type="text" name="mother_occupation" id="mother_occupation" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="mother_age" class="form-label fw-semibold">Age <span class="text-danger">*</span></label>
                    <input type="text" name="mother_age" id="mother_age" class="form-control">
                </div>
                <div class="col-md-7">
                    <label for="mother_education" class="form-label fw-semibold">Highest Educational Attainment <span class="text-danger">*</span></label>
                    <input type="text" name="mother_education" id="mother_education" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="mother_mobile_no" class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" name="mother_mobile_no" id="mother_mobile_no" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="mother_status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="mother_status" id="mother_status" class="form-select">
                        <option value="" disabled selected>Select Status</option>
                        <option value="Living" {{ old('mother_status', $application->mother_status ?? '') == 'Living' ? 'selected' : '' }}>Living</option>
                        <option value="Deceased" {{ old('mother_status', $application->mother_status ?? '') == 'Deceased' ? 'selected' : '' }}>Deceased</option>
                    </select>
                </div>
                <div class="col-md-10">
                    <label for="mother_placework" class="form-label fw-semibold">Place of Work <span class="text-danger">*</span></label>
                    <input type="text" name="mother_placework" id="mother_placework" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="mother_ofw_status" class="form-label fw-semibold">Work Status <span class="text-danger">*</span></label>
                    <select name="mother_ofw_status" id="mother_ofw_status" class="form-select">
                        <option value="" disabled selected>Select Work Status</option>
                        <option value="OFW" {{ old('mother_ofw_status', $application->mother_ofw_status ?? '') == 'OFW' ? 'selected' : '' }}>OFW</option>
                        <option value="Not OFW" {{ old('mother_ofw_status', $application->mother_ofw_status ?? '') == 'Not OFW' ? 'selected' : '' }}>Not OFW</option>
                    </select>
                </div>

                <div class="w-100 d-block" style="height: 2px; background-color: #dee2e6;"></div>

                <div class="col-md-5">
                    <label for="guardian_name" class="form-label fw-semibold">Guardian Name <span class="text-danger">*</span></label>
                    <input type="text" name="guardian_name" id="guardian_name" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="guardian_occupation" class="form-label fw-semibold">Occupation <span class="text-danger">*</span></label>
                    <input type="text" name="guardian_occupation" id="guardian_occupation" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="guardian_number" class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" name="guardian_number" id="guardian_number" class="form-control">
                </div>

                <div class="w-100 d-block" style="height: 2px; background-color: #dee2e6;"></div>

                <div class="col-md-6">
                    <label for="parents_marital_status" class="form-label fw-semibold">Marital Status of Parents <span class="text-danger">*</span></label>
                    <select id="parents_marital_status" name="parents_marital_status" class="form-select" required>
                        <option value="" disabled {{ old('parents_marital_status', $application->parents_marital_status ?? '') == '' ? 'selected' : '' }}>Select Status</option>
                        <option value="Living together" {{ old('parents_marital_status', $application->parents_marital_status ?? '') == 'Living together' ? 'selected' : '' }}>Living together</option>
                        <option value="Separated" {{ old('parents_marital_status', $application->parents_marital_status ?? '') == 'Separated' ? 'selected' : '' }}>Separated</option>
                        <option value="Single Parent" {{ old('parents_marital_status', $application->parents_marital_status ?? '') == 'Single Parent' ? 'selected' : '' }}>Single Parent</option>
                        <option value="Widow/Widower" {{ old('parents_marital_status', $application->parents_marital_status ?? '') == 'Widow/Widower' ? 'selected' : '' }}>Widow/Widower</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="monthly_family_income" class="form-label fw-semibold">Monthly Family Income <span class="text-danger">*</span></label>
                    <select id="monthly_family_income" name="monthly_family_income" class="form-select" required>
                        <option value="" disabled {{ old('monthly_family_income', $application->monthly_family_income ?? '') == '' ? 'selected' : '' }}>Select Income Range</option>
                        <option value="below P10,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'below P10,000' ? 'selected' : '' }}>Below P10,000</option>
                        <option value="P10,000 - below P21,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P10,000 - below P21,000' ? 'selected' : '' }}>P10,000 - below P21,000</option>
                        <option value="P21,000 - below P31,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P21,000 - below P31,000' ? 'selected' : '' }}>P21,000 - below P31,000</option>
                        <option value="P31,000 - below P41,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P31,000 - below P41,000' ? 'selected' : '' }}>P31,000 - below P41,000</option>
                        <option value="P41,000 - below P51,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P41,000 - below P51,000' ? 'selected' : '' }}>P41,000 - below P51,000</option>
                        <option value="P51,000 - below P61,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P51,000 - below P61,000' ? 'selected' : '' }}>P51,000 - below P61,000</option>
                        <option value="P61,000 - below P71,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P61,000 - below P71,000' ? 'selected' : '' }}>P61,000 - below P71,000</option>
                        <option value="P71,000 - below P81,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P71,000 - below P81,000' ? 'selected' : '' }}>P71,000 - below P81,000</option>
                        <option value="P81,000 - below P91,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P81,000 - below P91,000' ? 'selected' : '' }}>P81,000 - below P91,000</option>
                        <option value="P91,000 - below P101,000" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P91,000 - below P101,000' ? 'selected' : '' }}>P91,000 - below P101,000</option>
                        <option value="P101,000 and above" {{ old('monthly_family_income', $application->monthly_family_income ?? '') == 'P101,000 and above' ? 'selected' : '' }}>P101,000 and above</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="family_living_arrangement" class="form-label fw-semibold">Family Living Arrangement <span class="text-danger">*</span></label>
                    <select id="family_living_arrangement" name="family_living_arrangement" class="form-select" required>
                        <option value="" disabled {{ old('family_living_arrangement', $application->family_living_arrangement ?? '') == '' ? 'selected' : '' }}>Select Living Arrangement</option>
                        <option value="Living with parents" {{ old('family_living_arrangement', $application->family_living_arrangement ?? '') == 'Living with parents' ? 'selected' : '' }}>Living with parents</option>
                        <option value="Living with grandparents" {{ old('family_living_arrangement', $application->family_living_arrangement ?? '') == 'Living with grandparents' ? 'selected' : '' }}>Living with grandparents</option>
                        <option value="Living with sibling/s" {{ old('family_living_arrangement', $application->family_living_arrangement ?? '') == 'Living with sibling/s' ? 'selected' : '' }}>Living with sibling/s</option>
                        <option value="Living alone" {{ old('family_living_arrangement', $application->family_living_arrangement ?? '') == 'Living alone' ? 'selected' : '' }}>Living alone</option>
                        <option value="Living with relatives (please specify)" {{ old('family_living_arrangement', $application->family_living_arrangement ?? '') == 'Living with relatives (please specify)' ? 'selected' : '' }}>Living with relatives (please specify)</option>
                        <option value="Living with others (please specify)" {{ old('family_living_arrangement', $application->family_living_arrangement ?? '') == 'Living with others (please specify)' ? 'selected' : '' }}>Living with others (please specify)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div id="others_specify_box" class="mt-2" style="display: {{ in_array(old('family_living_arrangement', $application->family_living_arrangement ?? ''), ['Living with relatives (please specify)', 'Living with others (please specify)']) ? 'block' : 'none' }};">
                        <label class="d-none d-md-block">-</label>
                        <input type="text" id="others_specify" name="others_specify" class="form-control" placeholder="Please specify" value="{{ old('others_specify', $application->others_specify ?? '') }}">
                    </div>
                </div>
            </div><br>
            
            <hr class="border-2 border-warning">

            <div class="row g-3 mt-2">
                <h5><strong>Other Information</strong></h5>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="is_pwd" class="form-label fw-semibold">Is the student with Disability (PWD)</label>
                        <select id="is_pwd" name="is_pwd" class="form-select" required>
                            <option value="" disabled selected>Select PWD Status</option>
                            <option value="1" {{ old('is_pwd', $application->is_pwd ?? 0) ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('is_pwd', $application->is_pwd ?? 0) ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div id="is_pwd_specify_box" class="mt-2" style="display: {{ old('is_pwd', $application->is_pwd ?? '') == '1' ? 'block' : 'none' }};">
                            <label class="d-none d-md-block">-</label>
                            <input type="text" id="is_pwd_yes" name="is_pwd_yes" class="form-control" placeholder="Please specify" value="{{ old('is_pwd_yes', $application->is_pwd_yes ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="is_scholar" class="form-label fw-semibold">Is student has a scholar? <span class="text-danger">*</span></label>
                        <select id="is_scholar" name="is_scholar" class="form-select" required>
                            <option value="" disabled selected>Select Scholar Status</option>
                            <option value="1" {{ old('is_scholar', $application->is_scholar ?? 0) ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('is_scholar', $application->is_scholar ?? 0) ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div id="is_scholar_type_box" style="display: {{ old('is_scholar', $application->is_scholar ?? '') == '1' ? 'block' : 'none' }};">
                            <label for="is_scholar_type" class="form-label">If yes, which type of scholarship? <span class="text-danger">*</span></label>
                            <select id="is_scholar_type" name="is_scholar_type" class="form-select">
                                <option value="Entrance" {{ old('is_scholar_type', $application->is_scholar_type ?? '') == 'Entrance' ? 'selected' : '' }}>Entrance</option>
                                <option value="ESC" {{ old('is_scholar_type', $application->is_scholar_type ?? '') == 'ESC' ? 'selected' : '' }}>ESC</option>
                                <option value="Special Grant" {{ old('is_scholar_type', $application->is_scholar_type ?? '') == 'Special Grant' ? 'selected' : '' }}>Special Grant</option>
                                <option value="Voucher Program" {{ old('is_scholar_type', $application->is_scholar_type ?? '') == 'Voucher Program' ? 'selected' : '' }}>Voucher Program</option>
                                <option value="Others" {{ old('is_scholar_type', $application->is_scholar_type ?? '') == 'Others' ? 'selected' : '' }}>Others (pls. specify)</option>
                            </select>
                        </div>
                        <div id="is_scholar_others_box" class="mt-2" style="display: {{ old('is_scholar_type', $application->is_scholar_type ?? '') == 'Others' ? 'block' : 'none' }};">
                            <input type="text" id="is_scholar_yes_others" name="is_scholar_yes_others" class="form-control" placeholder="If yes, please specify" value="{{ old('is_scholar_yes_others', $application->is_scholar_yes_others ?? '') }}">
                        </div>
                    </div>
                </div>
            </div><br>


            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-custom">
                    <i class="bi bi-save"></i> Save Student
                </button>
            </div>
        </form>
    </div>
</div>

@include('Components.Registrar.load-data-modal')

@include('Components.Registrar.create-success-modal')

<!-- Add New Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    {{-- <div class="modal-dialog modal-lg modal-dialog-scrollable"> --}}
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Search Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Section inside modal -->
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold mb-0">Search Student</label>
                    </div>
                    <div class="col-12 col-md-7">
                        <input type="text" id="modalSearchInput" class="form-control" placeholder="Enter Application Number or Last Name" required>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="button" id="modalSearchBtn" class="btn btn-custom">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="modalSearchResults" class="d-none">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Select Student:</h6>
                            <div id="modalResultsList" class="list-group"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const livingSelect = document.getElementById('family_living_arrangement');
    const specifyBox = document.getElementById('others_specify_box');

    livingSelect.addEventListener('change', function() {
        if (this.value === 'Living with relatives (please specify)' || this.value === 'Living with others (please specify)') {
            specifyBox.style.display = 'block';
        } else {
            specifyBox.style.display = 'none';
            document.getElementById('others_specify').value = '';
        }
    });
</script>
    
<script>
    const pwdSelect = document.getElementById('is_pwd');
    const pwdBox = document.getElementById('is_pwd_specify_box');
    const pwdInput = document.getElementById('is_pwd_yes');

    pwdSelect.addEventListener('change', function() {
        if (this.value === '1') {
            pwdBox.style.display = 'block';
        } else {
            pwdBox.style.display = 'none';
            pwdInput.value = '';
        }
    });
</script>

<script>
    const scholarSelect = document.getElementById('is_scholar');
    const scholarTypeBox = document.getElementById('is_scholar_type_box');
    const scholarTypeSelect = document.getElementById('is_scholar_type');
    const scholarOthersBox = document.getElementById('is_scholar_others_box');
    const scholarOthersInput = document.getElementById('is_scholar_yes_others');

    scholarSelect.addEventListener('change', function() {
        if (this.value === '1') {
            scholarTypeBox.style.display = 'block';
        } else {
            scholarTypeBox.style.display = 'none';
            scholarTypeSelect.value = '';
            scholarOthersBox.style.display = 'none';
            scholarOthersInput.value = '';
        }
    });

    scholarTypeSelect.addEventListener('change', function() {
        if (this.value === 'Others') {
            scholarOthersBox.style.display = 'block';
        } else {
            scholarOthersBox.style.display = 'none';
            scholarOthersInput.value = '';
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const successModal = new bootstrap.Modal(document.getElementById('successModal'));

    const studentFormFields = {
        application_number: document.getElementById('application_number'),
        lastname: document.getElementById('lastname'),
        firstname: document.getElementById('firstname'),
        middlename: document.getElementById('middlename'),
        department_id: document.getElementById('department_id'),
        program_id: document.getElementById('program_id'),
        studentstatus_id: document.getElementById('studentstatus_id'),
        year_level_id: document.getElementById('year_level_id'),
        year_entry: document.getElementById('year_entry'),
        gender: document.getElementById('gender'),
        mobile_no: document.getElementById('mobile_no'),
        email: document.getElementById('email'),
        no_of_siblings: document.getElementById('no_of_siblings'),
        dob: document.getElementById('dob'),
        birthplace: document.getElementById('birthplace'),
        religion: document.getElementById('religion'),
        nationality: document.getElementById('nationality'),
        province_id: document.getElementById('province_id'),
        city_id: document.getElementById('city_id'),
        barangay: document.getElementById('barangay'),
        staying_in: document.getElementById('staying_in'),
        current_province_id: document.getElementById('current_province_id'),
        current_city_id: document.getElementById('current_city_id'),
        current_barangay: document.getElementById('current_barangay'),

        elem_school_name: document.getElementById('elem_school_name'),
        elem_address: document.getElementById('elem_address'),
        elem_school_year_attended: document.getElementById('elem_school_year_attended'),
        jhs_name: document.getElementById('jhs_name'),
        jhs_address: document.getElementById('jhs_address'),
        jhs_year_attended: document.getElementById('jhs_year_attended'),
        awards: document.querySelector('textarea[name="awards"]'),
        organization: document.getElementById('organization'),
        position: document.getElementById('position'),

        father_name: document.getElementById('father_name'),
        father_occupation: document.getElementById('father_occupation'),
        father_age: document.getElementById('father_age'),
        father_education: document.getElementById('father_education'),
        father_mobile_no: document.getElementById('father_mobile_no'),
        father_status: document.getElementById('father_status'),
        father_placework: document.getElementById('father_placework'),
        father_ofw_status: document.getElementById('father_ofw_status'),

        mother_name: document.getElementById('mother_name'),
        mother_occupation: document.getElementById('mother_occupation'),
        mother_age: document.getElementById('mother_age'),
        mother_education: document.getElementById('mother_education'),
        mother_mobile_no: document.getElementById('mother_mobile_no'),
        mother_status: document.getElementById('mother_status'),
        mother_placework: document.getElementById('mother_placework'),
        mother_ofw_status: document.getElementById('mother_ofw_status'),

        guardian_name: document.getElementById('guardian_name'),
        guardian_occupation: document.getElementById('guardian_occupation'),
        guardian_number: document.getElementById('guardian_number'),

        parents_marital_status: document.getElementById('parents_marital_status'),
        monthly_family_income: document.getElementById('monthly_family_income'),
        family_living_arrangement: document.getElementById('family_living_arrangement'),
        others_specify: document.getElementById('others_specify'),

        is_pwd: document.getElementById('is_pwd'),
        is_pwd_yes: document.getElementById('is_pwd_yes'),

        is_scholar: document.getElementById('is_scholar'),
        is_scholar_type: document.getElementById('is_scholar_type'),
        is_scholar_yes_others: document.getElementById('is_scholar_yes_others'),
    };

    const modalSearchInput = document.getElementById('modalSearchInput');
    const modalSearchBtn = document.getElementById('modalSearchBtn');
    const modalSearchResults = document.getElementById('modalSearchResults');
    const modalResultsList = document.getElementById('modalResultsList');

    // Load programs for a given department
    function loadPrograms(departmentId, selectedProgramId = null) {
        const programSelect = studentFormFields.program_id;
        programSelect.innerHTML = '<option value="" selected disabled>Loading...</option>';
        programSelect.disabled = false;

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
            })
            .catch(() => {
                programSelect.innerHTML = '<option value="" disabled>Error loading programs</option>';
            });
    }

    // Department selection change
    studentFormFields.department_id.addEventListener('change', function() {
        const departmentId = this.value;
        if (departmentId) loadPrograms(departmentId);
        else studentFormFields.program_id.innerHTML = '<option value="" selected disabled>Select Program</option>';
    });

    // Search modal
    modalSearchBtn.addEventListener('click', function() {
        const term = modalSearchInput.value.trim();
        if (!term) return alert('Please enter an application number or student name.');

        fetch('{{ route("registrar.studentmanagement.search") }}?query=' + encodeURIComponent(term), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.status === 404 ? Promise.reject('No results') : res.json())
        .then(students => {
            modalResultsList.innerHTML = '';
            if (students.length) {
                modalSearchResults.classList.remove('d-none');

                students.forEach(student => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('list-group-item', 'list-group-item-action');
                    btn.textContent = `${student.lastname}, ${student.firstname} ${student.middlename ?? ''} — ${student.application_number}`;

                    btn.addEventListener('click', () => {

                    studentFormFields.department_id.value = student.department_id ?? '';
    
                        // Load programs for this department and select the student's program
                        if (student.department_id) {
                            loadPrograms(student.department_id, student.program_id);
                        } else {
                            studentFormFields.program_id.innerHTML = '<option value="" selected disabled>Select Program</option>';
                        }

                        // Autofill fields
                        Object.keys(studentFormFields).forEach(key => {
                            const field = studentFormFields[key];
                            if (!field) return;

                            if (key === 'dob' && student[key]) {
                                field.value = new Date(student[key]).toISOString().split('T')[0];
                            } else if (key === 'family_living_arrangement') {
                                field.value = student[key] ?? '';
                                if (['Living with relatives (please specify)', 'Living with others (please specify)'].includes(field.value)) {
                                    specifyBox.style.display = 'block';
                                    studentFormFields.others_specify.value = student.others_specify ?? '';
                                } else {
                                    specifyBox.style.display = 'none';
                                    studentFormFields.others_specify.value = '';
                                }
                            } else if (key === 'is_pwd') {
                                const val = !!student[key]; // convert to boolean
                                field.value = val ? 1 : 0; // if using a <select> with value="1"/"0"
                                
                                // Show/hide PWD specify box
                                if (val) {
                                    pwdBox.style.display = 'block';
                                    studentFormFields.is_pwd_yes.value = student.is_pwd_yes ?? '';
                                } else {
                                    pwdBox.style.display = 'none';
                                    studentFormFields.is_pwd_yes.value = '';
                                }

                                field.dispatchEvent(new Event('change'));
                            } else if (key === 'is_scholar') {
                                const val = !!student[key]; // convert to boolean
                                field.value = val ? 1 : 0; // if using a <select> with value="1"/"0"

                                if (val) {
                                    scholarTypeBox.style.display = 'block';
                                    studentFormFields.is_scholar_type.value = student.is_scholar_type ?? '';
                                    if (student.is_scholar_type === 'Others') {
                                        scholarOthersBox.style.display = 'block';
                                        studentFormFields.is_scholar_yes_others.value = student.is_scholar_yes_others ?? '';
                                    } else {
                                        scholarOthersBox.style.display = 'none';
                                        studentFormFields.is_scholar_yes_others.value = '';
                                    }
                                } else {
                                    scholarTypeBox.style.display = 'none';
                                    scholarOthersBox.style.display = 'none';
                                    studentFormFields.is_scholar_type.value = '';
                                    studentFormFields.is_scholar_yes_others.value = '';
                                }

                                field.dispatchEvent(new Event('change'));
                            } else if (key === 'others_specify') {
                                // handled above
                            } else {
                                field.value = student[key] ?? '';
                            }
                        });

                        studentFormFields.program_id.disabled = false;

                        modalSearchResults.classList.add('d-none');
                        bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
                        successModal.show();
                    });

                    modalResultsList.appendChild(btn);
                });
            } else {
                modalSearchResults.classList.add('d-none');
            }
        })
        .catch(() => alert('No matching records found.'));
    });

    modalSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); modalSearchBtn.click(); }
    });
});
</script>

@endsection