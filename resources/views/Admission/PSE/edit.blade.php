@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Update Applicant Details</h2>
        <a href="{{ route('admission.pse.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
        </a>
    </div><br>

    <form action="{{ route('admission.pse.update', $application->application_number) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Student Information -->
        <h5 class="fw-bold mb-3">APPLICANT INFORMATION</h5>
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label">Last Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="lastname" 
                       value="{{ old('lastname', $application->lastname) }}" placeholder="Enter Last Name" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">First Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="firstname" 
                       value="{{ old('firstname', $application->firstname) }}" placeholder="Enter First Name" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Middle Name:</label>
                <input type="text" class="form-control" name="middlename" 
                       value="{{ old('middlename', $application->middlename) }}" placeholder="Enter Middle Name">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Suffix (Jr., Sr., I, II, etc):</label>
                <input type="text" class="form-control" name="suffix" 
                       value="{{ old('suffix', $application->suffix) }}" placeholder="Enter Suffix">
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-3">
                <label class="form-label">Gender: <span class="text-danger">*</span></label>
                <select class="form-select" name="gender" required>
                    <option value="" disabled>Choose Gender</option>
                    <option value="Male" {{ old('gender', $application->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $application->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Mobile No.: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mobile_no" 
                       value="{{ old('mobile_no', $application->mobile_no) }}" placeholder="Enter Mobile No." required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Email Address: <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" 
                       value="{{ old('email', $application->email) }}" placeholder="Enter Email Address" required>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-2">
                <label class="form-label">Date of Birth:</label>
                <input type="date" class="form-control" name="dob"
                    value="{{ old('dob', $application->dob ? \Carbon\Carbon::parse($application->dob)->format('Y-m-d') : '') }}"
                    required>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Age:</label>
                <input type="number" class="form-control" name="age" 
                       value="{{ old('age', $application->age) }}" placeholder="Enter Age" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Nationality: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nationality" 
                       value="{{ old('nationality', $application->nationality) }}" placeholder="Enter Nationality" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Religion: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="religion" 
                       value="{{ old('religion', $application->religion) }}" placeholder="Enter Religion" required>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-9">
                <label class="form-label">Permanent Home Address: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="address" 
                       value="{{ old('address', $application->address) }}" placeholder="Enter Permanent Home Address" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Zip Code: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="zip_code" 
                       value="{{ old('zip_code', $application->zip_code) }}" placeholder="Enter Zip Code" required>
            </div>
        </div>

        <div class="row g-3 mt-1 mb-4">
            <div class="col-12 col-md-9">
                <label class="form-label">Contact Person (Parent/Guardian): <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="contact_person" 
                       value="{{ old('contact_person', $application->contact_person) }}" placeholder="Enter Contact Person" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Contact Number: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="contact_number" 
                       value="{{ old('contact_number', $application->contact_number) }}" placeholder="Enter Contact Number" required>
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Last School Attended -->
        <h5 class="fw-bold mb-3">LAST SCHOOL ATTENDED</h5>
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">School Name:</label>
                <input type="text" class="form-control" name="school_name" 
                       value="{{ old('school_name', $application->school_name) }}" placeholder="Enter School Name">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Learner Reference Number (LRN):</label>
                <input type="text" class="form-control" name="lrn" 
                       value="{{ old('lrn', $application->lrn) }}" placeholder="Enter LRN">
            </div>
        </div>
        <div class="row g-3 mt-1 mb-4">
            <div class="col-12 col-md-9">
                <label class="form-label">School Address:</label>
                <input type="text" class="form-control" name="school_address" 
                       value="{{ old('school_address', $application->school_address) }}" placeholder="Enter School Address">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Zip Code:</label>
                <input type="text" class="form-control" name="school_zip" 
                       value="{{ old('school_zip', $application->school_zip) }}" placeholder="Enter Zip Code">
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Program Preference -->
        <h5 class="fw-bold mb-3">PROGRAM PREFERENCE</h5>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label">PSE Program: <span class="text-danger">*</span></label>
                <select class="form-select" name="program" required>
                    <option value="" disabled>Choose PSE Program</option>
                    @php
                        $programs = [
                            'PRESCHOOL : Nursery',
                            'PRESCHOOL : Preparatory',
                            'PRESCHOOL : Kinder',
                            'ELEMENTARY : Grade 1',
                            'ELEMENTARY : Grade 2',
                            'ELEMENTARY : Grade 3',
                            'ELEMENTARY : Grade 4',
                            'ELEMENTARY : Grade 5',
                            'ELEMENTARY : Grade 6'
                        ];
                    @endphp
                    @foreach($programs as $programOption)
                        <option value="{{ $programOption }}" 
                            {{ old('program', $application->program) == $programOption ? 'selected' : '' }}>
                            {{ $programOption }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Applicant Details -->
        <h5 class="fw-bold mb-3">APPLICANT DETAILS</h5>
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label">Application Number:</label>
                <input type="text" class="form-control" value="{{ $application->application_number }}" 
                    readonly
                    style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">OR Number:</label>
                <input type="text" class="form-control" value="{{ $application->collection->or_number ?? 'Unpaid Student - No OR to be show' }}" 
                       readonly
                       style="background-color: #f8f9fa; cursor: not-allowed;">
                <input type="hidden" name="or_number" value="{{ $application->collection->or_number ?? '' }}">
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-3">
                <label class="form-label">Applicant Status: <span class="text-danger">*</span></label>
                <select class="form-select" name="applicant_status" id="applicant_status" required>
                    <option value="" disabled>Choose Applicant Status</option>
                    @php
                        // Program → Default Applicant Status map
                        $statusMap = [
                            'PRESCHOOL : Nursery'       => 'Incoming Nursery',
                            'PRESCHOOL : Preparatory'   => 'Incoming Preparatory',
                            'PRESCHOOL : Kinder'        => 'Incoming Kinder',
                            'ELEMENTARY : Grade 1'      => 'Incoming Grade 1',
                            'ELEMENTARY : Grade 2'      => 'Transferee (Grade 2-6)',
                            'ELEMENTARY : Grade 3'      => 'Transferee (Grade 2-6)',
                            'ELEMENTARY : Grade 4'      => 'Transferee (Grade 2-6)',
                            'ELEMENTARY : Grade 5'      => 'Transferee (Grade 2-6)',
                            'ELEMENTARY : Grade 6'      => 'Transferee (Grade 2-6)',
                        ];

                        $selectedProgram = old('program', $application->program ?? '');
                        $defaultStatus   = $statusMap[$selectedProgram] ?? '';
                        $currentStatus   = old('applicant_status', $application->applicant_status ?? $defaultStatus);
                    @endphp

                    <option value="Incoming Nursery" {{ $currentStatus == 'Incoming Nursery' ? 'selected' : '' }}>Incoming Nursery</option>
                    <option value="Incoming Preparatory" {{ $currentStatus == 'Incoming Preparatory' ? 'selected' : '' }}>Incoming Preparatory</option>
                    <option value="Incoming Kinder" {{ $currentStatus == 'Incoming Kinder' ? 'selected' : '' }}>Incoming Kinder</option>
                    <option value="Incoming Grade 1" {{ $currentStatus == 'Incoming Grade 1' ? 'selected' : '' }}>Incoming Grade 1</option>
                    <option value="Transferee (Grade 2-6)" {{ $currentStatus == 'Transferee (Grade 2-6)' ? 'selected' : '' }}>Transferee (Grade 2-6)</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Exam Schedule (Date): <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="exam_schedule_date"
                    value="{{ old('exam_schedule_date', $application->exam_schedule_date 
                        ? \Carbon\Carbon::parse($application->exam_schedule_date)->format('Y-m-d') 
                        : \Carbon\Carbon::today()->format('Y-m-d')) }}"
                    required>
            </div>
        </div>

        <!-- Exam Taken Checkbox -->
        <div class="row g-3 mt-1 mb-4">
            <div class="col-12">
                <input type="hidden" name="exam_taken" value="0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="exam_taken" id="exam_taken" value="1" {{ old('exam_taken', $application->exam_taken ?? '') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="exam_taken">
                        Applicant already take exam
                    </label>
                </div>
            </div>
        </div>
    
        <!-- School Readiness Test (Incoming Grade 1) -->
        <div id="school_readiness_section">
            <hr class="border-2 border-warning opacity-75 my-4">
            <h5 class="fw-bold mb-3">SCHOOL READINESS TEST RESULT</h5>
            <div class="row mb-4">
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center align-middle" width="150">Test</th>
                                    <th class="text-center align-middle" width="100">Maximum Possible Score</th>
                                    <th class="text-center align-middle" width="100">RS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subtestsincoming as $subtest)
                                <tr>
                                    <td><b>{{ $subtest->name }}</b></td>
                                    <td class="text-center">
                                        <input type="hidden" name="incoming_subtest_id[]" value="{{ $subtest->id }}">
                                        {{ $subtest->maxscore ?? '' }}
                                        <input type="hidden" name="incoming_ts[]" value="{{ $subtest->maxscore }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-center" 
                                            name="incoming_rs[]" 
                                            value="{{ $subtest->result->rs ?? '' }}">
                                        <input type="hidden" name="incoming_subtest_name[]" value="{{ $subtest->name }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admission Result (Transferee) -->
        <div id="admission_result_section">
            <hr class="border-2 border-warning opacity-75 my-4">
            <h5 class="fw-bold mb-3">ADMISSION RESULT</h5>
            <div class="row mb-4">
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center align-middle" width="150">Area</th>
                                    <th class="text-center align-middle" width="100">TS</th>
                                    <th class="text-center align-middle" width="100">RS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subteststransferee as $subtest)
                                <tr>
                                    <td><b>{{ $subtest->name }}</b></td>
                                    <td class="text-center">
                                        <input type="hidden" name="transferee_subtest_id[]" value="{{ $subtest->id }}">
                                        {{ $subtest->maxscore ?? '' }}
                                        <input type="hidden" name="transferee_ts[]" value="{{ $subtest->maxscore }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-center" 
                                            name="transferee_rs[]" 
                                            value="{{ $subtest->result->rs ?? '' }}">
                                        <input type="hidden" name="transferee_subtest_name[]" value="{{ $subtest->name }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks Section -->
        <div id="remarks_section">
            <hr class="border-2 border-warning opacity-75 my-4">
            <h5 class="fw-bold mb-3">REMARKS</h5>
            <div class="row g-3 mb-4">
                <!-- Always show Reviewer’s Remarks -->
                <div class="col-12 col-md-12" id="reviewer_remarks_wrap">
                    <label class="form-label">Reviewer's Remarks <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="interviewer_remarks" id="interviewer_remarks" rows="3"
                        placeholder="Enter Interviewer's Remarks" required>{{ old('interviewer_remarks', $application->interviewer_remarks ?? '') }}</textarea>
                </div>

                <!-- Placement -->
                <div class="col-12 col-md-6" id="placement_wrap">
                    <label class="form-label">Preschool Readiness Test Recommended Placement <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="placement" id="placement"
                        value="{{ old('placement', $application->placement ?? '') }}"
                        placeholder="Enter Preschool Readiness Test Recommended Placement" required>
                </div>

                <!-- Remarks -->
                <div class="col-12 col-md-6" id="remarks_wrap">
                    <label class="form-label">Remarks <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="remarks" id="remarks"
                        value="{{ old('remarks', $application->remarks ?? '') }}"
                        placeholder="Enter Remarks" required>
                </div>
            </div>
        </div>

        <!-- Signatories Section -->
        <div id="signatories_section">
            <hr class="border-2 border-warning opacity-75 my-4">
            <h5 class="fw-bold mb-3">SIGNATORIES</h5>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label">Certifier Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="certifier_name" 
                        value="{{ old('certifier_name', $application->certifier_name ?? Auth::user()->name) }}" 
                        placeholder="Enter Certifier Name" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Certifier Designation: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="certifier_designation" 
                        value="{{ old('certifier_designation', $application->certifier_designation ?? 'Psychometrician') }}" required>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label">Verifier Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="verifier_name" 
                        value="{{ old('verifier_name', $application->verifier_name ?? '') }}" 
                        placeholder="Enter Verifier Name" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Verifier Designation: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="verifier_designation" 
                        value="{{ old('verifier_designation', $application->verifier_designation ?? 'Director, Office of Guidance and Testing') }}" required>
                </div>
            </div>
        </div>

        <!-- Update Button -->
        <div class="col-12">
            <button type="submit" class="btn btn-custom px-4 w-100">
                <i class="bi bi-check-lg"></i> Update Applicant
            </button>
        </div>

    </form>
