@extends('layouts.master')

@section('content')
<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Import OTR Records from Excel</h3>
        <a href="{{ route('registrar.otr.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to OTR List
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-import me-2"></i>Import Instructions</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>Before Importing:</h6>
                <ol class="mb-0">
                    <li>Download the <a href="{{ route('registrar.otr.template.generate') }}" class="fw-bold">Excel Template</a></li>
                    <li>Ensure your Excel file follows the exact format with two sheets:
                        <ul>
                            <li><strong>Sheet 1:</strong> "OTR Student Information" (for student data)</li>
                            <li><strong>Sheet 2:</strong> "Grades" (for grade records)</li>
                        </ul>
                    </li>
                    <li>Do not modify column headers or sheet names</li>
                    <li>Required fields are marked with *</li>
                </ol>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6>Import Errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('registrar.otr.import.store') }}" enctype="multipart/form-data" id="importForm">
                @csrf
                
                <div class="mb-4">
                    <label for="excel_file" class="form-label fw-bold">Select Excel File <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                    <div class="form-text">Accepted formats: .xlsx, .xls (Max: 10MB)</div>
                    @error('excel_file')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1">
                        <label class="form-check-label" for="skip_duplicates">
                            Skip duplicate Student IDs (continue with remaining records)
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('registrar.otr.index') }}" class="btn btn-light">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="importBtn">
                        <i class="fas fa-upload me-1"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Template Structure -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Template Structure</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Sheet 1: OTR Student Information</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Column</th>
                                    <th>Description</th>
                                    <th>Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>A: Student_ID</td>
                                    <td>Unique Student ID Number</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>B: Last_Name</td>
                                    <td>Student's Last Name</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>C: First_Name</td>
                                    <td>Student's First Name</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>D: Middle_Name</td>
                                    <td>Student's Middle Name</td>
                                    <td><span class="badge bg-success">No</span></td>
                                </tr>
                                <tr>
                                    <td>E: Degree_Course</td>
                                    <td>Program Code (e.g., BSN, BSA)</td>
                                    <td><span class="badge bg-success">No</span></td>
                                </tr>
                                <tr>
                                    <td>F: Date_of_Graduation</td>
                                    <td>YYYY-MM-DD format</td>
                                    <td><span class="badge bg-success">No</span></td>
                                </tr>
                                <tr>
                                    <td>G: NSTP_Serial_Number</td>
                                    <td>NSTP Serial Number</td>
                                    <td><span class="badge bg-success">No</span></td>
                                </tr>
                                <tr>
                                    <td>H: Exemption_Note</td>
                                    <td>Default: Exempted from the Issuance of Special Order (S.O.)</td>
                                    <td><span class="badge bg-success">No</span></td>
                                </tr>
                                <tr>
                                    <td>I: Accreditation_Level</td>
                                    <td>Default: PACUCOA Re-Accredited Level II</td>
                                    <td><span class="badge bg-success">No</span></td>
                                </tr>
                                <tr>
                                    <td>J: CHED_Memo_Order</td>
                                    <td>Default: CHED Memo Order No. 01, s. 2005</td>
                                    <td><span class="badge bg-success">No</span></td>
                                </tr>
                                <!-- Continue with other columns as needed -->
                            </tbody>
                        </table>
                        <small class="text-muted">Columns K to AB contain additional student information fields.</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6>Sheet 2: Grades/Collegiate Record</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Column</th>
                                    <th>Description</th>
                                    <th>Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>A: Student_ID</td>
                                    <td>Student ID (must match Sheet 1)</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>B: school_year</td>
                                    <td>e.g., 2023-2024</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>C: semester</td>
                                    <td>First, Second, or Summer</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>D: subject_code</td>
                                    <td>Subject Code</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>E: subject_title</td>
                                    <td>Subject Title</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>F: type</td>
                                    <td>Lecture, Lab, or Lecture/Lab</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>G: final_rating</td>
                                    <td>Numeric grade (0-5)</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>H: units_earned</td>
                                    <td>Units earned (0-10)</td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Important:</strong> The Excel file must have exactly these sheet names. Column headers should be in the first row.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const importForm = document.getElementById('importForm');
    const importBtn = document.getElementById('importBtn');
    
    importForm.addEventListener('submit', function(e) {
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Importing...';
    });
    
    // File validation
    document.getElementById('excel_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const validTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ];
            
            if (file.size > maxSize) {
                alert('File size exceeds 10MB limit.');
                e.target.value = '';
                return;
            }
            
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid Excel file (.xlsx or .xls).');
                e.target.value = '';
                return;
            }
        }
    });
});
</script>
@endpush