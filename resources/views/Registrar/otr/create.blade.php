{{-- resources/views/registrar/otr/create.blade.php --}}
@extends('layouts.master')

@push('styles')
{{-- Select2 CSS for Searchable Dropdown --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="mt-4 fw-bold mb-4">Create New OTR Record</h3>
        <a href="{{ route('registrar.otr.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to OTR List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('registrar.otr.store') }}" enctype="multipart/form-data" id="addOtrForm">
        @csrf
        
        <!-- Student Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa fa-user-graduate"></i> Student Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="Last_Name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Last_Name" name="Last_Name" required 
                               value="{{ old('Last_Name') }}">
                        @error('Last_Name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="First_Name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="First_Name" name="First_Name" required 
                               value="{{ old('First_Name') }}">
                        @error('First_Name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="Middle_Name" class="form-label fw-semibold">Middle Name</label>
                        <input type="text" class="form-control" id="Middle_Name" name="Middle_Name" 
                               value="{{ old('Middle_Name') }}">
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="Student_ID" class="form-label fw-semibold">Student ID Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Student_ID" name="Student_ID" required 
                               value="{{ old('Student_ID') }}">
                        @error('Student_ID')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="Degree_Course" class="form-label fw-semibold">Degree Course</label>
                        
                        <!-- SELECT2 DROPDOWN START -->
                        <select class="form-control select2" name="Degree_Course" id="Degree_Course" style="width: 100%;">
                            <option value="">Select Degree Course</option>
                            @if(isset($programs))
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}" {{ old('Degree_Course') == $prog->id ? 'selected' : '' }}>
                                        {{ $prog->code }} - {{ $prog->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('Degree_Course')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <!-- SELECT2 DROPDOWN END -->

                    </div>
                    <div class="col-md-4">
                        <label for="NSTP_Serial_Number" class="form-label fw-semibold">NSTP Serial Number</label>
                        <input type="text" class="form-control" id="NSTP_Serial_Number" name="NSTP_Serial_Number" 
                               value="{{ old('NSTP_Serial_Number') }}">
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="Exemption_Note" class="form-label fw-semibold">Exemption Note</label>
                        <textarea class="form-control" id="Exemption_Note" name="Exemption_Note" rows="2" readonly>Exempted from the Issuance of Special Order (S.O.)</textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="Accreditation_Level" class="form-label fw-semibold">Accreditation Level</label>
                        <input type="text" class="form-control" id="Accreditation_Level" name="Accreditation_Level" value="PACUCOA Re-Accredited Level II" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="CHED_Memo_Order" class="form-label fw-semibold">CHED Memo Order</label>
                        <input type="text" class="form-control" id="CHED_Memo_Order" name="CHED_Memo_Order" value="CHED Memo Order No. 01, s. 2005" readonly>
                    </div>
                </div>
                
                <!-- NEW FIELDS: Special Order Information -->
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="Special_Order_Number" class="form-label fw-semibold">Special Order Number</label>
                        <input type="text" class="form-control" id="Special_Order_Number" name="Special_Order_Number" 
                               value="{{ old('Special_Order_Number') }}">
                        <div class="form-text">Leave blank if exempted</div>
                    </div>
                    <div class="col-md-4">
                        <label for="Special_Order_Series" class="form-label fw-semibold">Special Order Series</label>
                        <input type="text" class="form-control" id="Special_Order_Series" name="Special_Order_Series" 
                               value="{{ old('Special_Order_Series') }}">
                        <div class="form-text">e.g., 2023-2024</div>
                    </div>
                    <div class="col-md-4">
                        <label for="Special_Order_Date_Issued" class="form-label fw-semibold">Special Order Date Issued</label>
                        <input type="date" class="form-control" id="Special_Order_Date_Issued" name="Special_Order_Date_Issued" 
                               value="{{ old('Special_Order_Date_Issued') }}">
                        <div class="form-text">Format: YYYY-MM-DD</div>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="Date_of_Graduation" class="form-label fw-semibold">Date of Graduation</label>
                        <input type="date" class="form-control" id="Date_of_Graduation" name="Date_of_Graduation" 
                               value="{{ old('Date_of_Graduation') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="student_photo" class="form-label fw-semibold">Student Photo</label>
                        <input type="file" class="form-control" id="student_photo" name="student_photo" accept="image/jpeg,image/png,image/jpg">
                        <div class="form-text">Accepted formats: JPG, JPEG, PNG (Max: 5MB)</div>
                        @error('student_photo')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Admission / Entrance Data -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa fa-school"></i> Admission / Entrance Data
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="Admission_Credentials" class="form-label fw-semibold">Admission Credentials</label>
                        <input type="text" class="form-control" id="Admission_Credentials" name="Admission_Credentials" 
                               value="{{ old('Admission_Credentials') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="Category" class="form-label fw-semibold">Category</label>
                        <input type="text" class="form-control" id="Category" name="Category" 
                               value="{{ old('Category') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="School_Last_Attended" class="form-label fw-semibold">School Last Attended</label>
                        <input type="text" class="form-control" id="School_Last_Attended" name="School_Last_Attended" 
                               value="{{ old('School_Last_Attended') }}">
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="School_Year_Last_Attended" class="form-label fw-semibold">School Year Last Attended</label>
                        <input type="text" class="form-control" id="School_Year_Last_Attended" name="School_Year_Last_Attended" 
                               value="{{ old('School_Year_Last_Attended') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="School_Address" class="form-label fw-semibold">School Address</label>
                        <input type="text" class="form-control" id="School_Address" name="School_Address" 
                               value="{{ old('School_Address') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="Semester_Year_Admitted" class="form-label fw-semibold">Semester/Year Admitted</label>
                        <input type="text" class="form-control" id="Semester_Year_Admitted" name="Semester_Year_Admitted" 
                               value="{{ old('Semester_Year_Admitted') }}">
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="College" class="form-label fw-semibold">College</label>
                        <input type="text" class="form-control" id="College" name="College" 
                               value="{{ old('College') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Personal Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa fa-id-card"></i> Personal Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="Address" class="form-label fw-semibold">Address</label>
                        <textarea class="form-control" id="Address" name="Address" rows="2">{{ old('Address') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label for="Birth_Date" class="form-label fw-semibold">Birth Date</label>
                        <input type="date" class="form-control" id="Birth_Date" name="Birth_Date" 
                               value="{{ old('Birth_Date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="Birth_Place" class="form-label fw-semibold">Birth Place</label>
                        <input type="text" class="form-control" id="Birth_Place" name="Birth_Place" 
                               value="{{ old('Birth_Place') }}">
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label for="Citizenship" class="form-label fw-semibold">Citizenship</label>
                        <input type="text" class="form-control" id="Citizenship" name="Citizenship" 
                               value="{{ old('Citizenship') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="Religion" class="form-label fw-semibold">Religion</label>
                        <input type="text" class="form-control" id="Religion" name="Religion" 
                               value="{{ old('Religion') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="Gender" class="form-label fw-semibold">Gender</label>
                        <select class="form-select" id="Gender" name="Gender">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('Gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('Gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('Gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Document Processing Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                <i class="fa fa-file-alt"></i> Document Processing Info
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="Prepared_By" class="form-label fw-semibold">Prepared By</label>
                        <input type="text" class="form-control" id="Prepared_By" name="Prepared_By" 
                               value="{{ old('Prepared_By') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="Checked_By" class="form-label fw-semibold">Checked By</label>
                        <input type="text" class="form-control" id="Checked_By" name="Checked_By" 
                               value="{{ old('Checked_By') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="Dean_Name" class="form-label fw-semibold">Dean Name</label>
                        <input type="text" class="form-control" id="Dean_Name" name="Dean_Name" 
                               value="{{ old('Dean_Name') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="Registrar_Name" class="form-label fw-semibold">Registrar Name</label>
                        <input type="text" class="form-control" id="Registrar_Name" name="Registrar_Name" value="MICHELLE J. BARBACENA-LLANTO, LPT" readonly>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label for="Date_Prepared" class="form-label fw-semibold">Date Prepared</label>
                        <input type="date" class="form-control" id="Date_Prepared" name="Date_Prepared" 
                               value="{{ old('Date_Prepared', date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('registrar.otr.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-custom">
                <i class="fas fa-save me-1"></i> Save Record
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- jQuery (Required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for searchable dropdown
        $('.select2').select2({
            placeholder: "Search for a program...",
            allowClear: true,
            theme: "bootstrap-5" // Optional theme
        });

        // Preview image before upload
        document.getElementById('student_photo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (5MB = 5 * 1024 * 1024 bytes)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size exceeds 5MB limit. Please choose a smaller file.');
                    event.target.value = ''; // Clear the file input
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, JPEG, or PNG).');
                    event.target.value = ''; // Clear the file input
                    return;
                }
            }
        });
    });
</script>
@endpush