</div>

<!-- Include Success Modal -->
@include('Components.Admission.save-applicant-modal')

<script>
    function toggleSections() {
        const status = document.getElementById("applicant_status").value;

        const readiness = document.getElementById("school_readiness_section");
        const admission = document.getElementById("admission_result_section");

        const placementWrap = document.getElementById("placement_wrap");
        const remarksWrap = document.getElementById("remarks_wrap");

        const placement = document.getElementById("placement");
        const remarks = document.getElementById("remarks");

        // Inputs for readiness and transferee results
        const incomingRS = document.querySelectorAll("#school_readiness_section input[name='rs[]']");
        const transfereeRS = document.querySelectorAll("#admission_result_section input[name='rs[]']");

        // Reset: hide sections + clear required
        readiness.style.display = "none";
        admission.style.display = "none";

        placementWrap.style.display = "block";
        remarksWrap.style.display = "block";
        placement.required = true;
        remarks.required = true;

        incomingRS.forEach(el => el.required = false);
        transfereeRS.forEach(el => el.required = false);

        // Show depending on status
        if (status === "Incoming Grade 1") {
            readiness.style.display = "block";
            
            placementWrap.style.display = "none";
            remarksWrap.style.display = "none";
            placement.required = false;
            remarks.required = false;
            
            incomingRS.forEach(el => el.required = true);
        }
        else if (status === "Transferee (Grade 2-6)") {
            admission.style.display = "block";
            
            placementWrap.style.display = "none";
            remarksWrap.style.display = "none";
            placement.required = false;
            remarks.required = false;
            
            transfereeRS.forEach(el => el.required = true);
        }
    }

    document.getElementById("applicant_status").addEventListener("change", toggleSections);
    toggleSections(); // run on load
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const examTaken = document.getElementById('exam_taken');
        const remarksSection = document.getElementById('remarks_section');
        const signatoriesSection = document.getElementById('signatories_section');

        function toggleExamSections() {
            if (examTaken.checked) {
                remarksSection.style.display = 'block';
                signatoriesSection.style.display = 'block';

                remarksSection.querySelectorAll('input, textarea').forEach(input => {
                    input.removeAttribute('disabled');
                    input.setAttribute('required', true);
                });
            } else {
                remarksSection.style.display = 'none';
                signatoriesSection.style.display = 'none';

                remarksSection.querySelectorAll('input, textarea').forEach(input => {
                    input.value = '';
                    input.removeAttribute('required');
                    input.setAttribute('disabled', true);
                });

                signatoriesSection.querySelectorAll('input').forEach(input => {
                    input.removeAttribute('disabled');
                    input.removeAttribute('required');
                });
            }
        }

        // Initialize when page loads
        toggleExamSections();

        // Listen for checkbox changes
        examTaken.addEventListener('change', toggleExamSections);
    });
</script>

<!-- Auto-open modal if session exists -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('show_success_modal'))
            document.getElementById("applicationNumber").innerText = "{{ session('application_number') }}";
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>

@endsection