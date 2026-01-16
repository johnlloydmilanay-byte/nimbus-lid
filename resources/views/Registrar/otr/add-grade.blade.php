@extends('layouts.master')

@section('content')
<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Manage Grades for {{ $otr->First_Name }} {{ $otr->Last_Name }}</h3>
        <a href="{{ route('registrar.otr.show', $otr->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Student
        </a>
    </div>

    <!-- Student Information Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Student Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    @if($otr->Photo_Path && Storage::disk('public')->exists($otr->Photo_Path))
                        <img src="{{ asset('storage/' . $otr->Photo_Path) }}" alt="Student Photo" 
                             class="rounded-circle shadow-sm border border-3 border-primary" 
                             style="width:80px; height:80px; object-fit:cover;">
                    @else
                        <img src="{{ asset('assets/photos/default.jpg') }}" alt="Default Photo" 
                             class="rounded-circle shadow-sm border border-3 border-primary" 
                             style="width:80px; height:80px; object-fit:cover;">
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Name:</strong> {{ $otr->Last_Name }}, {{ $otr->First_Name }} {{ $otr->Middle_Name }}</p>
                            <p class="mb-1"><strong>Student ID:</strong> {{ $otr->Student_ID }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Program:</strong> {{ $otr->program ? $otr->program->name : 'Not Specified' }}</p>
                            <p class="mb-1"><strong>College:</strong> {{ $otr->College ?? 'Not Specified' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Date of Graduation:</strong> {{ $otr->Date_of_Graduation ? $otr->Date_of_Graduation->format('F j, Y') : 'Not Specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Grades Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Existing Grades</h5>
            <span class="badge bg-light text-dark">{{ $otr->grades->count() }} Records</span>
        </div>
        <div class="card-body">
            @if($otr->grades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>School Year</th>
                                <th>Semester</th>
                                <th>Subject Code</th>
                                <th>Subject Title</th>
                                <th>Type</th>
                                <th>Final Rating</th>
                                <th>Units Earned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($otr->grades as $grade)
                                <tr>
                                    <td>{{ $grade->school_year }}</td>
                                    <td>{{ $grade->semester }}</td>
                                    <td>{{ $grade->subject_code }}</td>
                                    <td>{{ $grade->subject_title }}</td>
                                    <td>{{ $grade->type }}</td>
                                    <td>{{ $grade->final_rating }}</td>
                                    <td>{{ $grade->units_earned }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('registrar.otr.grade.edit', ['id' => $otr->id, 'gradeId' => $grade->id]) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('registrar.otr.grade.delete', ['id' => $otr->id, 'gradeId' => $grade->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this grade?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No grade records found for this student.
                </div>
            @endif
        </div>
    </div>

    <!-- Add New Grades Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Grades</h5>
            <div>
                <button type="button" id="addRowBtn" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i> Add Row
                </button>
                <button type="button" id="clearAllBtn" class="btn btn-warning btn-sm">
                    <i class="fas fa-eraser me-1"></i> Clear All
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="bulkGradeForm" method="POST" action="{{ route('registrar.otr.grade.bulk.store', $otr->id) }}">
                @csrf
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="gradesTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">School Year</th>
                                <th width="10%">Semester</th>
                                <th width="15%">Subject Code</th>
                                <th width="25%">Subject Title</th>
                                <th width="8%">Type</th>
                                <th width="10%">Final Rating</th>
                                <th width="10%">Units Earned</th>
                                <th width="5%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
                            <!-- Initial row -->
                            <tr class="grade-row" data-row-id="1">
                                <td class="text-center">1</td>
                                <td>
                                    <input type="text" name="grades[1][school_year]" class="form-control form-control-sm" placeholder="e.g. 2023-2024" required>
                                </td>
                                <td>
                                    <select name="grades[1][semester]" class="form-select form-select-sm" required>
                                        <option value="">Select</option>
                                        <option value="First">First</option>
                                        <option value="Second">Second</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="grades[1][subject_code]" class="form-control form-control-sm" required>
                                </td>
                                <td>
                                    <input type="text" name="grades[1][subject_title]" class="form-control form-control-sm" required>
                                </td>
                                <td>
                                    <select name="grades[1][type]" class="form-select form-select-sm" required>
                                        <option value="Lecture">Lecture</option>
                                        <option value="Lab">Lab</option>
                                        <option value="Lecture/Lab">Lecture/Lab</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="5" name="grades[1][final_rating]" class="form-control form-control-sm" required>
                                </td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="10" name="grades[1][units_earned]" class="form-control form-control-sm" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <a href="{{ route('registrar.otr.show', $otr->id) }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save All Grades
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let nextRowId = 2; // Start from 2 since we already have row 1
    let rowCount = 1;
    
    // Add row button
    document.getElementById('addRowBtn').addEventListener('click', function() {
        const tbody = document.getElementById('gradesTableBody');
        const newRow = document.createElement('tr');
        newRow.className = 'grade-row';
        newRow.setAttribute('data-row-id', nextRowId);
        
        newRow.innerHTML = `
            <td class="text-center">${++rowCount}</td>
            <td>
                <input type="text" name="grades[${nextRowId}][school_year]" class="form-control form-control-sm" placeholder="e.g. 2023-2024" required>
            </td>
            <td>
                <select name="grades[${nextRowId}][semester]" class="form-select form-select-sm" required>
                    <option value="">Select</option>
                    <option value="First">First</option>
                    <option value="Second">Second</option>
                    <option value="Summer">Summer</option>
                </select>
            </td>
            <td>
                <input type="text" name="grades[${nextRowId}][subject_code]" class="form-control form-control-sm" required>
            </td>
            <td>
                <input type="text" name="grades[${nextRowId}][subject_title]" class="form-control form-control-sm" required>
            </td>
            <td>
                <select name="grades[${nextRowId}][type]" class="form-select form-select-sm" required>
                    <option value="Lecture">Lecture</option>
                    <option value="Lab">Lab</option>
                    <option value="Lecture/Lab">Lecture/Lab</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0" max="5" name="grades[${nextRowId}][final_rating]" class="form-control form-control-sm" required>
            </td>
            <td>
                <input type="number" step="0.5" min="0" max="10" name="grades[${nextRowId}][units_earned]" class="form-control form-control-sm" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-row-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(newRow);
        nextRowId++;
    });
    
    // Remove row button (using event delegation)
    document.getElementById('gradesTableBody').addEventListener('click', function(e) {
        if (e.target.closest('.remove-row-btn')) {
            const row = e.target.closest('.grade-row');
            if (document.querySelectorAll('.grade-row').length > 1) {
                row.remove();
                updateRowNumbers();
            } else {
                alert('You must have at least one row.');
            }
        }
    });
    
    // Clear all button
    document.getElementById('clearAllBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all rows?')) {
            const tbody = document.getElementById('gradesTableBody');
            tbody.innerHTML = '';
            
            // Add back one empty row
            const newRow = document.createElement('tr');
            newRow.className = 'grade-row';
            newRow.setAttribute('data-row-id', '1');
            
            newRow.innerHTML = `
                <td class="text-center">1</td>
                <td>
                    <input type="text" name="grades[1][school_year]" class="form-control form-control-sm" placeholder="e.g. 2023-2024" required>
                </td>
                <td>
                    <select name="grades[1][semester]" class="form-select form-select-sm" required>
                        <option value="">Select</option>
                        <option value="First">First</option>
                        <option value="Second">Second</option>
                        <option value="Summer">Summer</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="grades[1][subject_code]" class="form-control form-control-sm" required>
                </td>
                <td>
                    <input type="text" name="grades[1][subject_title]" class="form-control form-control-sm" required>
                </td>
                <td>
                    <select name="grades[1][type]" class="form-select form-select-sm" required>
                        <option value="Lecture">Lecture</option>
                        <option value="Lab">Lab</option>
                        <option value="Lecture/Lab">Lecture/Lab</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" max="5" name="grades[1][final_rating]" class="form-control form-control-sm" required>
                </td>
                <td>
                    <input type="number" step="0.5" min="0" max="10" name="grades[1][units_earned]" class="form-control form-control-sm" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
            
            nextRowId = 2;
            rowCount = 1;
        }
    });
    
    // Update row numbers after deletion
    function updateRowNumbers() {
        const rows = document.querySelectorAll('.grade-row');
        rowCount = 0;
        
        rows.forEach(function(row) {
            const firstCell = row.querySelector('td:first-child');
            firstCell.textContent = ++rowCount;
        });
    }
    
    // Form submission
    document.getElementById('bulkGradeForm').addEventListener('submit', function(e) {
        // Check if at least one row is filled
        const rows = document.querySelectorAll('.grade-row');
        let hasValidRow = false;
        
        rows.forEach(function(row) {
            const schoolYear = row.querySelector('input[name*="school_year"]').value;
            const subjectCode = row.querySelector('input[name*="subject_code"]').value;
            
            if (schoolYear && subjectCode) {
                hasValidRow = true;
            }
        });
        
        if (!hasValidRow) {
            e.preventDefault();
            alert('Please fill in at least one complete grade row.');
        }
    });
});
</script>
@endpush