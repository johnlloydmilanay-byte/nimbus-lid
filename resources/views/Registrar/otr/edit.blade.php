@extends('layouts.master')

@section('content')
<div class="col-12">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h3 class="fw-bold mb-1">Edit OTR Record</h3>
            <p class="text-muted mb-0">Editing record for <strong>{{ $otr->First_Name }} {{ $otr->Last_Name }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('registrar.otr.show', $otr->id) }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" form="editOtrForm" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save Changes
            </button>
        </div>
    </div>

    <!-- Student Profile Header -->
    <div class="card shadow-sm mb-4 border-0 bg-light">
        <div class="card-body text-center">
            <div class="position-relative d-inline-block">
                @if($otr->Photo_Path && Storage::disk('public')->exists($otr->Photo_Path))
                    <img src="{{ asset('storage/' . $otr->Photo_Path) }}" alt="Student Photo" 
                         class="rounded-circle shadow-sm border border-4 border-white" 
                         style="width:130px; height:130px; object-fit:cover;">
                @else
                    <img src="{{ asset('assets/photos/default.jpg') }}" alt="Default Photo" 
                         class="rounded-circle shadow-sm border border-4 border-white" 
                         style="width:130px; height:130px; object-fit:cover;">
                @endif
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#photoModal">
                        <i class="fas fa-camera me-1"></i> Change Photo
                    </button>
                </div>
            </div>
            
            <h2 class="fw-bold mt-3 mb-1 text-uppercase">{{ $otr->Last_Name }}, {{ $otr->First_Name }}</h2>
            <h5 class="text-muted mb-2">{{ $otr->Middle_Name }}</h5>
            
            <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                <span class="badge bg-primary fs-6 px-3 py-2">
                    {{ $otr->program ? $otr->program->name : 'No Program Assigned' }}
                </span>
            </div>
            <div class="text-muted small">
                <i class="fas fa-id-card me-1"></i> {{ $otr->Student_ID }}
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('registrar.otr.update', $otr->id) }}" id="editOtrForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column: Main Info -->
            <div class="col-lg-8">
                
                <!-- Student Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        <i class="fa fa-user-graduate me-2 text-primary"></i> Student Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">First Name *</label>
                                <input type="text" name="First_Name" class="form-control @error('First_Name') is-invalid @enderror" 
                                       value="{{ old('First_Name', $otr->First_Name) }}" required>
                                @error('First_Name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Middle Name</label>
                                <input type="text" name="Middle_Name" class="form-control @error('Middle_Name') is-invalid @enderror" 
                                       value="{{ old('Middle_Name', $otr->Middle_Name) }}">
                                @error('Middle_Name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Last Name *</label>
                                <input type="text" name="Last_Name" class="form-control @error('Last_Name') is-invalid @enderror" 
                                       value="{{ old('Last_Name', $otr->Last_Name) }}" required>
                                @error('Last_Name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Student ID *</label>
                                <input type="text" name="Student_ID" class="form-control @error('Student_ID') is-invalid @enderror" 
                                       value="{{ old('Student_ID', $otr->Student_ID) }}" required>
                                @error('Student_ID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Degree/Course</label>
                                <select name="Degree_Course" class="form-select @error('Degree_Course') is-invalid @enderror">
                                    <option value="">Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('Degree_Course', $otr->Degree_Course) == $program->id ? 'selected' : '' }}>
                                            {{ $program->code }} - {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('Degree_Course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Date of Graduation</label>
                                <input type="date" name="Date_of_Graduation" class="form-control @error('Date_of_Graduation') is-invalid @enderror" 
                                       value="{{ old('Date_of_Graduation', $otr->Date_of_Graduation ? $otr->Date_of_Graduation->format('Y-m-d') : '') }}">
                                @error('Date_of_Graduation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">NSTP Serial No.</label>
                                <input type="text" name="NSTP_Serial_Number" class="form-control @error('NSTP_Serial_Number') is-invalid @enderror" 
                                       value="{{ old('NSTP_Serial_Number', $otr->NSTP_Serial_Number) }}">
                                @error('NSTP_Serial_Number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr class="my-3">

                        <!-- Static Notes Area -->
                        <div class="alert alert-light border border-secondary">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-semibold">Exemption Note</label>
                                    <textarea name="Exemption_Note" class="form-control @error('Exemption_Note') is-invalid @enderror" rows="2">{{ old('Exemption_Note', $otr->Exemption_Note ?? 'Exempted from the Issuance of Special Order (S.O.)') }}</textarea>
                                    @error('Exemption_Note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-semibold">Accreditation Level</label>
                                    <input type="text" name="Accreditation_Level" class="form-control @error('Accreditation_Level') is-invalid @enderror" 
                                           value="{{ old('Accreditation_Level', $otr->Accreditation_Level ?? 'PACUCOA Re-Accredited Level II') }}">
                                    @error('Accreditation_Level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label text-muted small fw-semibold">CHED Memo Order</label>
                                    <input type="text" name="CHED_Memo_Order" class="form-control @error('CHED_Memo_Order') is-invalid @enderror" 
                                           value="{{ old('CHED_Memo_Order', $otr->CHED_Memo_Order ?? 'CHED Memo Order No. 01, s. 2005') }}">
                                    @error('CHED_Memo_Order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admission / Entrance Data -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        <i class="fa fa-school me-2 text-primary"></i> Admission / Entrance Data
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Admission Credentials</label>
                                <input type="text" name="Admission_Credentials" class="form-control @error('Admission_Credentials') is-invalid @enderror" 
                                       value="{{ old('Admission_Credentials', $otr->Admission_Credentials) }}">
                                @error('Admission_Credentials')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Category</label>
                                <input type="text" name="Category" class="form-control @error('Category') is-invalid @enderror" 
                                       value="{{ old('Category', $otr->Category) }}">
                                @error('Category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">School Last Attended</label>
                                <input type="text" name="School_Last_Attended" class="form-control @error('School_Last_Attended') is-invalid @enderror" 
                                       value="{{ old('School_Last_Attended', $otr->School_Last_Attended) }}">
                                @error('School_Last_Attended')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">School Year Last Attended</label>
                                <input type="text" name="School_Year_Last_Attended" class="form-control @error('School_Year_Last_Attended') is-invalid @enderror" 
                                       value="{{ old('School_Year_Last_Attended', $otr->School_Year_Last_Attended) }}">
                                @error('School_Year_Last_Attended')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-semibold">School Address</label>
                                <textarea name="School_Address" class="form-control @error('School_Address') is-invalid @enderror" rows="2">{{ old('School_Address', $otr->School_Address) }}</textarea>
                                @error('School_Address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Semester/Year Admitted</label>
                                <input type="text" name="Semester_Year_Admitted" class="form-control @error('Semester_Year_Admitted') is-invalid @enderror" 
                                       value="{{ old('Semester_Year_Admitted', $otr->Semester_Year_Admitted) }}">
                                @error('Semester_Year_Admitted')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">College</label>
                                <input type="text" name="College" class="form-control @error('College') is-invalid @enderror" 
                                       value="{{ old('College', $otr->College) }}">
                                @error('College')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        <i class="fa fa-id-card me-2 text-primary"></i> Personal Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-semibold">Address</label>
                                <textarea name="Address" class="form-control @error('Address') is-invalid @enderror" rows="2">{{ old('Address', $otr->Address) }}</textarea>
                                @error('Address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Birth Date</label>
                                <input type="date" name="Birth_Date" class="form-control @error('Birth_Date') is-invalid @enderror" 
                                       value="{{ old('Birth_Date', $otr->Birth_Date ? $otr->Birth_Date->format('Y-m-d') : '') }}">
                                @error('Birth_Date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Birth Place</label>
                                <input type="text" name="Birth_Place" class="form-control @error('Birth_Place') is-invalid @enderror" 
                                       value="{{ old('Birth_Place', $otr->Birth_Place) }}">
                                @error('Birth_Place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-semibold">Gender</label>
                                <select name="Gender" class="form-select @error('Gender') is-invalid @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('Gender', $otr->Gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('Gender', $otr->Gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('Gender', $otr->Gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('Gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Citizenship</label>
                                <input type="text" name="Citizenship" class="form-control @error('Citizenship') is-invalid @enderror" 
                                       value="{{ old('Citizenship', $otr->Citizenship) }}">
                                @error('Citizenship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Religion</label>
                                <input type="text" name="Religion" class="form-control @error('Religion') is-invalid @enderror" 
                                       value="{{ old('Religion', $otr->Religion) }}">
                                @error('Religion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Processing Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        <i class="fa fa-file-signature me-2 text-primary"></i> Document Processing
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Prepared By</label>
                            <input type="text" name="Prepared_By" class="form-control @error('Prepared_By') is-invalid @enderror" 
                                   value="{{ old('Prepared_By', $otr->Prepared_By) }}">
                            @error('Prepared_By')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Checked By</label>
                            <input type="text" name="Checked_By" class="form-control @error('Checked_By') is-invalid @enderror" 
                                   value="{{ old('Checked_By', $otr->Checked_By) }}">
                            @error('Checked_By')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Dean</label>
                            <input type="text" name="Dean_Name" class="form-control @error('Dean_Name') is-invalid @enderror" 
                                   value="{{ old('Dean_Name', $otr->Dean_Name) }}">
                            @error('Dean_Name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Registrar</label>
                            <input type="text" name="Registrar_Name" class="form-control @error('Registrar_Name') is-invalid @enderror" 
                                   value="{{ old('Registrar_Name', $otr->Registrar_Name ?? 'MICHELLE J. BARBACENA-LLANTO, LPT') }}">
                            @error('Registrar_Name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Date Prepared</label>
                            <input type="date" name="Date_Prepared" class="form-control @error('Date_Prepared') is-invalid @enderror" 
                                   value="{{ old('Date_Prepared', $otr->Date_Prepared ? $otr->Date_Prepared->format('Y-m-d') : date('Y-m-d')) }}">
                            @error('Date_Prepared')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Photo Upload Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Change Student Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="photoForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="student_photo" class="form-label">Upload New Photo</label>
                        <input type="file" class="form-control" id="student_photo" name="student_photo" accept="image/*">
                        <div class="form-text">Max file size: 5MB. Accepted formats: JPG, PNG, JPEG.</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-3">
                            <img id="photoPreview" src="#" alt="Photo Preview" class="img-fluid rounded-circle d-none" style="width:150px; height:150px; object-fit:cover;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadPhoto()">Upload Photo</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Photo preview
    document.getElementById('student_photo').addEventListener('change', function(e) {
        const preview = document.getElementById('photoPreview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    function uploadPhoto() {
        const form = document.getElementById('photoForm');
        const formData = new FormData(form);
        
        // Add OTR ID
        formData.append('_method', 'PUT');
        
        fetch("{{ route('registrar.otr.update', $otr->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error uploading photo: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading photo');
        });
    }
</script>
@endsection
@endsection