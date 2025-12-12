@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Chemical / Solutions / Mixtures Request Form</h2>
       <a href="{{ route('lid.reservations.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
       </a>
    </div><br>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lid.reservations.store') }}" method="POST">
        @csrf

        <!-- SECTION A: Borrower Information -->
        <h5 class="fw-bold mb-3">BORROWER INFORMATION</h5>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Borrower's Name: <span class="text-danger">*</span></label>
                <input type="text" name="borrower_name" class="form-control @error('borrower_name') is-invalid @enderror" placeholder="Enter Full Name" value="{{ old('borrower_name') }}" required>
                @error('borrower_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Borrower Type: <span class="text-danger">*</span></label>
                <select name="borrower_type" class="form-select @error('borrower_type') is-invalid @enderror" required>
                    <option value="" disabled>Select Type</option>
                    <option value="Faculty Member" {{ old('borrower_type') == 'Faculty Member' ? 'selected' : '' }}>Faculty Member</option>
                    <option value="Student" {{ old('borrower_type') == 'Student' ? 'selected' : '' }}>Student</option>
                </select>
                @error('borrower_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Date Requested: <span class="text-danger">*</span></label>
                <input type="date" name="date_requested" class="form-control @error('date_requested') is-invalid @enderror" value="{{ old('date_requested') }}" required>
                @error('date_requested')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-3">
                <label class="form-label">Term: <span class="text-danger">*</span></label>
                <select name="term" class="form-select @error('term') is-invalid @enderror" required>
                    <option value="" disabled>Select Term</option>
                    <option value="1st Semester" {{ old('term') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd Semester" {{ old('term') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    <option value="Summer" {{ old('term') == 'Summer' ? 'selected' : '' }}>Summer</option>
                    <option value="Special Term" {{ old('term') == 'Special Term' ? 'selected' : '' }}>Special Term</option>
                </select>
                @error('term')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Purpose: <span class="text-danger">*</span></label>
                <div class="d-flex gap-4">
                    <div>
                        <input type="checkbox" name="purpose[]" value="Laboratory Activity" {{ in_array('Laboratory Activity', old('purpose', [])) ? 'checked' : '' }}>
                        <label>Laboratory Activity</label>
                    </div>
                    <div>
                        <input type="checkbox" name="purpose[]" value="Student Thesis" {{ in_array('Student Thesis', old('purpose', [])) ? 'checked' : '' }}>
                        <label>Student Thesis</label>
                    </div>
                    <div>
                        <input type="checkbox" name="purpose[]" value="Faculty Research" {{ in_array('Faculty Research', old('purpose', [])) ? 'checked' : '' }}>
                        <label>Faculty Research</label>
                    </div>
                </div>
                @error('purpose')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Room No.: <span class="text-danger">*</span></label>
                <input type="text" name="room_no" class="form-control @error('room_no') is-invalid @enderror" placeholder="Enter Room No." value="{{ old('room_no') }}" required>
                @error('room_no')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-3">
                <label class="form-label">Start Date: <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">End Date: <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Number of Groups: <span class="text-danger">*</span></label>
                <input type="number" name="number_of_groups" class="form-control @error('number_of_groups') is-invalid @enderror" min="1" value="{{ old('number_of_groups') }}" required>
                @error('number_of_groups')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Time: <span class="text-danger">*</span></label>
                <input type="time" name="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time') }}" required>
                @error('time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- SECTION B: Course Information -->
        <h5 class="fw-bold mb-3">COURSE / ACTIVITY INFORMATION</h5>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Program:</label>
                <input type="text" name="program" class="form-control" placeholder="e.g., BS Biology" value="{{ old('program') }}">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Year Level & Section/Block:</label>
                <input type="text" name="year_section" class="form-control" placeholder="e.g., 3A / Block 1" value="{{ old('year_section') }}">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Subject/Course Code:</label>
                <input type="text" name="subject_code" class="form-control" placeholder="e.g., CHEM 201" value="{{ old('subject_code') }}">
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-md-8">
                <label class="form-label">Subject/Course Description:</label>
                <input type="text" name="subject_description" class="form-control" value="{{ old('subject_description') }}">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Activity No.:</label>
                <input type="text" name="activity_no" class="form-control" value="{{ old('activity_no') }}">
            </div>
        </div>

        <div class="row g-3 mt-1 mb-4">
            <div class="col-12">
                <label class="form-label">Activity Title:</label>
                <input type="text" name="activity_title" class="form-control" value="{{ old('activity_title') }}">
            </div>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- SECTION C: Non-Regulated Chemicals -->
        <h5 class="fw-bold mb-3">NON-REGULATED CHEMICALS</h5>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:25%">Chemical Name</th>
                        <th style="width:10%">Sol'n</th>
                        <th style="width:10%">Conc</th>
                        <th style="width:20%">If Sol'n, Concentration</th>
                        <th style="width:20%">Volume/Weight Needed</th>
                        <th style="width:25%">Working Instruction</th>
                    </tr>
                </thead>
                <tbody id="nonRegulatedTable">
                    <tr>
                        <td>
                            <select class="form-select chemical-select" name="nonregulated[0][name]" data-type="normal">
                                <option value="">Select Chemical...</option>
                                @foreach ($chemicals as $chem)
                                    <option value="{{ $chem->name }}"
                                        data-soln="{{ $chem->solution }}"
                                        data-conc="{{ $chem->concentration }}"
                                        data-cval="{{ $chem->concentration_value }}"
                                        data-vol="{{ $chem->volume }}">
                                        {{ $chem->name }} (Available: {{ $chem->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="text-center">
                            <input type="checkbox" name="nonregulated[0][soln]" value="1">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="nonregulated[0][conc]" value="1">
                        </td>
                        <td>
                            <input type="text" name="nonregulated[0][concentration_value]" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="nonregulated[0][volume]" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="nonregulated[0][instruction]" class="form-control">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add Row Button -->
        <div class="mb-4">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow('nonRegulatedTable','nonregulated')">
                <i class="bi bi-plus-circle"></i> Add Row
            </button>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- SECTION D: PDEA-Regulated Chemicals -->
        <h5 class="fw-bold mb-3 text-danger">PDEA-REGULATED CHEMICALS</h5>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:25%">Chemical Name</th>
                        <th style="width:10%">Sol'n</th>
                        <th style="width:10%">Conc</th>
                        <th style="width:20%">If Sol'n, Concentration</th>
                        <th style="width:20%">Volume/Weight Needed</th>
                        <th style="width:25%">Working Instruction</th>
                    </tr>
                </thead>
                <tbody id="regulatedTable">
                    <tr>
                        <td>
                            <select class="form-select chemical-select" name="regulated[0][name]" data-type="pdea">
                                <option value="">Select Chemical...</option>
                                @foreach ($pdeaChemicals as $chem)
                                    <option value="{{ $chem->name }}"
                                        data-soln="{{ $chem->solution }}"
                                        data-conc="{{ $chem->concentration }}"
                                        data-cval="{{ $chem->concentration_value }}"
                                        data-vol="{{ $chem->volume }}">
                                        {{ $chem->name }} (Available: {{ $chem->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" name="regulated[0][soln]" value="1"></td>
                        <td class="text-center"><input type="checkbox" name="regulated[0][conc]" value="1"></td>
                        <td><input type="text" name="regulated[0][concentration_value]" class="form-control"></td>
                        <td><input type="text" name="regulated[0][volume]" class="form-control"></td>
                        <td><input type="text" name="regulated[0][instruction]" class="form-control"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-4">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="addRow('regulatedTable','regulated')">
                <i class="bi bi-plus-circle"></i> Add Row
            </button>
        </div>

        <hr class="border-2 border-warning opacity-75 my-4">

        <!-- Submit Button -->
        <div class="col-12 mb-5">
            <button type="submit" class="btn btn-custom px-4 w-100">
                <i class="bi bi-save"></i> Submit Request
            </button>
        </div>

    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    let rowCount = 1;

    function addRow(tableId, groupName) {
    const table = document.getElementById(tableId);
    const index = rowCount++;

    let chemicalOptions = '';

    if (groupName === 'nonregulated') {
        @foreach ($chemicals as $chem)
            chemicalOptions += `<option value="{{ $chem->name }}"
                data-soln="{{ $chem->solution }}"
                data-conc="{{ $chem->concentration }}"
                data-cval="{{ $chem->concentration_value }}"
                data-vol="{{ $chem->volume }}">
                {{ $chem->name }} (Available: {{ $chem->quantity }})
            </option>`;
        @endforeach
    } else if (groupName === 'regulated') {
        @foreach ($pdeaChemicals as $chem)
            chemicalOptions += `<option value="{{ $chem->name }}"
                data-soln="{{ $chem->solution }}"
                data-conc="{{ $chem->concentration }}"
                data-cval="{{ $chem->concentration_value }}"
                data-vol="{{ $chem->volume }}">
                {{ $chem->name }} (Available: {{ $chem->quantity }})
            </option>`;
        @endforeach
    }

    const row = `
        <tr>
            <td>
                <select class="form-select chemical-select" name="${groupName}[${index}][name]">
                    <option value="">Select Chemical...</option>
                    ${chemicalOptions}
                </select>
            </td>
            <td class="text-center"><input type="checkbox" name="${groupName}[${index}][soln]" value="1"></td>
            <td class="text-center"><input type="checkbox" name="${groupName}[${index}][conc]" value="1"></td>
            <td><input type="text" name="${groupName}[${index}][concentration_value]" class="form-control"></td>
            <td><input type="text" name="${groupName}[${index}][volume]" class="form-control"></td>
            <td><input type="text" name="${groupName}[${index}][instruction]" class="form-control"></td>
        </tr>
    `;

    table.insertAdjacentHTML('beforeend', row);

    // Reinitialize Select2 for new rows
    $('.chemical-select').select2();
}

</script>

<script>
$(document).ready(function () {
    $('.chemical-select').select2();

    $(document).on('change', '.chemical-select', function () {
        let option = $(this).find(':selected');
        let row = $(this).closest('tr');

        // Autofill fields
        row.find('input[name$="[soln]"]').prop('checked', option.data('soln') == 1);
        row.find('input[name$="[conc]"]').prop('checked', option.data('conc') == 1);
        row.find('input[name$="[concentration_value]"]').val(option.data('cval') || '');
        row.find('input[name$="[volume]"]').val(option.data('vol') || '');
    });
});
</script>

@include('Components.LID.success-modal')

@endsection