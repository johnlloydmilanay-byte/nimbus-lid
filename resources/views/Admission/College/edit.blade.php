@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Update Applicant Details</h2>
        <a href="{{ route('admission.college.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
        </a>
    </div><br>

    <form action="{{ route('admission.college.update', $application->application_number) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Student Information -->
        <h5 class="fw-bold mb-3">APPLICANT INFORMATION</h5>
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label">Last Name:</label>
                <input type="text" class="form-control" name="lastname" 
                       value="{{ old('lastname', $application->lastname) }}" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">First Name:</label>
                <input type="text" class="form-control" name="firstname" 
                       value="{{ old('firstname', $application->firstname) }}" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Middle Name:</label>
                <input type="text" class="form-control" name="middlename" 
                       value="{{ old('middlename', $application->middlename) }}" >
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Suffix (Jr., Sr., I, II, etc):</label>
                <input type="text" class="form-control" name="suffix" 
                       value="{{ old('suffix', $application->suffix) }}" >
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-3">
                <label class="form-label">Gender:</label>
                <select class="form-select" name="gender" required>
                    <option value="" disabled>Choose Gender</option>
                    <option value="Male" {{ old('gender', $application->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $application->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Mobile No.:</label>
                <input type="text" class="form-control" name="mobile_no" 
                       value="{{ old('mobile_no', $application->mobile_no) }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Email Address:</label>
                <input type="email" class="form-control" name="email" 
                       value="{{ old('email', $application->email) }}" required>
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
                       value="{{ old('age', $application->age) }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Nationality:</label>
                <input type="text" class="form-control" name="nationality" 
                       value="{{ old('nationality', $application->nationality) }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Religion:</label>
                <input type="text" class="form-control" name="religion" 
                       value="{{ old('religion', $application->religion) }}" required>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-9">
                <label class="form-label">Permanent Home Address:</label>
                <input type="text" class="form-control" name="address" 
                       value="{{ old('address', $application->address) }}" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Zip Code:</label>
                <input type="text" class="form-control" name="zip_code" 
                       value="{{ old('zip_code', $application->zip_code) }}" required>
            </div>
        </div>

        <div class="row g-3 mt-1 mb-4">
            <div class="col-12 col-md-9">
                <label class="form-label">Contact Person (Parent/Guardian):</label>
                <input type="text" class="form-control" name="contact_person" 
                       value="{{ old('contact_person', $application->contact_person) }}" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Contact Number:</label>
                <input type="text" class="form-control" name="contact_number" 
                       value="{{ old('contact_number', $application->contact_number) }}" required>
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Last School Attended -->
        <h5 class="fw-bold mb-3">LAST SCHOOL ATTENDED</h5>
        <div class="row g-3">
            <div class="col-12 col-md-12">
                <label class="form-label">Track / Strand:</label>
                <select class="form-select" name="strand_id" required>
                    @foreach($strand as $program)
                        <option value="{{ $program->id }}"
                            {{ old('choice_first', $application->choice_first) == $program->id ? 'selected' : '' }}>
                            {{ $program->program }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-3 mt-1 mb-4">
            <div class="col-12 col-md-4">
                <label class="form-label">School Name:</label>
                <input type="text" class="form-control" name="school_name" 
                       value="{{ old('school_name', $application->school_name) }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">School Address:</label>
                <input type="text" class="form-control" name="school_address" 
                       value="{{ old('school_address', $application->school_address) }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Zip Code:</label>
                <input type="text" class="form-control" name="school_zip" 
                       value="{{ old('school_zip', $application->school_zip) }}">
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Program Preference -->
        <h5 class="fw-bold mb-3">PROGRAM PREFERENCE</h5>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label">First Choice:</label>
                <select class="form-select" name="choice_first" required>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}"
                            {{ old('choice_first', $application->choice_first) == $program->id ? 'selected' : '' }}>
                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Second Choice:</label>
                <select class="form-select" name="choice_second" required>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}"
                            {{ old('choice_second', $application->choice_second) == $program->id ? 'selected' : '' }}>
                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Third Choice:</label>
                <select class="form-select" name="choice_third" required>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}"
                            {{ old('choice_third', $application->choice_third) == $program->id ? 'selected' : '' }}>
                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
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
                <label class="form-label">Year Level: <span class="text-danger">*</span></label>
                <select class="form-select" name="year_level" required>
                    <option value="1st Year" {{ old('year_level', $application->year_level ?? '') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                    <option value="2nd Year" {{ old('year_level', $application->year_level ?? '') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3rd Year" {{ old('year_level', $application->year_level ?? '') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4th Year" {{ old('year_level', $application->year_level ?? '') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                    <option value="5th Year" {{ old('year_level', $application->year_level ?? '') == '5th Year' ? 'selected' : '' }}>5th Year</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Applicant Status: <span class="text-danger">*</span></label>
                <select class="form-select" name="applicant_status" required>
                    <option value="Freshman" {{ old('applicant_status', $application->applicant_status ?? '') == 'Freshman' ? 'selected' : '' }}>Freshman</option>
                    <option value="Transferee (K+12)" {{ old('applicant_status', $application->applicant_status ?? '') == 'Transferee (K+12)' ? 'selected' : '' }}>Transferee (K+12)</option>
                    <option value="Transferee (Old)" {{ old('applicant_status', $application->applicant_status ?? '') == 'Transferee (Old)' ? 'selected' : '' }}>Transferee (Old)</option>
                    <option value="Second Courser" {{ old('applicant_status', $application->applicant_status ?? '') == 'Second Courser' ? 'selected' : '' }}>Second Courser</option>
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



        <!-- Admission Result -->
    <div id="remarks_section" style="display: none;">
        <hr class="border-2 border-warning opacity-75 my-4">
        <h5 class="fw-bold mb-3">ADMISSION RESULT</h5>
        <div class="row mb-4">
            <div class="col-6">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr class="freshman">
                                <th class="text-center" width="150">Subtest</th>
                                <th class="text-center" colspan="2">Raw Score</th>
                                <th class="text-center" colspan="2">High School Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subtests as $subtest)
                            <tr class="freshman">
                                <td>{{ $subtest->name }}</td>
                                <td colspan="2">
                                    <input class="form-control text-center" type="text"
                                        name="rs[{{ $subtest->id }}]" value="{{ $subtest->result->rs }}" required>
                                </td>
                                <td colspan="2">
                                    <input class="form-control text-center" type="text"
                                        name="hg[{{ $subtest->id }}]" value="{{ $subtest->result->hg }}" required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


        <!-- Signatories -->
    <div id="signatories_section" style="display: none;">
        <hr class="border-2 border-warning opacity-75 my-4">
        <h5 class="fw-bold mb-3">SIGNATORIES</h5>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label class="form-label">Certifier Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="certifier_name" 
                    value="{{ old('certifier_name', $application->certifier_name ?? Auth::user()->name) }}" placeholder="Enter Certifier Name" required>
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

@endsection