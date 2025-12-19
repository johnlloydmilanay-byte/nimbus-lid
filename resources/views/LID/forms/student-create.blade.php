@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Student Laboratory Reservation Form</h2>
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

    <!-- Form action updated to match the correct route -->
    <form action="{{ route('lid.student-reservations.store') }}" method="POST" id="studentRequestForm">
        @csrf
        <input type="hidden" name="borrower_type" value="Student">

        <!-- Faculty Reference Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Faculty Reference</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Faculty Reference Number: <span class="text-danger">*</span></label>
                        <input type="text" name="faculty_reference" class="form-control @error('faculty_reference') is-invalid @enderror" 
                               placeholder="Enter faculty reference (e.g., 2526-A-000001)" 
                               value="{{ old('faculty_reference') }}" 
                               id="facultyReference"
                               required>
                        @error('faculty_reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="fetchFacultyReservation()" id="fetchBtn">
                            <i class="bi bi-search"></i> Fetch Reservation Details
                        </button>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Faculty Name:</label>
                        <input type="text" class="form-control" id="facultyName" readonly>
                    </div>
                </div>
                
                <!-- Reservation Details (Will be populated dynamically) -->
                <div id="reservationDetails" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <h6>Faculty Reservation Details:</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Course:</strong> <span id="courseDetails">N/A</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Activity:</strong> <span id="activityDetails">N/A</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Groups Remaining:</strong> <span id="groupsRemaining">0</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong> <span id="reservationStatus">Not checked</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <strong>Date Requested:</strong> <span id="dateRequested">N/A</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Term:</strong> <span id="term">N/A</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Room:</strong> <span id="room">N/A</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Faculty:</strong> <span id="facultyNameDisplay">N/A</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Warning (shown when not approved) -->
                    <div id="statusWarning" class="alert alert-warning mt-3" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="statusMessage"></span>
                    </div>
                    
                    <!-- Items List -->
                    <div class="mt-3" id="itemsSection" style="display: none;">
                        <h6>Requested Items:</h6>
                        
                        <!-- Tabs for different item types -->
                        <ul class="nav nav-tabs" id="itemsTabs" role="tablist">
                            <li class="nav-item" role="presentation" id="chemicalsTabContainer" style="display: none;">
                                <button class="nav-link active" id="chemicals-tab" data-bs-toggle="tab" data-bs-target="#chemicals" type="button" role="tab" aria-controls="chemicals" aria-selected="true">Chemicals</button>
                            </li>
                            <li class="nav-item" role="presentation" id="glasswareTabContainer" style="display: none;">
                                <button class="nav-link" id="glassware-tab" data-bs-toggle="tab" data-bs-target="#glassware" type="button" role="tab" aria-controls="glassware" aria-selected="false">Glassware</button>
                            </li>
                            <li class="nav-item" role="presentation" id="equipmentTabContainer" style="display: none;">
                                <button class="nav-link" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment" type="button" role="tab" aria-controls="equipment" aria-selected="false">Equipment</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="itemsTabsContent">
                            <!-- Chemicals Tab -->
                            <div class="tab-pane fade show active" id="chemicals" role="tabpanel" aria-labelledby="chemicals-tab">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Chemical Name</th>
                                                <th>Type</th>
                                                <th>Concentration</th>
                                                <th>Quantity per Group</th>
                                                <th>Instructions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="chemicalsList">
                                            <!-- Will be populated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Glassware Tab -->
                            <div class="tab-pane fade" id="glassware" role="tabpanel" aria-labelledby="glassware-tab">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Item Name</th>
                                                <th>Type</th>
                                                <th>Quantity per Group</th>
                                                <th>Instructions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="glasswareList">
                                            <!-- Will be populated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Equipment Tab -->
                            <div class="tab-pane fade" id="equipment" role="tabpanel" aria-labelledby="equipment-tab">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Item Name</th>
                                                <th>Type</th>
                                                <th>Quantity per Group</th>
                                                <th>Instructions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="equipmentList">
                                            <!-- Will be populated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Student Name (Group Leader): <span class="text-danger">*</span></label>
                        <input type="text" name="borrower_name" class="form-control @error('borrower_name') is-invalid @enderror" 
                               placeholder="Enter your full name" 
                               value="{{ old('borrower_name') }}" 
                               required>
                        @error('borrower_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Student ID Number: <span class="text-danger">*</span></label>
                        <input type="text" name="student_id" class="form-control @error('student_id') is-invalid @enderror" 
                               placeholder="Enter student ID" 
                               value="{{ old('student_id') }}" 
                               required>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Email Address:</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Enter email address" 
                               value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Contact Number:</label>
                        <input type="tel" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" 
                               placeholder="Enter contact number" 
                               value="{{ old('contact_number') }}">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Group Number: <span class="text-danger">*</span></label>
                        <select name="group_number" class="form-select @error('group_number') is-invalid @enderror" id="groupNumber" required disabled>
                            <option value="">Select Group Number</option>
                            <!-- Will be populated dynamically -->
                        </select>
                        @error('group_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Only available when faculty reservation is approved</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                <i class="bi bi-check-circle"></i> Submit Student Reservation
            </button>
        </div>
    </form>
</div>

<script>
    let facultyData = null;
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    
    function fetchFacultyReservation() {
        const reference = document.getElementById('facultyReference').value.trim();
        if (!reference) {
            showAlert('Please enter a faculty reference number', 'danger');
            return;
        }
        
        console.log('Fetching faculty reservation for reference:', reference);
        
        // Show loading state
        const fetchBtn = document.getElementById('fetchBtn');
        const originalBtnText = fetchBtn.innerHTML;
        fetchBtn.disabled = true;
        fetchBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
        
        // Construct URL using the provided route name
        const url = "{{ route('lid.reservations.get-faculty', ':reference') }}".replace(':reference', encodeURIComponent(reference));

        // Clear previous data
        facultyData = null;
        document.getElementById('facultyName').value = '';
        document.getElementById('reservationDetails').style.display = 'none';
        document.getElementById('chemicalsList').innerHTML = '';
        document.getElementById('glasswareList').innerHTML = '';
        document.getElementById('equipmentList').innerHTML = '';
        document.getElementById('groupNumber').innerHTML = '<option value="">Select Group Number</option>';
        document.getElementById('groupNumber').disabled = true;
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('itemsSection').style.display = 'none';
        document.getElementById('statusWarning').style.display = 'none';

        // Fetch reservation details via AJAX
        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => {
                if (!response.ok) {
                    // Try to parse error response as JSON first
                    return response.text().then(text => {
                        try {
                            const errorData = JSON.parse(text);
                            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                        } catch (e) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Faculty reservation data received:', data);
                
                // Show reservation details section
                document.getElementById('reservationDetails').style.display = 'block';
                
                // Check if reservation is not approved
                if (!data.success && data.status && data.status !== 'Approved') {
                    // Show status warning
                    document.getElementById('statusWarning').style.display = 'block';
                    document.getElementById('statusMessage').textContent = data.status_message || data.message || 'Reservation is not approved';
                    
                    // Show basic reservation info
                    document.getElementById('courseDetails').textContent = 'N/A';
                    document.getElementById('activityDetails').textContent = 'N/A';
                    document.getElementById('dateRequested').textContent = 'N/A';
                    document.getElementById('term').textContent = 'N/A';
                    document.getElementById('room').textContent = 'N/A';
                    document.getElementById('facultyNameDisplay').textContent = 'N/A';
                    document.getElementById('groupsRemaining').textContent = '0';
                    
                    // Show status with badge
                    let statusBadge = '';
                    let statusClass = 'warning';
                    if (data.status === 'Rejected') {
                        statusBadge = '<span class="badge bg-danger">Rejected</span>';
                        statusClass = 'danger';
                    } else if (data.status === 'Pending') {
                        statusBadge = '<span class="badge bg-warning">Pending</span>';
                    } else if (data.status === 'Cancelled') {
                        statusBadge = '<span class="badge bg-secondary">Cancelled</span>';
                        statusClass = 'secondary';
                    } else {
                        statusBadge = `<span class="badge bg-${statusClass}">${data.status}</span>`;
                    }
                    
                    document.getElementById('reservationStatus').innerHTML = statusBadge;
                    
                    // Disable form submission
                    document.getElementById('groupNumber').disabled = true;
                    document.getElementById('submitBtn').disabled = true;
                    
                    showAlert(data.message || 'Faculty reservation is not approved', 'danger');
                    return;
                }
                
                if (data.success) {
                    facultyData = data;
                    
                    // Populate faculty details
                    document.getElementById('facultyName').value = data.reservation.borrower_name;
                    document.getElementById('facultyNameDisplay').textContent = data.reservation.borrower_name;
                    
                    // Show status badge
                    let statusBadge = '';
                    let statusClass = 'success';
                    if (data.reservation.status === 'Approved') {
                        statusBadge = '<span class="badge bg-success">Approved</span>';
                    } else if (data.reservation.status === 'Rejected') {
                        statusBadge = '<span class="badge bg-danger">Rejected</span>';
                        statusClass = 'danger';
                    } else if (data.reservation.status === 'Pending') {
                        statusBadge = '<span class="badge bg-warning">Pending</span>';
                        statusClass = 'warning';
                    } else if (data.reservation.status === 'Cancelled') {
                        statusBadge = '<span class="badge bg-secondary">Cancelled</span>';
                        statusClass = 'secondary';
                    } else {
                        statusBadge = `<span class="badge bg-${statusClass}">${data.reservation.status}</span>`;
                    }
                    
                    document.getElementById('reservationStatus').innerHTML = statusBadge;
                    
                    // Populate course details
                    const subjectCode = data.reservation.subject_code || 'N/A';
                    const subjectDesc = data.reservation.subject_description || 'N/A';
                    document.getElementById('courseDetails').textContent = 
                        `${subjectCode} - ${subjectDesc}`;
                    
                    const activityTitle = data.reservation.activity_title || 'N/A';
                    const activityNo = data.reservation.activity_no || 'N/A';
                    document.getElementById('activityDetails').textContent = 
                        `${activityTitle} (Activity #${activityNo})`;
                    
                    // Populate additional details
                    const dateRequested = data.reservation.date_requested ? 
                        new Date(data.reservation.date_requested).toLocaleDateString() : 'N/A';
                    document.getElementById('dateRequested').textContent = dateRequested;
                    
                    document.getElementById('term').textContent = data.reservation.term || 'N/A';
                    document.getElementById('room').textContent = data.reservation.room_no || 'N/A';
                    
                    // Calculate remaining groups
                    const totalGroups = data.reservation.number_of_groups || 0;
                    const usedGroups = data.student_count || 0;
                    const remainingGroups = totalGroups - usedGroups;
                    
                    document.getElementById('groupsRemaining').textContent = 
                        `${remainingGroups} of ${totalGroups} available`;
                    
                    // Populate group number dropdown
                    const groupSelect = document.getElementById('groupNumber');
                    groupSelect.innerHTML = '<option value="">Select Group Number</option>';
                    
                    if (totalGroups > 0 && data.reservation.status === 'Approved') {
                        for (let i = 1; i <= totalGroups; i++) {
                            const option = document.createElement('option');
                            option.value = i;
                            option.textContent = `Group ${i}`;
                            
                            // Disable already used group numbers
                            if (i <= usedGroups) {
                                option.disabled = true;
                                option.textContent += ' (Already reserved)';
                            }
                            
                            groupSelect.appendChild(option);
                        }
                        groupSelect.disabled = false;
                    } else {
                        groupSelect.disabled = true;
                    }
                    
                    // Populate items list if reservation is approved
                    const chemicalsList = document.getElementById('chemicalsList');
                    const glasswareList = document.getElementById('glasswareList');
                    const equipmentList = document.getElementById('equipmentList');
                    
                    chemicalsList.innerHTML = '';
                    glasswareList.innerHTML = '';
                    equipmentList.innerHTML = '';
                    
                    // Track which tabs should be visible
                    let hasChemicals = false;
                    let hasGlassware = false;
                    let hasEquipment = false;
                    
                    if (data.reservation.status === 'Approved') {
                        // Add non-regulated chemicals
                        if (data.chemicals && data.chemicals.length > 0) {
                            hasChemicals = true;
                            data.chemicals.forEach(chem => {
                                const row = document.createElement('tr');
                                const concentrationText = chem.has_concentration ? 
                                    `${chem.concentration_value || 0} ${chem.concentration_unit || ''}` : 'N/A';
                                    
                                row.innerHTML = `
                                    <td>${chem.name}</td>
                                    <td>${chem.is_solution ? 'Solution' : 'Solid'}</td>
                                    <td>${concentrationText}</td>
                                    <td>${chem.quantity_per_group} ${chem.unit}</td>
                                    <td>${chem.instruction || 'N/A'}</td>
                                `;
                                chemicalsList.appendChild(row);
                            });
                        }
                        
                        // Add PDEA chemicals
                        if (data.pdea_chemicals && data.pdea_chemicals.length > 0) {
                            hasChemicals = true;
                            data.pdea_chemicals.forEach(chem => {
                                const row = document.createElement('tr');
                                const concentrationText = chem.has_concentration ? 
                                    `${chem.concentration_value || 0} ${chem.concentration_unit || ''}` : 'N/A';
                                    
                                row.innerHTML = `
                                    <td class="text-danger">${chem.name} (PDEA)</td>
                                    <td>${chem.is_solution ? 'Solution' : 'Solid'}</td>
                                    <td>${concentrationText}</td>
                                    <td>${chem.quantity_per_group} ${chem.unit}</td>
                                    <td>${chem.instruction || 'N/A'}</td>
                                `;
                                chemicalsList.appendChild(row);
                            });
                        }
                        
                        // Add glassware items
                        if (data.glassware && data.glassware.length > 0) {
                            hasGlassware = true;
                            data.glassware.forEach(item => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${item.name}</td>
                                    <td>${item.type}</td>
                                    <td>${item.quantity_per_group} ${item.unit}</td>
                                    <td>${item.instruction || 'N/A'}</td>
                                `;
                                glasswareList.appendChild(row);
                            });
                        }
                        
                        // Add equipment items
                        if (data.equipment && data.equipment.length > 0) {
                            hasEquipment = true;
                            data.equipment.forEach(item => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${item.name}</td>
                                    <td>${item.type}</td>
                                    <td>${item.quantity_per_group} ${item.unit}</td>
                                    <td>${item.instruction || 'N/A'}</td>
                                `;
                                equipmentList.appendChild(row);
                            });
                        }
                    }
                    
                    // Show/hide tabs based on available items
                    document.getElementById('chemicalsTabContainer').style.display = hasChemicals ? 'block' : 'none';
                    document.getElementById('glasswareTabContainer').style.display = hasGlassware ? 'block' : 'none';
                    document.getElementById('equipmentTabContainer').style.display = hasEquipment ? 'block' : 'none';
                    
                    // Show items section if there are any items
                    if (hasChemicals || hasGlassware || hasEquipment) {
                        document.getElementById('itemsSection').style.display = 'block';
                        
                        // Activate the first visible tab
                        if (hasChemicals) {
                            document.getElementById('chemicals-tab').click();
                        } else if (hasGlassware) {
                            document.getElementById('glassware-tab').click();
                        } else if (hasEquipment) {
                            document.getElementById('equipment-tab').click();
                        }
                    }
                    
                    // Enable submit button if reservation is approved and groups are available
                    if (data.reservation.status === 'Approved' && remainingGroups > 0) {
                        document.getElementById('submitBtn').disabled = false;
                        showAlert('Faculty reservation found and approved! Please select a group number.', 'success');
                    } else if (data.reservation.status !== 'Approved') {
                        showAlert(`Faculty reservation is ${data.reservation.status.toLowerCase()}. Students can only make reservations when the faculty reservation is approved.`, 'warning');
                        document.getElementById('submitBtn').disabled = true;
                    } else {
                        showAlert('All groups for this reservation have been taken.', 'warning');
                        document.getElementById('submitBtn').disabled = true;
                    }
                    
                } else {
                    console.error('Server returned failure:', data);
                    showAlert(data.message || 'Reservation not found or invalid reference number', 'danger');
                }
            })
            .catch(error => {
                console.error('Error fetching faculty reservation:', error);
                showAlert(`Error: ${error.message}. Please check the reference number and try again.`, 'danger');
            })
            .finally(() => {
                // Restore button state
                fetchBtn.disabled = false;
                fetchBtn.innerHTML = originalBtnText;
            });
    }
    
    // Enable submit when group is selected
    document.getElementById('groupNumber').addEventListener('change', function() {
        const submitBtn = document.getElementById('submitBtn');
        if (this.value && facultyData && facultyData.reservation?.status === 'Approved') {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    });
    
    // Form submission
    document.getElementById('studentRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!facultyData) {
            showAlert('Please fetch faculty reservation details first', 'danger');
            return;
        }
        
        // Check if faculty reservation is approved
        if (facultyData.reservation?.status !== 'Approved') {
            showAlert('Faculty reservation is not approved. Students can only make reservations when the faculty reservation is approved.', 'danger');
            return;
        }
        
        const groupNumber = document.getElementById('groupNumber').value;
        if (!groupNumber) {
            showAlert('Please select a group number', 'danger');
            return;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';

        const form = e.target;
        const formData = new FormData(form);
        
        console.log('Submitting student reservation with data:', Object.fromEntries(formData.entries()));

        // Submit via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            console.log('Server response status:', response.status);
            
            // First, try to parse as JSON
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    return {
                        status: response.status,
                        ok: response.ok,
                        data: data
                    };
                } catch (e) {
                    // If not JSON, it's HTML
                    console.error('Response is not JSON:', text.substring(0, 100));
                    throw new Error('Server returned HTML instead of JSON. Please check server logs.');
                }
            });
        })
        .then(result => {
            if (!result.ok) {
                // Handle error responses
                if (result.data && result.data.errors) {
                    const errorMessages = Object.values(result.data.errors).flat().join('<br>');
                    throw new Error(errorMessages || result.data.message || 'Submission failed');
                }
                throw new Error(result.data?.message || `Server error: ${result.status}`);
            }
            
            console.log('Submission successful:', result.data);
            
            // Check if we got a redirect URL
            if (result.data.redirect_url) {
                window.location.href = result.data.redirect_url;
            } else if (result.data.success) {
                // Show success message and redirect
                showAlert(result.data.message || 'Reservation created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = "{{ route('lid.reservations.index') }}";
                }, 2000);
            } else {
                throw new Error(result.data.message || 'Submission failed');
            }
        })
        .catch(error => {
            console.error('Submission failed:', error);
            let errorMessage = 'Failed to create student reservation. Please try again.';
            
            // Handle Laravel validation errors
            if (error.message) {
                errorMessage = error.message;
            }
            
            showAlert(errorMessage, 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
    
    // Helper function to show alerts
    function showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        
        // Remove existing alerts of the same type if needed
        const existingAlerts = alertContainer.querySelectorAll('.alert');
        if (existingAlerts.length > 3) {
            existingAlerts[0].remove();
        }
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        alertContainer.insertBefore(alertDiv, alertContainer.firstChild);
        
        // Auto-dismiss after 7 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 7000);
    }
    
    // Add enter key support for faculty reference field
    document.getElementById('facultyReference').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchFacultyReservation();
        }
    });
    
    // Add CSRF token to form if not already present
    document.addEventListener('DOMContentLoaded', function() {
        // Check if CSRF token exists in form
        const csrfInput = document.querySelector('input[name="_token"]');
        if (!csrfInput) {
            const form = document.getElementById('studentRequestForm');
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = csrfToken;
            form.appendChild(csrfField);
        }
    });
</script>

<style>
    .alert {
        animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    #groupNumber:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
    
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }
    
    .bg-success {
        background-color: #198754 !important;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .bg-danger {
        background-color: #dc3545 !important;
    }
    
    .bg-secondary {
        background-color: #6c757d !important;
    }
    
    /* Status-specific styling */
    .status-pending {
        border-left: 4px solid #ffc107;
    }
    
    .status-rejected {
        border-left: 4px solid #dc3545;
    }
    
    .status-approved {
        border-left: 4px solid #198754;
    }
    
    .status-cancelled {
        border-left: 4px solid #6c757d;
    }
    
    /* Responsive table */
    .table-responsive {
        max-height: 300px;
        overflow-y: auto;
    }
    
    /* Loading state for buttons */
    button:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
    
    /* Form validation styling */
    .is-invalid {
        border-color: #dc3545 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
    
    /* Tab styling */
    .nav-tabs {
        margin-bottom: 1rem;
    }
    
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
</style>

@endsection