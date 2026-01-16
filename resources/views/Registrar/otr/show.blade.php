@extends('layouts.master')

@section('content')
<div class="col-12">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h3 class="fw-bold mb-1">OTR Record Details</h3>
        <p class="text-muted mb-0">Viewing record for <strong>{{ $otr->First_Name }} {{ $otr->Last_Name }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        {{-- Navigation Buttons --}}
        <div class="btn-group">
            <a href="{{ route('registrar.otr.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('registrar.otr.edit', $otr->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
        
        {{-- Export Buttons --}}
        <div class="btn-group">
            <a href="{{ route('registrar.otr.export-grades', $otr->id) }}" 
               class="btn btn-success" 
               title="Download Grades Transcript (Excel)">
                <i class="fas fa-file-excel me-1"></i> Export Grades
            </a>
            <a href="{{ route('registrar.otr.pdf', $otr->id) }}" 
               class="btn btn-danger" 
               target="_blank"
               title="Download Personal Data (PDF)">
                <i class="fas fa-file-pdf me-1"></i> Download PDF
            </a>
        </div>
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
                            <label class="text-muted small fw-semibold">Date of Graduation</label>
                            <div class="fw-bold fs-5">
                                {{ $otr->Date_of_Graduation ? $otr->Date_of_Graduation->format('F j, Y') : 'Not Set' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold">NSTP Serial No.</label>
                            <div>{{ $otr->NSTP_Serial_Number ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <hr class="my-3">

                    <!-- Static Notes Area -->
                    <div class="alert alert-light border border-secondary">
                        <h6 class="alert-heading fw-bold fs-6">Exemption Note:</h6>
                        <p class="mb-2 small text-muted fst-italic">"{{ $otr->Exemption_Note ?? 'Exempted from the Issuance of Special Order (S.O.)' }}"</p>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <small class="d-block text-muted">Accreditation Level</small>
                                <span class="fw-bold">{{ $otr->Accreditation_Level ?? 'PACUCOA Re-Accredited Level II' }}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="d-block text-muted">CHED Memo Order</small>
                                <span class="fw-bold">{{ $otr->CHED_Memo_Order ?? 'CHED Memo Order No. 01, s. 2005' }}</span>
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
                            <label class="text-muted small fw-semibold">Admission Credentials</label>
                            <div>{{ $otr->Admission_Credentials ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold">Category</label>
                            <div>{{ $otr->Category ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold">School Last Attended</label>
                            <div>{{ $otr->School_Last_Attended ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold">School Year Last Attended</label>
                            <div>{{ $otr->School_Year_Last_Attended ?? '-' }}</div>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small fw-semibold">School Address</label>
                            <div>{{ $otr->School_Address ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold">Semester/Year Admitted</label>
                            <div>{{ $otr->Semester_Year_Admitted ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold">College</label>
                            <div>{{ $otr->College ?? '-' }}</div>
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
                            <label class="text-muted small fw-semibold">Address</label>
                            <div>{{ $otr->Address ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Birth Date</label>
                            <div>{{ $otr->Birth_Date ? $otr->Birth_Date->format('F j, Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Birth Place</label>
                            <div>{{ $otr->Birth_Place ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Gender</label>
                            <div>{{ $otr->Gender ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Citizenship</label>
                            <div>{{ $otr->Citizenship ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Religion</label>
                            <div>{{ $otr->Religion ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Processing Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4 sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-white fw-bold">
                    <i class="fa fa-file-signature me-2 text-primary"></i> Document Processing
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Prepared By</label>
                        <div class="fw-bold">{{ $otr->Prepared_By ?? 'Not Set' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Checked By</label>
                        <div class="fw-bold">{{ $otr->Checked_By ?? 'Not Set' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Dean</label>
                        <div class="fw-bold">{{ $otr->Dean_Name ?? 'Not Set' }}</div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Registrar</label>
                        <div class="fw-bold text-primary">{{ $otr->Registrar_Name ?? 'MICHELLE J. BARBACENA-LLANTO, LPT' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Date Prepared</label>
                        <div class="fw-bold">{{ $otr->Date_Prepared ? $otr->Date_Prepared->format('F j, Y') : date('F j, Y') }}</div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <form action="{{ route('registrar.otr.destroy', $otr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this record? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash-alt me-1"></i> Delete Record
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grades Section (Insert into show.blade.php) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
        <i class="fa fa-list-ol me-2 text-primary"></i> Grades / Records
        <a href="{{ route('registrar.otr.grade.add', $otr->id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Grade
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>School Year</th>
                        <th>Sem</th>
                        <th>Subject Code</th>
                        <th>Subject Title</th>
                        <th>Type</th>
                        <th>Rating</th>
                        <th>Units</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($otr->grades as $grade)
                    <tr>
                        <td>{{ $grade->school_year }}</td>
                        <td>{{ $grade->semester }}</td>
                        <td>{{ $grade->subject_code }}</td>
                        <td>{{ $grade->subject_title }}</td>
                        <td>{{ $grade->type }}</td>
                        <td class="text-center fw-bold">{{ $grade->final_rating }}</td>
                        <td class="text-center">{{ $grade->units_earned }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            No grades recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection