@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">College Application Form</h2>
        <a href="{{ route('admission.college.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
        </a>
    </div><br>

    <form action="{{ route('admission.college.store') }}" method="POST">
        @csrf

        <!-- Student Information -->
        <h5 class="fw-bold mb-3">STUDENT INFORMATION</h5>

        <!-- Student Search -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label">Search Existing Student (Start typing last name):</label>
                <input type="text" class="form-control" id="studentSearch" placeholder="Type last name to search existing students...">
                <div id="searchResults" class="list-group mt-2" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                <small class="text-muted">If the student exists in our system, select from dropdown to auto-fill the form. Double-click on name fields to edit.</small>
            </div>
        </div>

        <input type="hidden" name="elog_id" id="elog_id" value="{{ $elog->id ?? '' }}">

        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label">Last Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control readonly-field" name="lastname" id="lastname" placeholder="Enter Last Name" required readonly>
                <small class="text-info"><i class="bi bi-info-circle"></i> Double-click to edit</small>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">First Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control readonly-field" name="firstname" id="firstname" placeholder="Enter First Name" required readonly>
                <small class="text-info"><i class="bi bi-info-circle"></i> Double-click to edit</small>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Middle Name:</label>
                <input type="text" class="form-control readonly-field" name="middlename" id="middlename" placeholder="Enter Middle Name" readonly>
                <small class="text-info"><i class="bi bi-info-circle"></i> Double-click to edit</small>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Suffix (Jr., Sr., I, II, etc):</label>
                <input type="text" class="form-control readonly-field" name="suffix" id="suffix" placeholder="Enter Suffix" readonly>
                <small class="text-info"><i class="bi bi-info-circle"></i> Double-click to edit</small>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-3">
                <label class="form-label">Gender: <span class="text-danger">*</span></label>
                <select class="form-select" name="gender" required>
                    <option value="" disabled selected>Choose Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Mobile No.: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mobile_no" placeholder="Enter Mobile No." required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Email Address: <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" placeholder="Enter Email Address" required>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-2">
                <label class="form-label">Date of Birth: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="dob" required>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Age: <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="age" placeholder="Enter Age" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Nationality: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nationality" value="Filipino" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Religion: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="religion" value="Roman Catholic" required>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-9">
                <label class="form-label">Permanent Home Address: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="address" placeholder="Enter Permanent Home Address" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Zip Code: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="zip_code" placeholder="Enter Zip Code" required>
            </div>
        </div>

        <div class="row g-3 mt-1 mb-4">
            <div class="col-12 col-md-9">
                <label class="form-label">Contact Person (Parent/Guardian): <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="contact_person" placeholder="Enter Contact Person" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Contact Number: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="contact_number" placeholder="Enter Contact Number" required>
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Last School Attended -->
        <h5 class="fw-bold mb-3">LAST SCHOOL ATTENDED</h5>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Track / Strand: <span class="text-danger">*</span></label>
                <select class="form-select" name="strand_id" required>
                    @foreach($strand as $key => $program)
                        <option value="{{ $program->id }}" {{ $key === 0 ? 'selected' : '' }}>
                            {{ $program->program }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-3 mt-1 mb-4">
            <div class="col-12 col-md-4">
                <label class="form-label">School Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="school_name" placeholder="Enter School Name" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">School Address: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="school_address" placeholder="Enter School Address" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Zip Code: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="school_zip" placeholder="Enter Zip Code" required>
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Program Preference -->
        <h5 class="fw-bold mb-3">PROGRAM PREFERENCE</h5>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label">First Choice: <span class="text-danger">*</span></label>
                <select class="form-select" name="choice_first" required>
                    @foreach($programs as $key => $program)
                        <option value="{{ $program->id }}" {{ $key === 0 ? 'selected' : '' }}>
                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Second Choice: <span class="text-danger">*</span></label>
                <select class="form-select" name="choice_second" required>
                    @foreach($programs as $key => $program)
                        <option value="{{ $program->id }}" {{ $key === 1 ? 'selected' : '' }}>
                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Third Choice: <span class="text-danger">*</span></label>
                <select class="form-select" name="choice_third" required>
                    @foreach($programs as $key => $program)
                        <option value="{{ $program->id }}" {{ $key === 2 ? 'selected' : '' }}>
                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
                        </option>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const studentSearch = document.getElementById('studentSearch');
        const searchResults = document.getElementById('searchResults');
        const readonlyFields = document.querySelectorAll('.readonly-field');
        let searchTimeout;
        let isEditing = false;

        // Double-click to enable editing
        readonlyFields.forEach(field => {
            field.addEventListener('dblclick', function() {
                if (!isEditing) {
                    enableEditing(this);
                }
            });

            // Save on blur (when field loses focus)
            field.addEventListener('blur', function() {
                if (isEditing) {
                    disableEditing(this);
                }
            });

            // Save on Enter key
            field.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && isEditing) {
                    disableEditing(this);
                }
            });
        });

        function enableEditing(field) {
            isEditing = true;
            field.readOnly = false;
            field.classList.remove('readonly-field');
            field.classList.add('editing-field');
            field.focus();
            field.select();
            
            // Show editing indicator
            const smallText = field.parentElement.querySelector('small');
            if (smallText) {
                smallText.innerHTML = '<i class="bi bi-pencil-square text-warning"></i> Editing... Press Enter or click away to save';
                smallText.className = 'text-warning';
            }
        }

        function disableEditing(field) {
            isEditing = false;
            field.readOnly = true;
            field.classList.remove('editing-field');
            field.classList.add('readonly-field');
            
            // Restore original indicator
            const smallText = field.parentElement.querySelector('small');
            if (smallText) {
                smallText.innerHTML = '<i class="bi bi-info-circle"></i> Double-click to edit';
                smallText.className = 'text-info';
            }

            // Validate required fields
            if ((field.id === 'lastname' || field.id === 'firstname') && !field.value.trim()) {
                showToast('Required fields cannot be empty!', 'error');
                enableEditing(field);
                return;
            }

            showToast('Changes saved!', 'success');
        }

        // Search students when typing
        studentSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                searchStudents(query);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!studentSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        function searchStudents(query) {
            fetch(`{{ route('admission.college.search-students') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(students => {
                    searchResults.innerHTML = '';
                    
                    if (students.length > 0) {
                        students.forEach(student => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = `
                                <strong>${student.last_name}, ${student.first_name}</strong>
                                ${student.middle_name ? ' ' + student.middle_name : ''}
                                ${student.suffix ? ' ' + student.suffix : ''}
                            `;
                            item.addEventListener('click', () => selectStudent(student.id));
                            searchResults.appendChild(item);
                        });
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error searching students:', error);
                    searchResults.style.display = 'none';
                });
        }

        function selectStudent(studentId) {
            fetch(`{{ route('admission.college.student-details', '') }}/${studentId}`)
                .then(response => response.json())
                .then(student => {
                    // Auto-fill the form fields with name data only
                    document.getElementById('lastname').value = student.last_name || '';
                    document.getElementById('firstname').value = student.first_name || '';
                    document.getElementById('middlename').value = student.middle_name || '';
                    document.getElementById('suffix').value = student.suffix || '';

                    // Hide search results and clear search field
                    searchResults.style.display = 'none';
                    studentSearch.value = '';

                    // Show success message
                    showToast('Student name loaded successfully! Double-click on any name field to make edits.', 'success');
                })
                .catch(error => {
                    console.error('Error fetching student details:', error);
                    showToast('Error loading student information', 'error');
                });
        }

        function showToast(message, type = 'success') {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.custom-toast');
            existingToasts.forEach(toast => toast.remove());

            // Create new toast
            const toast = document.createElement('div');
            toast.className = `custom-toast alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.minWidth = '300px';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi ${type === 'error' ? 'bi-exclamation-triangle' : 'bi-check-circle'} me-2"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 4000);
        }

        // Form submission validation
        document.getElementById('collegeForm').addEventListener('submit', function(e) {
            const lastname = document.getElementById('lastname').value.trim();
            const firstname = document.getElementById('firstname').value.trim();
            
            if (!lastname || !firstname) {
                e.preventDefault();
                showToast('Please fill in all required name fields!', 'error');
                return false;
            }
        });

        @if(session('show_success_modal'))
            document.getElementById("applicationNumber").innerText = "{{ session('application_number') }}";
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>

<!-- Include Success Modal -->
@include('Components.Admission.success-modal')

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