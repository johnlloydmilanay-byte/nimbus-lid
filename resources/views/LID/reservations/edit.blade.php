@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">
            @if($reservation->borrower_type === 'Faculty Member')
                Edit Faculty Reservation
            @else
                Edit Student Reservation
            @endif
        </h2>
        <a href="{{ route('lid.reservations.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
        </a>
    </div><br>

    <!-- Alert container for dynamic messages -->
    <div id="alertContainer">
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
    </div>

    <!-- Form for updating reservation -->
    <form action="{{ route('lid.reservations.update', $reservation->id) }}" method="POST" id="reservationEditForm">
        @csrf
        @method('PUT')
        
        <!-- Reservation Status Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Reservation Status</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Current Status:</label>
                        <div>
                            @php
                                $status = $reservation->status ?? 'Draft';
                                $statusClass = $status == 'Approved' ? 'bg-success' : ($status == 'Pending' ? 'bg-warning' : ($status == 'Rejected' ? 'bg-danger' : 'bg-secondary'));
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Update Status:</label>
                        <select name="status" class="form-select">
                            <option value="Pending" {{ $reservation->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ $reservation->status === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ $reservation->status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Cancelled" {{ $reservation->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Items Released to Borrower:</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="items_released" value="0">
                            <input class="form-check-input" type="checkbox" name="items_released" id="itemsReleased" value="1"
                                   @if($reservation->items_released) checked @endif>
                            <label class="form-check-label" for="itemsReleased">
                                Mark as released to borrower
                            </label>
                        </div>
                        @if($reservation->items_released)
                            <small class="text-muted">Released on: {{ $reservation->items_released_at ? \Carbon\Carbon::parse($reservation->items_released_at)->format('F d, Y h:i A') : 'N/A' }}</small>
                        @endif
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Items Returned by Borrower:</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="items_returned" value="0">
                            <input class="form-check-input" type="checkbox" name="items_returned" id="itemsReturned" value="1"
                                   @if($reservation->items_returned) checked @endif>
                            <label class="form-check-label" for="itemsReturned">
                                Mark as returned by borrower
                            </label>
                        </div>
                        @if($reservation->items_returned)
                            <small class="text-muted">Returned on: {{ $reservation->items_returned_at ? \Carbon\Carbon::parse($reservation->items_returned_at)->format('F d, Y h:i A') : 'N/A' }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrower Information Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Borrower Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Borrower Name: <span class="text-danger">*</span></label>
                        <input type="text" name="borrower_name" class="form-control @error('borrower_name') is-invalid @enderror" 
                               value="{{ old('borrower_name', $reservation->borrower_name) }}" 
                               required>
                        @error('borrower_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Borrower Type:</label>
                        <input type="text" class="form-control" value="{{ $reservation->borrower_type }}" readonly>
                        <input type="hidden" name="borrower_type" value="{{ $reservation->borrower_type }}">
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Reference Number:</label>
                        <input type="text" class="form-control" value="{{ $reservation->reference_number }}" readonly>
                    </div>
                    
                    @if($reservation->borrower_type === 'Student')
                        <div class="col-12 col-md-6">
                            <label class="form-label">Student ID:</label>
                            <input type="text" name="student_id" class="form-control @error('student_id') is-invalid @enderror" 
                                   value="{{ old('student_id', $reservation->student_id) }}">
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Group Number:</label>
                            <input type="number" name="group_number" class="form-control @error('group_number') is-invalid @enderror" 
                                   value="{{ old('group_number', $reservation->group_number) }}">
                            @error('group_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $reservation->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Contact Number:</label>
                            <input type="tel" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" 
                                   value="{{ old('contact_number', $reservation->contact_number) }}">
                            @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if($reservation->faculty_reference_id)
                            <div class="col-12">
                                <label class="form-label">Faculty Reference:</label>
                                <input type="text" class="form-control" value="{{ $reservation->facultyReservation->reference_number }}" readonly>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Reservation Details Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Reservation Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Date Requested: <span class="text-danger">*</span></label>
                        <input type="date" name="date_requested" class="form-control @error('date_requested') is-invalid @enderror" 
                               value="{{ old('date_requested', $reservation->date_requested ? \Carbon\Carbon::parse($reservation->date_requested)->format('Y-m-d') : '') }}" 
                               required>
                        @error('date_requested')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Term: <span class="text-danger">*</span></label>
                        <input type="text" name="term" class="form-control @error('term') is-invalid @enderror" 
                               value="{{ old('term', $reservation->term) }}" 
                               required>
                        @error('term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Purpose: <span class="text-danger">*</span></label>
                        <select name="purpose[]" class="form-select @error('purpose') is-invalid @enderror" multiple required>
                            <option value="Laboratory Activity" {{ in_array('Laboratory Activity', explode(', ', $reservation->purpose)) ? 'selected' : '' }}>Laboratory Activity</option>
                            <option value="Student Thesis" {{ in_array('Student Thesis', explode(', ', $reservation->purpose)) ? 'selected' : '' }}>Student Thesis</option>
                            <option value="Faculty Research" {{ in_array('Faculty Research', explode(', ', $reservation->purpose)) ? 'selected' : '' }}>Faculty Research</option>
                        </select>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Room No.: <span class="text-danger">*</span></label>
                        <input type="text" name="room_no" class="form-control @error('room_no') is-invalid @enderror" 
                               value="{{ old('room_no', $reservation->room_no) }}" 
                               required>
                        @error('room_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Start Date: <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                               value="{{ old('start_date', $reservation->start_date ? \Carbon\Carbon::parse($reservation->start_date)->format('Y-m-d') : '') }}" 
                               required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">End Date: <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                               value="{{ old('end_date', $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date)->format('Y-m-d') : '') }}" 
                               required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Time: <span class="text-danger">*</span></label>
                        <input type="text" name="time" class="form-control @error('time') is-invalid @enderror" 
                               value="{{ old('time', $reservation->time) }}" 
                               required>
                        @error('time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Number of Groups: <span class="text-danger">*</span></label>
                        <input type="number" name="number_of_groups" class="form-control @error('number_of_groups') is-invalid @enderror" 
                               value="{{ old('number_of_groups', $reservation->number_of_groups) }}" 
                               min="1" max="20" required>
                        @error('number_of_groups')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Course Information (for Faculty Reservations) -->
                    @if($reservation->borrower_type === 'Faculty Member')
                        <div class="col-12 col-md-6">
                            <label class="form-label">Program:</label>
                            <input type="text" name="program" class="form-control @error('program') is-invalid @enderror" 
                                   value="{{ old('program', $reservation->program) }}">
                            @error('program')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Year & Section:</label>
                            <input type="text" name="year_section" class="form-control @error('year_section') is-invalid @enderror" 
                                   value="{{ old('year_section', $reservation->year_section) }}">
                            @error('year_section')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Subject Code:</label>
                            <input type="text" name="subject_code" class="form-control @error('subject_code') is-invalid @enderror" 
                                   value="{{ old('subject_code', $reservation->subject_code) }}">
                            @error('subject_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Subject Description:</label>
                            <input type="text" name="subject_description" class="form-control @error('subject_description') is-invalid @enderror" 
                                   value="{{ old('subject_description', $reservation->subject_description) }}">
                            @error('subject_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Activity Title:</label>
                            <input type="text" name="activity_title" class="form-control @error('activity_title') is-invalid @enderror" 
                                   value="{{ old('activity_title', $reservation->activity_title) }}">
                            @error('activity_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label">Activity No.:</label>
                            <input type="text" name="activity_no" class="form-control @error('activity_no') is-invalid @enderror" 
                                   value="{{ old('activity_no', $reservation->activity_no) }}">
                            @error('activity_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Requested Items</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-bs-toggle="pill" data-bs-target="#chemicals" role="tab">
                        <i class="bi bi-droplet-fill me-1"></i>Chemicals
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="pill" data-bs-target="#glassware" role="tab">
                        <i class="bi bi-beaker2 me-1"></i>Glassware
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="pill" data-bs-target="#equipment" role="tab">
                        <i class="bi bi-tools me-1"></i>Equipment
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Tab Content -->
                <div class="tab-content" id="reservationTabContent">
                    <!-- Chemicals Tab -->
                    <div class="tab-pane fade show active" id="chemicals" role="tabpanel">
                        <!-- Non-Regulated Chemicals -->
                        <h6 class="text-primary mb-2">Non-Regulated Chemicals</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Available Chemicals:</label>
                                <select id="availableChemicals" class="form-select">
                                    <option value="">Select a chemical...</option>
                                    @foreach($chemicals as $chemical)
                                        <option value="{{ $chemical->name }}" data-unit="{{ $chemical->unit }}" 
                                                data-is-solution="{{ $chemical->is_solution }}" 
                                                data-has-concentration="{{ $chemical->has_concentration }}"
                                                data-concentration-value="{{ $chemical->concentration_value }}"
                                                data-concentration-unit="{{ $chemical->concentration_unit }}"
                                                data-volume="{{ $chemical->volume }}"
                                                data-volume-unit="{{ $chemical->volume_unit }}"
                                                data-available="{{ $chemical->available_quantity }}">
                                            {{ $chemical->name }} ({{ $chemical->available_quantity }} {{ $chemical->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary" id="addSelectedChemical">
                                        <i class="bi bi-plus-circle"></i> Add Selected Chemical
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered" id="nonregulatedTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Chemical Name</th>
                                        <th>Type</th>
                                        <th>Available Stock</th>
                                        <th>Quantity per Group</th>
                                        <th>Total Quantity Required</th>
                                        <th>Working Instruction</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($reservation->chemicalRequests && $reservation->chemicalRequests->count() > 0)
                                        @foreach($reservation->chemicalRequests as $index => $chem)
                                        <tr data-index="{{ $index }}">
                                            <input type="hidden" name="nonregulated[{{ $index }}][id]" value="{{ $chem->id }}">
                                            <td>
                                                <input type="text" name="nonregulated[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $chem->name }}">
                                            </td>
                                            <td class="text-center">
                                                <select name="nonregulated[{{ $index }}][is_solution]" class="form-select form-select-sm">
                                                    <option value="0" {{ !$chem->is_solution ? 'selected' : '' }}>Solid</option>
                                                    <option value="1" {{ $chem->is_solution ? 'selected' : '' }}>Solution</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info chemical-stock" data-name="{{ $chem->name }}">
                                                    {{ $chemicalStocks[$chem->name]['available_quantity'] ?? 0 }} {{ $chemicalStocks[$chem->name]['unit'] ?? $chem->unit }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="nonregulated[{{ $index }}][quantity_per_group]" class="form-control quantity-per-group" value="{{ $chem->quantity_per_group }}" step="0.1" min="0.1">
                                                    <input type="text" name="nonregulated[{{ $index }}][unit]" class="form-control" value="{{ $chem->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="nonregulated[{{ $index }}][total_quantity]" class="form-control total-quantity" value="{{ $chem->total_quantity }}" step="0.1" min="0.1" readonly>
                                                    <input type="text" class="form-control" value="{{ $chem->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="nonregulated[{{ $index }}][instruction]" class="form-control form-control-sm" value="{{ $chem->instruction }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No non-regulated chemicals requested.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- PDEA-Regulated Chemicals -->
                        <h6 class="text-danger mb-2 mt-4">PDEA-Regulated Chemicals</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Available PDEA Chemicals:</label>
                                <select id="availablePdeaChemicals" class="form-select">
                                    <option value="">Select a PDEA chemical...</option>
                                    @foreach($pdeaChemicals as $chemical)
                                        <option value="{{ $chemical->name }}" data-unit="{{ $chemical->unit }}" 
                                                data-is-solution="{{ $chemical->is_solution }}" 
                                                data-has-concentration="{{ $chemical->has_concentration }}"
                                                data-concentration-value="{{ $chemical->concentration_value }}"
                                                data-concentration-unit="{{ $chemical->concentration_unit }}"
                                                data-volume="{{ $chemical->volume }}"
                                                data-volume-unit="{{ $chemical->volume_unit }}"
                                                data-available="{{ $chemical->available_quantity }}">
                                            {{ $chemical->name }} ({{ $chemical->available_quantity }} {{ $chemical->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" id="addSelectedPdeaChemical">
                                        <i class="bi bi-plus-circle"></i> Add Selected PDEA Chemical
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="regulatedTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Chemical Name</th>
                                        <th>Type</th>
                                        <th>Available Stock</th>
                                        <th>Quantity per Group</th>
                                        <th>Total Quantity Required</th>
                                        <th>Working Instruction</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($reservation->pdeaRequests && $reservation->pdeaRequests->count() > 0)
                                        @foreach($reservation->pdeaRequests as $index => $chem)
                                        <tr data-index="{{ $index }}">
                                            <input type="hidden" name="regulated[{{ $index }}][id]" value="{{ $chem->id }}">
                                            <td>
                                                <input type="text" name="regulated[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $chem->name }}">
                                            </td>
                                            <td class="text-center">
                                                <select name="regulated[{{ $index }}][is_solution]" class="form-select form-select-sm">
                                                    <option value="0" {{ !$chem->is_solution ? 'selected' : '' }}>Solid</option>
                                                    <option value="1" {{ $chem->is_solution ? 'selected' : '' }}>Solution</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger chemical-stock" data-name="{{ $chem->name }}">
                                                    {{ $pdeaChemicalStocks[$chem->name]['available_quantity'] ?? 0 }} {{ $pdeaChemicalStocks[$chem->name]['unit'] ?? $chem->unit }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="regulated[{{ $index }}][quantity_per_group]" class="form-control pdea-quantity-per-group" value="{{ $chem->quantity_per_group }}" step="0.1" min="0.1">
                                                    <input type="text" name="regulated[{{ $index }}][unit]" class="form-control" value="{{ $chem->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="regulated[{{ $index }}][total_quantity]" class="form-control pdea-total-quantity" value="{{ $chem->total_quantity }}" step="0.1" min="0.1" readonly>
                                                    <input type="text" class="form-control" value="{{ $chem->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="regulated[{{ $index }}][instruction]" class="form-control form-control-sm" value="{{ $chem->instruction }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-pdea-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No PDEA-regulated chemicals requested.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Glassware Tab -->
                    <div class="tab-pane fade" id="glassware" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Available Glassware:</label>
                                <select id="availableGlassware" class="form-select">
                                    <option value="">Select glassware...</option>
                                    @foreach($glassware as $item)
                                        <option value="{{ $item->name }}" data-unit="{{ $item->unit }}" data-type="{{ $item->type }}" data-available="{{ $item->available_quantity }}">
                                            {{ $item->name }} ({{ $item->available_quantity }} {{ $item->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-info" id="addSelectedGlassware">
                                        <i class="bi bi-plus-circle"></i> Add Selected Glassware
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="glasswareTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Available Stock</th>
                                        <th>Quantity per Group</th>
                                        <th>Total Quantity Required</th>
                                        <th>Working Instruction</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($reservation->glasswareRequests && $reservation->glasswareRequests->count() > 0)
                                        @foreach($reservation->glasswareRequests as $index => $item)
                                        <tr data-index="{{ $index }}">
                                            <input type="hidden" name="glassware[{{ $index }}][id]" value="{{ $item->id }}">
                                            <td>
                                                <input type="text" name="glassware[{{ $index }}][name]" class="form-control form-control-sm item-name" value="{{ $item->name }}">
                                            </td>
                                            <td>
                                                <input type="text" name="glassware[{{ $index }}][type]" class="form-control form-control-sm" value="{{ $item->type }}">
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info item-stock" data-name="{{ $item->name }}">
                                                    {{ $glasswareStocks[$item->name]['available_quantity'] ?? 0 }} {{ $glasswareStocks[$item->name]['unit'] ?? $item->unit }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="glassware[{{ $index }}][quantity_per_group]" class="form-control glassware-quantity-per-group" value="{{ $item->quantity_per_group }}" step="0.1" min="0.1">
                                                    <input type="text" name="glassware[{{ $index }}][unit]" class="form-control" value="{{ $item->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="glassware[{{ $index }}][total_quantity]" class="form-control glassware-total-quantity" value="{{ $item->total_quantity }}" step="0.1" min="0.1" readonly>
                                                    <input type="text" class="form-control" value="{{ $item->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="glassware[{{ $index }}][instruction]" class="form-control form-control-sm" value="{{ $item->instruction }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-glassware-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No glassware requested.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Equipment Tab -->
                    <div class="tab-pane fade" id="equipment" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Available Equipment:</label>
                                <select id="availableEquipment" class="form-select">
                                    <option value="">Select equipment...</option>
                                    @foreach($equipment as $item)
                                        <option value="{{ $item->name }}" data-unit="{{ $item->unit }}" data-type="{{ $item->type }}" data-available="{{ $item->available_quantity }}">
                                            {{ $item->name }} ({{ $item->available_quantity }} {{ $item->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-warning" id="addSelectedEquipment">
                                        <i class="bi bi-plus-circle"></i> Add Selected Equipment
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="equipmentTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Available Stock</th>
                                        <th>Quantity per Group</th>
                                        <th>Total Quantity Required</th>
                                        <th>Working Instruction</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($reservation->equipmentRequests && $reservation->equipmentRequests->count() > 0)
                                        @foreach($reservation->equipmentRequests as $index => $item)
                                        <tr data-index="{{ $index }}">
                                            <input type="hidden" name="equipment[{{ $index }}][id]" value="{{ $item->id }}">
                                            <td>
                                                <input type="text" name="equipment[{{ $index }}][name]" class="form-control form-control-sm item-name" value="{{ $item->name }}">
                                            </td>
                                            <td>
                                                <input type="text" name="equipment[{{ $index }}][type]" class="form-control form-control-sm" value="{{ $item->type }}">
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning item-stock" data-name="{{ $item->name }}">
                                                    {{ $equipmentStocks[$item->name]['available_quantity'] ?? 0 }} {{ $equipmentStocks[$item->name]['unit'] ?? $item->unit }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="equipment[{{ $index }}][quantity_per_group]" class="form-control equipment-quantity-per-group" value="{{ $item->quantity_per_group }}" step="0.1" min="0.1">
                                                    <input type="text" name="equipment[{{ $index }}][unit]" class="form-control" value="{{ $item->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="equipment[{{ $index }}][total_quantity]" class="form-control equipment-total-quantity" value="{{ $item->total_quantity }}" step="0.1" min="0.1" readonly>
                                                    <input type="text" class="form-control" value="{{ $item->unit }}" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="equipment[{{ $index }}][instruction]" class="form-control form-control-sm" value="{{ $item->instruction }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-equipment-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No equipment requested.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('lid.reservations.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Update Reservation
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the number of groups input
    const numberOfGroupsInput = document.querySelector('input[name="number_of_groups"]');
    
    // Get the available item selects
    const availableChemicals = document.getElementById('availableChemicals');
    const availablePdeaChemicals = document.getElementById('availablePdeaChemicals');
    const availableGlassware = document.getElementById('availableGlassware');
    const availableEquipment = document.getElementById('availableEquipment');
    
    // Handle tab switching
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('[data-bs-toggle="pill"]').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show selected tab pane
            const target = this.getAttribute('data-bs-target');
            document.querySelector(target).classList.add('show', 'active');
        });
    });
    
    // Function to calculate total quantity based on quantity per group and number of groups
    function calculateTotalQuantity() {
        const numberOfGroups = parseInt(numberOfGroupsInput.value) || 0;
        
        // Calculate for non-regulated chemicals
        document.querySelectorAll('.quantity-per-group').forEach(input => {
            const quantityPerGroup = parseFloat(input.value) || 0;
            const totalQuantity = quantityPerGroup * numberOfGroups;
            const row = input.closest('tr');
            const totalInput = row.querySelector('.total-quantity');
            if (totalInput) {
                totalInput.value = totalQuantity.toFixed(2);
            }
        });
        
        // Calculate for PDEA chemicals
        document.querySelectorAll('.pdea-quantity-per-group').forEach(input => {
            const quantityPerGroup = parseFloat(input.value) || 0;
            const totalQuantity = quantityPerGroup * numberOfGroups;
            const row = input.closest('tr');
            const totalInput = row.querySelector('.pdea-total-quantity');
            if (totalInput) {
                totalInput.value = totalQuantity.toFixed(2);
            }
        });
        
        // Calculate for glassware
        document.querySelectorAll('.glassware-quantity-per-group').forEach(input => {
            const quantityPerGroup = parseFloat(input.value) || 0;
            const totalQuantity = quantityPerGroup * numberOfGroups;
            const row = input.closest('tr');
            const totalInput = row.querySelector('.glassware-total-quantity');
            if (totalInput) {
                totalInput.value = totalQuantity.toFixed(2);
            }
        });
        
        // Calculate for equipment
        document.querySelectorAll('.equipment-quantity-per-group').forEach(input => {
            const quantityPerGroup = parseFloat(input.value) || 0;
            const totalQuantity = quantityPerGroup * numberOfGroups;
            const row = input.closest('tr');
            const totalInput = row.querySelector('.equipment-total-quantity');
            if (totalInput) {
                totalInput.value = totalQuantity.toFixed(2);
            }
        });
    }
    
    // Add event listener to number of groups input
    if (numberOfGroupsInput) {
        numberOfGroupsInput.addEventListener('input', calculateTotalQuantity);
    }
    
    // Function to add a selected chemical to table
    function addSelectedChemical(tableId, selectId, rowClass) {
        const select = document.getElementById(selectId);
        const table = document.querySelector(`#${tableId} tbody`);
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption.value) {
            alert('Please select an item first');
            return;
        }
        
        // Check if table has a "no items" row
        const noItemsRow = table.querySelector('tr td[colspan]');
        if (noItemsRow) {
            noItemsRow.closest('tr').remove();
        }
        
        // Get current row count
        const rowCount = table.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-index', rowCount);
        newRow.classList.add(rowClass);
        
        const chemicalName = selectedOption.value;
        const unit = selectedOption.getAttribute('data-unit');
        const isSolution = selectedOption.getAttribute('data-is-solution');
        const hasConcentration = selectedOption.getAttribute('data-has-concentration');
        const concentrationValue = selectedOption.getAttribute('data-concentration-value');
        const concentrationUnit = selectedOption.getAttribute('data-concentration-unit');
        const availableStock = selectedOption.getAttribute('data-available');
        
        newRow.innerHTML = `
            <td>
                <input type="text" name="${tableId === 'nonregulatedTable' ? 'nonregulated' : 'regulated'}[${rowCount}][name]" class="form-control form-control-sm" value="${chemicalName}">
            </td>
            <td class="text-center">
                <select name="${tableId === 'nonregulatedTable' ? 'nonregulated' : 'regulated'}[${rowCount}][is_solution]" class="form-select form-select-sm">
                    <option value="0" ${isSolution === '0' ? 'selected' : ''}>Solid</option>
                    <option value="1" ${isSolution === '1' ? 'selected' : ''}>Solution</option>
                </select>
            </td>
            <td class="text-center">
                <span class="badge bg-${tableId === 'nonregulatedTable' ? 'info' : 'danger'} chemical-stock" data-name="${chemicalName}">
                    ${availableStock} ${unit}
                </span>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="${tableId === 'nonregulatedTable' ? 'nonregulated' : 'regulated'}[${rowCount}][quantity_per_group]" class="form-control ${tableId === 'nonregulatedTable' ? 'quantity-per-group' : 'pdea-quantity-per-group'}" step="0.1" min="0.1">
                    <input type="text" name="${tableId === 'nonregulatedTable' ? 'nonregulated' : 'regulated'}[${rowCount}][unit]" class="form-control" value="${unit}" readonly>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="${tableId === 'nonregulatedTable' ? 'nonregulated' : 'regulated'}[${rowCount}][total_quantity]" class="form-control ${tableId === 'nonregulatedTable' ? 'total-quantity' : 'pdea-total-quantity'}" step="0.1" min="0.1" readonly>
                    <input type="text" class="form-control" value="${unit}" readonly>
                </div>
            </td>
            <td>
                <input type="text" name="${tableId === 'nonregulatedTable' ? 'nonregulated' : 'regulated'}[${rowCount}][instruction]" class="form-control form-control-sm">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger ${tableId === 'nonregulatedTable' ? 'remove-row' : 'remove-pdea-row'}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        table.appendChild(newRow);
        
        // Add event listeners to the new row
        const newQuantityInput = newRow.querySelector(`.${tableId === 'nonregulatedTable' ? 'quantity-per-group' : 'pdea-quantity-per-group'}`);
        newQuantityInput.addEventListener('input', calculateTotalQuantity);
        
        const removeButton = newRow.querySelector(`.${tableId === 'nonregulatedTable' ? 'remove-row' : 'remove-pdea-row'}`);
        removeButton.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this chemical?')) {
                const row = this.closest('tr');
                row.remove();
                
                // Check if table is now empty
                if (table.querySelectorAll('tr').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `<td colspan="7" class="text-center">No ${tableId === 'nonregulatedTable' ? 'non-regulated' : 'PDEA-regulated'} chemicals requested.</td>`;
                    table.appendChild(emptyRow);
                }
                
                calculateTotalQuantity();
            }
        });
        
        // Reset the select
        select.selectedIndex = 0;
        
        // Calculate total quantity for the new row
        calculateTotalQuantity();
    }
    
    // Function to add a selected item to table (for glassware and equipment)
    function addSelectedItem(tableId, selectId, rowClass) {
        const select = document.getElementById(selectId);
        const table = document.querySelector(`#${tableId} tbody`);
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption.value) {
            alert('Please select an item first');
            return;
        }
        
        // Check if table has a "no items" row
        const noItemsRow = table.querySelector('tr td[colspan]');
        if (noItemsRow) {
            noItemsRow.closest('tr').remove();
        }
        
        // Get current row count
        const rowCount = table.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-index', rowCount);
        newRow.classList.add(rowClass);
        
        const itemName = selectedOption.value;
        const unit = selectedOption.getAttribute('data-unit');
        const type = selectedOption.getAttribute('data-type');
        
        newRow.innerHTML = `
            <td>
                <input type="text" name="${tableId === 'glasswareTable' ? 'glassware' : 'equipment'}[${rowCount}][name]" class="form-control form-control-sm" value="${itemName}">
            </td>
            <td>
                <input type="text" name="${tableId === 'glasswareTable' ? 'glassware' : 'equipment'}[${rowCount}][type]" class="form-control form-control-sm" value="${type}">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="${tableId === 'glasswareTable' ? 'glassware' : 'equipment'}[${rowCount}][quantity_per_group]" class="form-control ${tableId === 'glasswareTable' ? 'glassware-quantity-per-group' : 'equipment-quantity-per-group'}" step="0.1" min="0.1">
                    <input type="text" name="${tableId === 'glasswareTable' ? 'glassware' : 'equipment'}[${rowCount}][unit]" class="form-control" value="${unit}" readonly>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="${tableId === 'glasswareTable' ? 'glassware' : 'equipment'}[${rowCount}][total_quantity]" class="form-control ${tableId === 'glasswareTable' ? 'glassware-total-quantity' : 'equipment-total-quantity'}" step="0.1" min="0.1" readonly>
                    <input type="text" class="form-control" value="${unit}" readonly>
                </div>
            </td>
            <td>
                <input type="text" name="${tableId === 'glasswareTable' ? 'glassware' : 'equipment'}[${rowCount}][instruction]" class="form-control form-control-sm">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger ${tableId === 'glasswareTable' ? 'remove-glassware-row' : 'remove-equipment-row'}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        table.appendChild(newRow);
        
        // Add event listeners to the new row
        const newQuantityInput = newRow.querySelector(`.${tableId === 'glasswareTable' ? 'glassware-quantity-per-group' : 'equipment-quantity-per-group'}`);
        newQuantityInput.addEventListener('input', calculateTotalQuantity);
        
        const removeButton = newRow.querySelector(`.${tableId === 'glasswareTable' ? 'remove-glassware-row' : 'remove-equipment-row'}`);
        removeButton.addEventListener('click', function() {
            if (confirm(`Are you sure you want to remove this ${tableId === 'glasswareTable' ? 'glassware' : 'equipment'} item?`)) {
                const row = this.closest('tr');
                row.remove();
                
                // Check if table is now empty
                if (table.querySelectorAll('tr').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `<td colspan="6" class="text-center">No ${tableId === 'glasswareTable' ? 'glassware' : 'equipment'} requested.</td>`;
                    table.appendChild(emptyRow);
                }
                
                calculateTotalQuantity();
            }
        });
        
        // Reset the select
        select.selectedIndex = 0;
        
        // Calculate total quantity for the new row
        calculateTotalQuantity();
    }
    
    // Add event listeners to the "Add Selected" buttons
    document.getElementById('addSelectedChemical').addEventListener('click', function() {
        addSelectedChemical('nonregulatedTable', 'availableChemicals', 'non-regulated-row');
    });
    
    document.getElementById('addSelectedPdeaChemical').addEventListener('click', function() {
        addSelectedChemical('regulatedTable', 'availablePdeaChemicals', 'pdea-row');
    });
    
    document.getElementById('addSelectedGlassware').addEventListener('click', function() {
        addSelectedItem('glasswareTable', 'availableGlassware', 'glassware-row');
    });
    
    document.getElementById('addSelectedEquipment').addEventListener('click', function() {
        addSelectedItem('equipmentTable', 'availableEquipment', 'equipment-row');
    });
    
    // Remove row functionality for non-regulated chemicals
    document.querySelectorAll('.remove-row').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this chemical?')) {
                const row = this.closest('tr');
                const table = row.closest('tbody');
                row.remove();
                
                // Check if table is now empty
                if (table.querySelectorAll('tr').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = '<td colspan="7" class="text-center">No non-regulated chemicals requested.</td>';
                    table.appendChild(emptyRow);
                }
                
                calculateTotalQuantity();
            }
        });
    });
    
    // Remove row functionality for PDEA chemicals
    document.querySelectorAll('.remove-pdea-row').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this PDEA chemical?')) {
                const row = this.closest('tr');
                const table = row.closest('tbody');
                row.remove();
                
                // Check if table is now empty
                if (table.querySelectorAll('tr').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = '<td colspan="7" class="text-center">No PDEA-regulated chemicals requested.</td>';
                    table.appendChild(emptyRow);
                }
                
                calculateTotalQuantity();
            }
        });
    });
    
    // Remove row functionality for glassware
    document.querySelectorAll('.remove-glassware-row').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this glassware item?')) {
                const row = this.closest('tr');
                const table = row.closest('tbody');
                row.remove();
                
                // Check if table is now empty
                if (table.querySelectorAll('tr').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = '<td colspan="6" class="text-center">No glassware requested.</td>';
                    table.appendChild(emptyRow);
                }
                
                calculateTotalQuantity();
            }
        });
    });
    
    // Remove row functionality for equipment
    document.querySelectorAll('.remove-equipment-row').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this equipment item?')) {
                const row = this.closest('tr');
                const table = row.closest('tbody');
                row.remove();
                
                // Check if table is now empty
                if (table.querySelectorAll('tr').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = '<td colspan="6" class="text-center">No equipment requested.</td>';
                    table.appendChild(emptyRow);
                }
                
                calculateTotalQuantity();
            }
        });
    });
    
    // Add event listeners to existing quantity per group inputs
    document.querySelectorAll('.quantity-per-group, .pdea-quantity-per-group, .glassware-quantity-per-group, .equipment-quantity-per-group').forEach(input => {
        input.addEventListener('input', calculateTotalQuantity);
    });
    
    // Initial calculation
    calculateTotalQuantity();
    
    // Debug function to check if form is working
    window.checkForm = function() {
        console.log('Form submission check');
        const form = document.getElementById('reservationEditForm');
        if (form) {
            console.log('Form found:', form);
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            // Check if all required fields have values
            const requiredFields = form.querySelectorAll('[required]');
            let allValid = true;
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    allValid = false;
                    console.log('Missing required field:', field.name, field.value);
                }
            });
            
            console.log('All required fields valid:', allValid);
            return allValid;
        }
    };
});
</script>

<style>
    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .input-group-sm input {
        font-size: 0.875rem;
    }
    
    .form-select-sm {
        font-size: 0.875rem;
    }
    
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
</style>
@endsection