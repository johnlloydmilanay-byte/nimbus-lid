@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Faculty Chemical Request Form</h2>
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

    <!-- Progress Indicator -->
    <div class="progress mb-4" style="height: 30px;">
        <div class="progress-bar" role="progressbar" style="width: 16.67%;" aria-valuenow="16.67" aria-valuemin="0" aria-valuemax="100">Phase 1 of 6</div>
    </div>

    <form action="{{ route('lid.reservations.store') }}" method="POST" id="chemicalRequestForm">
        @csrf
        <input type="hidden" name="borrower_type" value="Faculty Member">

        <!-- Phase 1: Faculty Information -->
        <div class="phase" id="phase1">
            <h5 class="fw-bold mb-3">FACULTY INFORMATION</h5>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Faculty Name: <span class="text-danger">*</span></label>
                    <input type="text" name="borrower_name" class="form-control @error('borrower_name') is-invalid @enderror" placeholder="Enter Full Name" value="{{ old('borrower_name') }}" required>
                    @error('borrower_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Date Requested: <span class="text-danger">*</span></label>
                    <input type="date" name="date_requested" class="form-control @error('date_requested') is-invalid @enderror" value="{{ old('date_requested') ?? date('Y-m-d') }}" required>
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
                    <label class="form-label">Number of Student Groups: <span class="text-danger">*</span></label>
                    <input type="number" name="number_of_groups" class="form-control @error('number_of_groups') is-invalid @enderror" min="1" value="{{ old('number_of_groups') }}" id="number_of_groups" required>
                    @error('number_of_groups')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Number of student groups that will use these chemicals</small>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Time: <span class="text-danger">*</span></label>
                    <input type="time" name="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time') }}" required>
                    @error('time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-primary" onclick="nextPhase(1)">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- Phase 2: Course Information -->
        <div class="phase" id="phase2" style="display: none;">
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

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" onclick="prevPhase(2)"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary" onclick="nextPhase(2)">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- Phase 3: Non-Regulated Chemicals -->
        <div class="phase" id="phase3" style="display: none;">
            <h5 class="fw-bold mb-3">NON-REGULATED CHEMICALS</h5>

            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i> <strong>Note:</strong> Total quantity will be calculated automatically based on number of groups. Stock validation uses true available quantity (Total - Issued - Reserved).
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:25%">Chemical Name</th>
                            <th style="width:15%">Type</th>
                            <th style="width:15%">Available Stock</th>
                            <th style="width:15%">Quantity per Group</th>
                            <th style="width:15%">Total Quantity Required</th>
                            <th style="width:25%">Working Instruction</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="nonRegulatedTable">
                        <tr>
                            <td>
                                <select class="form-select chemical-select" name="nonregulated[0][name]" data-type="normal" data-row="0" onchange="updateChemicalDetails(this)">
                                    <option value="">Select Chemical...</option>
                                    @foreach ($chemicals as $chem)
                                        <option value="{{ $chem->name }}"
                                            data-solution="{{ $chem->is_solution ? 'Solution' : 'Solid' }}"
                                            data-has-concentration="{{ $chem->has_concentration ? 'Yes' : 'No' }}"
                                            data-concentration-value="{{ $chem->concentration_value ?? 0 }}"
                                            data-concentration-unit="{{ $chem->concentration_unit ?? '' }}"
                                            data-volume="{{ $chem->volume ?? 0 }}"
                                            data-volume-unit="{{ $chem->volume_unit ?? '' }}"
                                            data-quantity="{{ $chem->quantity }}"
                                            data-available="{{ $chem->available_quantity }}"
                                            data-unit="{{ $chem->unit }}">
                                            {{ $chem->name }} (Available: {{ $chem->available_quantity }} {{ $chem->unit }}, Total: {{ $chem->quantity }} {{ $chem->unit }})
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Hidden fields for chemical properties -->
                                <input type="hidden" name="nonregulated[0][is_solution]" value="0">
                                <input type="hidden" name="nonregulated[0][has_concentration]" value="0">
                                <input type="hidden" name="nonregulated[0][concentration_value]" value="0">
                                <input type="hidden" name="nonregulated[0][concentration_unit]" value="">
                                <input type="hidden" name="nonregulated[0][volume]" value="0">
                                <input type="hidden" name="nonregulated[0][volume_unit]" value="">
                            </td>
                            <td>
                                <input type="text" class="form-control chemical-type" data-row="0" readonly>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control available-stock" data-row="0" readonly>
                                    <span class="ms-2 stock-unit" data-row="0"></span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="nonregulated[0][quantity_per_group]" class="form-control quantity-per-group" data-row="0" min="0.1" step="0.1" oninput="calculateTotalQuantity(this)">
                                    <span class="input-group-text unit-display" data-row="0"></span>
                                </div>
                                <small class="text-danger stock-error" data-row="0" style="display:none;"></small>
                            </td>
                            <td>
                                <input type="text" class="form-control total-quantity" data-row="0" readonly>
                            </td>
                            <td>
                                <input type="text" name="nonregulated[0][instruction]" class="form-control" placeholder="e.g., Handle with gloves">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" data-row="0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow('nonRegulatedTable','nonregulated')">
                    <i class="bi bi-plus-circle"></i> Add Chemical
                </button>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" onclick="prevPhase(3)"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary" onclick="nextPhase(3)">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- Phase 4: PDEA-Regulated Chemicals -->
        <div class="phase" id="phase4" style="display: none;">
            <h5 class="fw-bold mb-3 text-danger">PDEA-REGULATED CHEMICALS</h5>

            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Important:</strong> These chemicals are regulated by PDEA. Please ensure all information is accurate and complete. Stock validation uses true available quantity (Total - Issued - Reserved).
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:25%">Chemical Name</th>
                            <th style="width:15%">Type</th>
                            <th style="width:15%">Available Stock</th>
                            <th style="width:15%">Quantity per Group</th>
                            <th style="width:15%">Total Quantity Required</th>
                            <th style="width:25%">Working Instruction</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="regulatedTable">
                        <tr>
                            <td>
                                <select class="form-select chemical-select" name="regulated[0][name]" data-type="pdea" data-row="0" onchange="updateChemicalDetails(this)">
                                    <option value="">Select Chemical...</option>
                                    @foreach ($pdeaChemicals as $chem)
                                        <option value="{{ $chem->name }}"
                                            data-solution="{{ $chem->is_solution ? 'Solution' : 'Solid' }}"
                                            data-has-concentration="{{ $chem->has_concentration ? 'Yes' : 'No' }}"
                                            data-concentration-value="{{ $chem->concentration_value ?? 0 }}"
                                            data-concentration-unit="{{ $chem->concentration_unit ?? '' }}"
                                            data-volume="{{ $chem->volume ?? 0 }}"
                                            data-volume-unit="{{ $chem->volume_unit ?? '' }}"
                                            data-quantity="{{ $chem->quantity }}"
                                            data-available="{{ $chem->available_quantity }}"
                                            data-unit="{{ $chem->unit }}">
                                            {{ $chem->name }} (Available: {{ $chem->available_quantity }} {{ $chem->unit }}, Total: {{ $chem->quantity }} {{ $chem->unit }})
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Hidden fields for chemical properties -->
                                <input type="hidden" name="regulated[0][is_solution]" value="0">
                                <input type="hidden" name="regulated[0][has_concentration]" value="0">
                                <input type="hidden" name="regulated[0][concentration_value]" value="0">
                                <input type="hidden" name="regulated[0][concentration_unit]" value="">
                                <input type="hidden" name="regulated[0][volume]" value="0">
                                <input type="hidden" name="regulated[0][volume_unit]" value="">
                            </td>
                            <td>
                                <input type="text" class="form-control chemical-type" data-row="0" readonly>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control available-stock" data-row="0" readonly>
                                    <span class="ms-2 stock-unit" data-row="0"></span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="regulated[0][quantity_per_group]" class="form-control quantity-per-group" data-row="0" min="0.1" step="0.1" oninput="calculateTotalQuantity(this)">
                                    <span class="input-group-text unit-display" data-row="0"></span>
                                </div>
                                <small class="text-danger stock-error" data-row="0" style="display:none;"></small>
                            </td>
                            <td>
                                <input type="text" class="form-control total-quantity" data-row="0" readonly>
                            </td>
                            <td>
                                <input type="text" name="regulated[0][instruction]" class="form-control" placeholder="e.g., Special handling required">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" data-row="0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="addRow('regulatedTable','regulated')">
                    <i class="bi bi-plus-circle"></i> Add Chemical
                </button>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" onclick="prevPhase(4)"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary" onclick="nextPhase(4)">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- Phase 5: Glassware/Apparatus/Materials -->
        <div class="phase" id="phase5" style="display: none;">
            <h5 class="fw-bold mb-3">GLASSWARE / APPARATUS / MATERIALS</h5>

            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i> <strong>Note:</strong> Total quantity will be calculated automatically based on number of groups. Stock validation uses true available quantity (Total - Issued - Reserved).
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:25%">Item Name</th>
                            <th style="width:15%">Type</th>
                            <th style="width:15%">Available Stock</th>
                            <th style="width:15%">Quantity per Group</th>
                            <th style="width:15%">Total Quantity Required</th>
                            <th style="width:25%">Working Instruction</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="glasswareTable">
                        <tr>
                            <td>
                                <select class="form-select item-select" name="glassware[0][name]" data-type="glassware" data-row="0" onchange="updateItemDetails(this)">
                                    <option value="">Select Item...</option>
                                    @foreach ($glassware as $item)
                                        <option value="{{ $item->name }}"
                                            data-type="{{ $item->type }}"
                                            data-quantity="{{ $item->quantity }}"
                                            data-available="{{ $item->available_quantity }}"
                                            data-unit="{{ $item->unit }}">
                                            {{ $item->name }} (Available: {{ $item->available_quantity }} {{ $item->unit }}, Total: {{ $item->quantity }} {{ $item->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control item-type" data-row="0" readonly>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control available-stock" data-row="0" readonly>
                                    <span class="ms-2 stock-unit" data-row="0"></span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="glassware[0][quantity_per_group]" class="form-control quantity-per-group" data-row="0" min="0.1" step="0.1" oninput="calculateTotalQuantity(this)">
                                    <span class="input-group-text unit-display" data-row="0"></span>
                                </div>
                                <small class="text-danger stock-error" data-row="0" style="display:none;"></small>
                            </td>
                            <td>
                                <input type="text" class="form-control total-quantity" data-row="0" readonly>
                            </td>
                            <td>
                                <input type="text" name="glassware[0][instruction]" class="form-control" placeholder="e.g., Handle with care">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" data-row="0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow('glasswareTable','glassware')">
                    <i class="bi bi-plus-circle"></i> Add Item
                </button>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" onclick="prevPhase(5)"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary" onclick="nextPhase(5)">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- Phase 6: Equipment/Consumables -->
        <div class="phase" id="phase6" style="display: none;">
            <h5 class="fw-bold mb-3 text-danger">EQUIPMENT / CONSUMABLES</h5>

            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Important:</strong> These items may require special handling or training. Stock validation uses true available quantity (Total - Issued - Reserved).
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:25%">Item Name</th>
                            <th style="width:15%">Type</th>
                            <th style="width:15%">Available Stock</th>
                            <th style="width:15%">Quantity per Group</th>
                            <th style="width:15%">Total Quantity Required</th>
                            <th style="width:25%">Working Instruction</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="equipmentTable">
                        <tr>
                            <td>
                                <select class="form-select item-select" name="equipment[0][name]" data-type="equipment" data-row="0" onchange="updateItemDetails(this)">
                                    <option value="">Select Item...</option>
                                    @foreach ($equipment as $item)
                                        <option value="{{ $item->name }}"
                                            data-type="{{ $item->type }}"
                                            data-quantity="{{ $item->quantity }}"
                                            data-available="{{ $item->available_quantity }}"
                                            data-unit="{{ $item->unit }}">
                                            {{ $item->name }} (Available: {{ $item->available_quantity }} {{ $item->unit }}, Total: {{ $item->quantity }} {{ $item->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control item-type" data-row="0" readonly>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control available-stock" data-row="0" readonly>
                                    <span class="ms-2 stock-unit" data-row="0"></span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="equipment[0][quantity_per_group]" class="form-control quantity-per-group" data-row="0" min="0.1" step="0.1" oninput="calculateTotalQuantity(this)">
                                    <span class="input-group-text unit-display" data-row="0"></span>
                                </div>
                                <small class="text-danger stock-error" data-row="0" style="display:none;"></small>
                            </td>
                            <td>
                                <input type="text" class="form-control total-quantity" data-row="0" readonly>
                            </td>
                            <td>
                                <input type="text" name="equipment[0][instruction]" class="form-control" placeholder="e.g., Special handling required">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" data-row="0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="addRow('equipmentTable','equipment')">
                    <i class="bi bi-plus-circle"></i> Add Item
                </button>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" onclick="prevPhase(6)"><i class="bi bi-arrow-left"></i> Previous</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Submit Request</button>
            </div>
        </div>
    </form>
</div>

<!-- Student Reservation Modal -->
<div class="modal fade" id="studentReservationModal" tabindex="-1" aria-labelledby="studentReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentReservationModalLabel">Student Group Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please provide the following reference number to your students:</p>
                <div class="alert alert-success">
                    <h4 class="text-center mb-0" id="facultyReferenceNumber"></h4>
                </div>
                <p class="mt-3">Students should use this reference number when making their reservations.</p>
                <p>Number of groups allowed: <strong id="allowedGroups">0</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('lid.student-reservations.create') }}" class="btn btn-primary">Go to Student Reservation</a>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    let rowCount = 1;

    function nextPhase(currentPhase) {
        if (currentPhase === 1) {
            if (!validatePhase1()) return false;
        } else if (currentPhase === 6) {
            if (!validateChemicals()) return false;
        }
        
        document.getElementById('phase' + currentPhase).style.display = 'none';
        document.getElementById('phase' + (currentPhase + 1)).style.display = 'block';
        updateProgressBar(currentPhase + 1);
    }

    function prevPhase(currentPhase) {
        document.getElementById('phase' + currentPhase).style.display = 'none';
        document.getElementById('phase' + (currentPhase - 1)).style.display = 'block';
        updateProgressBar(currentPhase - 1);
    }

    function updateProgressBar(phase) {
        const progressBar = document.querySelector('.progress-bar');
        const percentage = (phase / 6) * 100;
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
        progressBar.textContent = 'Phase ' + phase + ' of 6';
    }

    function validatePhase1() {
        const requiredFields = [
            'borrower_name', 'date_requested', 'term', 'room_no',
            'start_date', 'end_date', 'number_of_groups', 'time'
        ];
        
        for (const field of requiredFields) {
            const element = document.querySelector(`[name="${field}"]`);
            if (!element.value.trim()) {
                alert(`Please fill in ${field.replace('_', ' ')}.`);
                element.focus();
                return false;
            }
        }
        
        const purposeCheckboxes = document.querySelectorAll('input[name="purpose[]"]:checked');
        if (purposeCheckboxes.length === 0) {
            alert('Please select at least one purpose.');
            return false;
        }
        
        return true;
    }

    function validateChemicals() {
        const hasChemicals = document.querySelectorAll('.chemical-select').length > 0;
        const hasItems = document.querySelectorAll('.item-select').length > 0;
        
        if (!hasChemicals && !hasItems) {
            alert('Please select at least one chemical or item in Phases 3, 4, 5, or 6.');
            return false;
        }
        
        // Check stock availability for chemicals
        const allChemicalRows = document.querySelectorAll('tr:has(.chemical-select)');
        for (const row of allChemicalRows) {
            const stockError = row.querySelector('.stock-error');
            if (stockError && stockError.style.display !== 'none') {
                alert('Please fix all stock availability issues before proceeding.');
                return false;
            }
        }
        
        // Check stock availability for items
        const allItemRows = document.querySelectorAll('tr:has(.item-select)');
        for (const row of allItemRows) {
            const stockError = row.querySelector('.stock-error');
            if (stockError && stockError.style.display !== 'none') {
                alert('Please fix all stock availability issues before proceeding.');
                return false;
            }
        }
        
        return true;
    }

    function updateChemicalDetails(selectElement) {
        const row = selectElement.closest('tr');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption.value) {
            // Update type field
            const typeField = row.querySelector('.chemical-type');
            typeField.value = selectedOption.dataset.solution;
            
            // Update hidden fields with chemical properties
            const isSolutionField = row.querySelector('input[name$="[is_solution]"]');
            isSolutionField.value = selectedOption.dataset.solution === 'Solution' ? 1 : 0;
            
            const hasConcentrationField = row.querySelector('input[name$="[has_concentration]"]');
            hasConcentrationField.value = selectedOption.dataset.hasConcentration === 'Yes' ? 1 : 0;
            
            const concentrationValueField = row.querySelector('input[name$="[concentration_value]"]');
            concentrationValueField.value = parseFloat(selectedOption.dataset.concentrationValue) || 0;
            
            const concentrationUnitField = row.querySelector('input[name$="[concentration_unit]"]');
            concentrationUnitField.value = selectedOption.dataset.concentrationUnit || '';
            
            const volumeField = row.querySelector('input[name$="[volume]"]');
            volumeField.value = parseFloat(selectedOption.dataset.volume) || 0;
            
            const volumeUnitField = row.querySelector('input[name$="[volume_unit]"]');
            volumeUnitField.value = selectedOption.dataset.volumeUnit || '';
            
            // Update available stock (using true available quantity)
            const stockField = row.querySelector('.available-stock');
            const stockUnit = row.querySelector('.stock-unit');
            stockField.value = selectedOption.dataset.available; // Using available quantity, not total quantity
            stockUnit.textContent = selectedOption.dataset.unit;
            
            // Update unit display
            const unitDisplay = row.querySelector('.unit-display');
            unitDisplay.textContent = selectedOption.dataset.unit;
            
            // Clear previous values
            row.querySelector('.quantity-per-group').value = '';
            row.querySelector('.total-quantity').value = '';
            row.querySelector('.stock-error').style.display = 'none';
        } else {
            // Reset all fields if no chemical is selected
            const isSolutionField = row.querySelector('input[name$="[is_solution]"]');
            isSolutionField.value = 0;
            
            const hasConcentrationField = row.querySelector('input[name$="[has_concentration]"]');
            hasConcentrationField.value = 0;
            
            const concentrationValueField = row.querySelector('input[name$="[concentration_value]"]');
            concentrationValueField.value = 0;
            
            const concentrationUnitField = row.querySelector('input[name$="[concentration_unit]"]');
            concentrationUnitField.value = '';
            
            const volumeField = row.querySelector('input[name$="[volume]"]');
            volumeField.value = 0;
            
            const volumeUnitField = row.querySelector('input[name$="[volume_unit]"]');
            volumeUnitField.value = '';
            
            // Reset other fields
            row.querySelector('.chemical-type').value = '';
            row.querySelector('.available-stock').value = '';
            row.querySelector('.stock-unit').textContent = '';
            row.querySelector('.unit-display').textContent = '';
            row.querySelector('.quantity-per-group').value = '';
            row.querySelector('.total-quantity').value = '';
            row.querySelector('.stock-error').style.display = 'none';
        }
    }

    function updateItemDetails(selectElement) {
        const row = selectElement.closest('tr');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption.value) {
            // Update type field
            const typeField = row.querySelector('.item-type');
            typeField.value = selectedOption.dataset.type;
            
            // Update available stock (using true available quantity)
            const stockField = row.querySelector('.available-stock');
            const stockUnit = row.querySelector('.stock-unit');
            stockField.value = selectedOption.dataset.available; // Using available quantity, not total quantity
            stockUnit.textContent = selectedOption.dataset.unit;
            
            // Update unit display
            const unitDisplay = row.querySelector('.unit-display');
            unitDisplay.textContent = selectedOption.dataset.unit;
            
            // Clear previous values
            row.querySelector('.quantity-per-group').value = '';
            row.querySelector('.total-quantity').value = '';
            row.querySelector('.stock-error').style.display = 'none';
        } else {
            // Reset all fields if no item is selected
            row.querySelector('.item-type').value = '';
            row.querySelector('.available-stock').value = '';
            row.querySelector('.stock-unit').textContent = '';
            row.querySelector('.unit-display').textContent = '';
            row.querySelector('.quantity-per-group').value = '';
            row.querySelector('.total-quantity').value = '';
            row.querySelector('.stock-error').style.display = 'none';
        }
    }

    function calculateTotalQuantity(inputElement) {
        const row = inputElement.closest('tr');
        const quantityPerGroup = parseFloat(inputElement.value) || 0;
        const numberOfGroups = parseInt(document.getElementById('number_of_groups').value) || 1;
        const availableStock = parseFloat(row.querySelector('.available-stock').value) || 0;
        const unit = row.querySelector('.stock-unit').textContent;
        
        const totalRequired = quantityPerGroup * numberOfGroups;
        const totalQuantityField = row.querySelector('.total-quantity');
        
        if (quantityPerGroup > 0) {
            totalQuantityField.value = `${totalRequired.toFixed(2)} ${unit}`;
            
            // Check stock availability - compare with true available stock
            const stockError = row.querySelector('.stock-error');
            if (totalRequired > availableStock) {
                stockError.textContent = `Insufficient stock! Available: ${availableStock} ${unit}, Required: ${totalRequired} ${unit}`;
                stockError.style.display = 'block';
                inputElement.classList.add('is-invalid');
            } else {
                stockError.style.display = 'none';
                inputElement.classList.remove('is-invalid');
            }
        } else {
            totalQuantityField.value = '';
            row.querySelector('.stock-error').style.display = 'none';
            inputElement.classList.remove('is-invalid');
        }
    }

    function addRow(tableId, groupName) {
        const table = document.getElementById(tableId);
        const index = rowCount++;

        let options = '';
        let items = [];
        
        if (groupName === 'nonregulated') {
            items = @json($chemicals);
            items.forEach(chem => {
                options += `<option value="${chem.name}"
                    data-solution="${chem.is_solution ? 'Solution' : 'Solid'}"
                    data-has-concentration="${chem.has_concentration ? 'Yes' : 'No'}"
                    data-concentration-value="${chem.concentration_value || 0}"
                    data-concentration-unit="${chem.concentration_unit || ''}"
                    data-volume="${chem.volume || 0}"
                    data-volume-unit="${chem.volume_unit || ''}"
                    data-quantity="${chem.quantity}"
                    data-available="${chem.available_quantity}"
                    data-unit="${chem.unit}">
                    ${chem.name} (Available: ${chem.available_quantity} ${chem.unit}, Total: ${chem.quantity} ${chem.unit})
                </option>`;
            });
        } else if (groupName === 'regulated') {
            items = @json($pdeaChemicals);
            items.forEach(chem => {
                options += `<option value="${chem.name}"
                    data-solution="${chem.is_solution ? 'Solution' : 'Solid'}"
                    data-has-concentration="${chem.has_concentration ? 'Yes' : 'No'}"
                    data-concentration-value="${chem.concentration_value || 0}"
                    data-concentration-unit="${chem.concentration_unit || ''}"
                    data-volume="${chem.volume || 0}"
                    data-volume-unit="${chem.volume_unit || ''}"
                    data-quantity="${chem.quantity}"
                    data-available="${chem.available_quantity}"
                    data-unit="${chem.unit}">
                    ${chem.name} (Available: ${chem.available_quantity} ${chem.unit}, Total: ${chem.quantity} ${chem.unit})
                </option>`;
            });
        } else if (groupName === 'glassware') {
            items = @json($glassware);
            items.forEach(item => {
                options += `<option value="${item.name}"
                    data-type="${item.type}"
                    data-quantity="${item.quantity}"
                    data-available="${item.available_quantity}"
                    data-unit="${item.unit}">
                    ${item.name} (Available: ${item.available_quantity} ${item.unit}, Total: ${item.quantity} ${item.unit})
                </option>`;
            });
        } else if (groupName === 'equipment') {
            items = @json($equipment);
            items.forEach(item => {
                options += `<option value="${item.name}"
                    data-type="${item.type}"
                    data-quantity="${item.quantity}"
                    data-available="${item.available_quantity}"
                    data-unit="${item.unit}">
                    ${item.name} (Available: ${item.available_quantity} ${item.unit}, Total: ${item.quantity} ${item.unit})
                </option>`;
            });
        }

        const isChemical = groupName === 'nonregulated' || groupName === 'regulated';
        const selectClass = isChemical ? 'chemical-select' : 'item-select';
        const typeClass = isChemical ? 'chemical-type' : 'item-type';
        const updateFunction = isChemical ? 'updateChemicalDetails' : 'updateItemDetails';
        
        let hiddenFields = '';
        if (isChemical) {
            hiddenFields = `
                <!-- Hidden fields for chemical properties -->
                <input type="hidden" name="${groupName}[${index}][is_solution]" value="0">
                <input type="hidden" name="${groupName}[${index}][has_concentration]" value="0">
                <input type="hidden" name="${groupName}[${index}][concentration_value]" value="0">
                <input type="hidden" name="${groupName}[${index}][concentration_unit]" value="">
                <input type="hidden" name="${groupName}[${index}][volume]" value="0">
                <input type="hidden" name="${groupName}[${index}][volume_unit]" value="">
            `;
        }

        const row = `
            <tr>
                <td>
                    <select class="form-select ${selectClass}" name="${groupName}[${index}][name]" data-type="${groupName}" data-row="${index}" onchange="${updateFunction}(this)">
                        <option value="">Select ${isChemical ? 'Chemical' : 'Item'}...</option>
                        ${options}
                    </select>
                    ${hiddenFields}
                </td>
                <td>
                    <input type="text" class="form-control ${typeClass}" data-row="${index}" readonly>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <input type="text" class="form-control available-stock" data-row="${index}" readonly>
                        <span class="ms-2 stock-unit" data-row="${index}"></span>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="${groupName}[${index}][quantity_per_group]" class="form-control quantity-per-group" data-row="${index}" min="0.1" step="0.1" oninput="calculateTotalQuantity(this)">
                        <span class="input-group-text unit-display" data-row="${index}"></span>
                    </div>
                    <small class="text-danger stock-error" data-row="${index}" style="display:none;"></small>
                </td>
                <td>
                    <input type="text" class="form-control total-quantity" data-row="${index}" readonly>
                </td>
                <td>
                    <input type="text" name="${groupName}[${index}][instruction]" class="form-control" placeholder="Enter instructions">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row" data-row="${index}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        table.insertAdjacentHTML('beforeend', row);
        $('.chemical-select, .item-select').select2();
    }

    // Handle form submission
    document.getElementById('chemicalRequestForm').addEventListener('submit', function(e) {
        if (!validateChemicals()) {
            e.preventDefault();
            return;
        }
        
        // Show success modal with reference number
        const facultyName = document.querySelector('input[name="borrower_name"]').value;
        const groups = document.getElementById('number_of_groups').value;
        
        // In a real app, you would get this from the server response
        // For now, we'll show a placeholder
        document.getElementById('facultyReferenceNumber').textContent = 'Pending Generation...';
        document.getElementById('allowedGroups').textContent = groups;
        
        const modal = new bootstrap.Modal(document.getElementById('studentReservationModal'));
        modal.show();
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        $('.chemical-select, .item-select').select2();
        
        // Handle remove row
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
            }
        });
        
        // Recalculate totals when number of groups changes
        document.getElementById('number_of_groups').addEventListener('input', function() {
            document.querySelectorAll('.quantity-per-group').forEach(input => {
                if (input.value) calculateTotalQuantity(input);
            });
        });
    });
</script>

<style>
    /* Add tooltip styles for better stock information */
    .available-stock {
        cursor: help;
        position: relative;
    }
    
    .available-stock:hover::after {
        content: "Available = Total - Issued - Reserved";
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-bottom: 5px;
    }
    
    .stock-error {
        display: block;
        margin-top: 5px;
        font-size: 0.85em;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    /* Improve select2 dropdown styling */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    /* Responsive table adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.85em;
        }
        
        .input-group-text {
            font-size: 0.85em;
        }
        
        .form-control, .form-select {
            font-size: 0.9em;
        }
    }
</style>

@endsection