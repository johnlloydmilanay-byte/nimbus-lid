<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold">Borrower Information</h6>
        <table class="table table-sm table-borderless">
            <tr>
                <td width="35%"><strong>Name:</strong></td>
                <td>{{ $reservation->borrower_name }}</td>
            </tr>
            <tr>
                <td><strong>Type:</strong></td>
                <td>{{ $reservation->borrower_type }}</td>
            </tr>
            <tr>
                <td><strong>Reference Number:</strong></td>
                <td><span class="badge bg-secondary">{{ $reservation->reference_number }}</span></td>
            </tr>
            @if($reservation->faculty_reference_id)
            <tr>
                <td><strong>Faculty Reference:</strong></td>
                <td><a href="#" onclick="viewReservation({{ $reservation->faculty_reference_id }})">{{ $reservation->facultyReservation->reference_number }}</a></td>
            </tr>
            @endif
            <tr>
                <td><strong>Date Requested:</strong></td>
                <td>{{ \Carbon\Carbon::parse($reservation->date_requested)->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Purpose:</strong></td>
                <td>{{ $reservation->purpose }}</td>
            </tr>
            <tr>
                <td><strong>Room No.:</strong></td>
                <td>{{ $reservation->room_no }}</td>
            </tr>
            <tr>
                <td><strong>Term:</strong></td>
                <td>{{ $reservation->term }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold">Reservation Details</h6>
        <table class="table table-sm table-borderless">
            <tr>
                <td width="35%"><strong>Start Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($reservation->start_date)->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td><strong>End Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($reservation->end_date)->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Time:</strong></td>
                <td>{{ $reservation->time }}</td>
            </tr>
            <tr>
                <td><strong>Number of Groups:</strong></td>
                <td>{{ $reservation->number_of_groups }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    @php
                        $status = $reservation->status ?? 'Draft';
                        $statusClass = $status == 'Approved' ? 'bg-success' : ($status == 'Pending' ? 'bg-warning' : ($status == 'Rejected' ? 'bg-danger' : 'bg-secondary'));
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                </td>
            </tr>
            @if($reservation->borrower_type === 'Student' && $reservation->studentReservations->count() > 0)
            <tr>
                <td><strong>Student Groups:</strong></td>
                <td>
                    @foreach($reservation->studentReservations as $student)
                        <a href="#" onclick="viewReservation({{ $student->id }})" class="badge bg-info me-1">{{ $student->reference_number }}</a>
                    @endforeach
                </td>
            </tr>
            @endif
        </table>
    </div>
</div>

@if($reservation->program || $reservation->subject_code)
<hr>
<h6 class="fw-bold">Course Information</h6>
<table class="table table-sm table-borderless">
    <tr>
        <td width="20%"><strong>Program:</strong></td>
        <td>{{ $reservation->program ?? 'N/A' }}</td>
        <td width="20%"><strong>Year & Section:</strong></td>
        <td>{{ $reservation->year_section ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>Subject Code:</strong></td>
        <td>{{ $reservation->subject_code ?? 'N/A' }}</td>
        <td><strong>Subject Description:</strong></td>
        <td>{{ $reservation->subject_description ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>Activity No.:</strong></td>
        <td>{{ $reservation->activity_no ?? 'N/A' }}</td>
        <td><strong>Activity Title:</strong></td>
        <td>{{ $reservation->activity_title ?? 'N/A' }}</td>
    </tr>
</table>
@endif

<!-- Student Groups Section - Only show for Faculty Reservations -->
@if($reservation->borrower_type === 'Faculty Member' && $reservation->studentReservations->count() > 0)
<hr>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-people-fill me-2"></i>Student Groups
        </h6>
        <span class="badge bg-primary">{{ $reservation->studentReservations->count() }} Groups</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Group #</th>
                        <th>Group Leader</th>
                        <th>Student ID</th>
                        <th>Reference #</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->studentReservations as $student)
                    <tr>
                        <td><span class="badge bg-primary">{{ $student->group_number }}</span></td>
                        <td>{{ $student->borrower_name }}</td>
                        <td>{{ $student->student_id }}</td>
                        <td><span class="badge bg-info">{{ $student->reference_number }}</span></td>
                        <td>
                            @if($student->email)
                                <div class="mb-1">
                                    <i class="bi bi-envelope-fill text-primary me-1"></i>
                                    <small>{{ $student->email }}</small>
                                </div>
                            @endif
                            @if($student->contact_number)
                                <div>
                                    <i class="bi bi-telephone-fill text-success me-1"></i>
                                    <small>{{ $student->contact_number }}</small>
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $studentStatus = $student->status ?? 'Draft';
                                $studentStatusClass = $studentStatus == 'Approved' ? 'bg-success' : ($studentStatus == 'Pending' ? 'bg-warning' : ($studentStatus == 'Rejected' ? 'bg-danger' : 'bg-secondary'));
                            @endphp
                            <span class="badge {{ $studentStatusClass }}">{{ ucfirst($studentStatus) }}</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewReservation({{ $student->id }})">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<hr>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-0">Requested Items</h6>
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

<!-- Tab Content -->
<div class="tab-content" id="reservationTabContent">
    <!-- Chemicals Tab -->
    <div class="tab-pane fade show active" id="chemicals" role="tabpanel">
        <!-- Non-Regulated Chemicals -->
        @if($reservation->chemicalRequests && $reservation->chemicalRequests->count() > 0)
        <h6 class="text-primary mb-2">Non-Regulated Chemicals</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Concentration</th>
                        <th>Conc. Value</th>
                        <th>Quantity per Group</th>
                        <th>Total Quantity</th>
                        <th>Instruction</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->chemicalRequests as $chem)
                    <tr>
                        <td>{{ $chem->name }}</td>
                        <td class="text-center">{{ $chem->is_solution ? 'Solution' : 'Solid' }}</td>
                        <td class="text-center">{{ $chem->has_concentration ? 'Yes' : 'No' }}</td>
                        <td>{{ $chem->has_concentration ? ($chem->concentration_value . ' ' . $chem->concentration_unit) : 'N/A' }}</td>
                        <td>{{ $chem->quantity_per_group }} {{ $chem->unit }}</td>
                        <td>{{ $chem->total_quantity }} {{ $chem->unit }}</td>
                        <td>{{ $chem->instruction ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No non-regulated chemicals requested.</p>
        @endif

        <!-- PDEA-Regulated Chemicals -->
        @if($reservation->pdeaRequests && $reservation->pdeaRequests->count() > 0)
        <h6 class="text-danger mb-2 mt-4">PDEA-Regulated Chemicals</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Concentration</th>
                        <th>Conc. Value</th>
                        <th>Quantity per Group</th>
                        <th>Total Quantity</th>
                        <th>Instruction</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->pdeaRequests as $chem)
                    <tr>
                        <td>{{ $chem->name }}</td>
                        <td class="text-center">{{ $chem->is_solution ? 'Solution' : 'Solid' }}</td>
                        <td class="text-center">{{ $chem->has_concentration ? 'Yes' : 'No' }}</td>
                        <td>{{ $chem->has_concentration ? ($chem->concentration_value . ' ' . $chem->concentration_unit) : 'N/A' }}</td>
                        <td>{{ $chem->quantity_per_group }} {{ $chem->unit }}</td>
                        <td>{{ $chem->total_quantity }} {{ $chem->unit }}</td>
                        <td>{{ $chem->instruction ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No PDEA-regulated chemicals requested.</p>
        @endif
    </div>

    <!-- Glassware Tab -->
    <div class="tab-pane fade" id="glassware" role="tabpanel">
        @if($reservation->glasswareRequests && $reservation->glasswareRequests->count() > 0)
        <h6 class="text-info mb-2">Glassware / Apparatus / Materials</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Quantity per Group</th>
                        <th>Total Quantity</th>
                        <th>Instruction</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->glasswareRequests as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->quantity_per_group }} {{ $item->unit }}</td>
                        <td>{{ $item->total_quantity }} {{ $item->unit }}</td>
                        <td>{{ $item->instruction ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No glassware, apparatus, or materials requested.</p>
        @endif
    </div>

    <!-- Equipment Tab -->
    <div class="tab-pane fade" id="equipment" role="tabpanel">
        @if($reservation->equipmentRequests && $reservation->equipmentRequests->count() > 0)
        <h6 class="text-warning mb-2">Equipment / Consumables</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Quantity per Group</th>
                        <th>Total Quantity</th>
                        <th>Instruction</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->equipmentRequests as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->quantity_per_group }} {{ $item->unit }}</td>
                        <td>{{ $item->total_quantity }} {{ $item->unit }}</td>
                        <td>{{ $item->instruction ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No equipment or consumables requested.</p>
        @endif
    </div>
</div>

<script>
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
</script>