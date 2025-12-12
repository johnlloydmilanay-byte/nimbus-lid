<div class="modal fade" id="editFeeModal" tabindex="-1" aria-labelledby="editFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('accounting.feesmanagement.update.post') }}" id="editFeeForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="fee_ids" id="edit_fee_ids">
                <input type="hidden" name="selected_feesname_id" value="{{ $selected_feesname->id ?? '' }}">
                <input type="hidden" name="year" value="{{ request('year') }}">
                <input type="hidden" name="term" value="{{ request('term') }}">
                <input type="hidden" name="academicgroup_id" value="{{ request('academicgroup_id') }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editFeeModalLabel">
                        <i class="fa fa-edit"></i> Edit {{ $selected_feesname->name ?? 'Fee' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if(isset($selected_feesname) && in_array($selected_feesname->id, [2,3]))
                        <!-- Fee Name & Type -->
                        <div class="mb-3">
                            <label class="fw-bold">Fee Name</label>
                            <input type="text" name="fee_name" class="form-control" id="edit_fee_name">
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Fee Type</label>
                            <select name="fee_types_id" class="form-select" id="edit_fee_types_id" required>
                                <option value="">Select Fee Type</option>
                                @foreach($feestypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if(isset($selected_feesname) && in_array($selected_feesname->id, [4]))
                        <!-- Lab Subject -->
                        <div class="mb-3">
                            <label class="fw-bold">Subject</label>
                            <div id="class-checkboxes" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                @foreach($labSchedule as $sched)
                                    @if($sched->subject)
                                        <div class="form-check class-item" data-dept="{{ $sched->department_id ?? '' }}">
                                            <input class="form-check-input" type="checkbox" name="class_schedule_ids[]" 
                                                   value="{{ $sched->id }}" id="class_{{ $sched->id }}">
                                            <label class="form-check-label" for="class_{{ $sched->id }}">
                                                {{ $sched->subject->code }} : {{ $sched->section }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Rate & SY & Term -->
                    <div class="row mb-3">
                        <div class="col-12 {{ isset($selected_feesname) && $selected_feesname->id == 4 ? 'col-md-6 mb-3' : 'col-md-4' }}">
                            <label class="fw-bold">Rate</label>
                            <input type="number" step="0.01" name="rate" class="form-control" id="edit_rate" required>
                        </div>
                        @if(isset($selected_feesname) && $selected_feesname->id == 4)
                            <div class="col-md-6">
                                <label class="fw-bold">Deposit</label>
                                <input type="number" step="0.01" name="deposit" class="form-control" id="edit_deposit" required>
                            </div>
                        @endif
                        <div class="col-12 {{ isset($selected_feesname) && $selected_feesname->id == 4 ? 'col-md-6' : 'col-md-4' }}">
                            <label class="fw-bold">School Year</label>
                            <input type="text" class="form-control" value="{{ request('year') }}-{{ request('year') + 1 }}" disabled>
                        </div>
                        <div class="col-12 {{ isset($selected_feesname) && $selected_feesname->id == 4 ? 'col-md-6' : 'col-md-4' }}">
                            <label class="fw-bold">Term</label>
                            <select class="form-select" disabled>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ request('term') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- GL Account -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                {{ (isset($selected_feesname) && $selected_feesname->id == 2) ? 'GL Account' : 'AR Account' }}
                            </label>

                            @if(isset($selected_feesname) && $selected_feesname->id == 2)
                                <select name="gl_account" class="form-select" id="edit_gl_account" required>
                                    <option value="">Select GL Account</option>
                                    @foreach($accounts as $a)
                                        <option value="{{ $a->accountcode }}">
                                            {{ $a->accountcode }} : {{ $a->accountname }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <select name="ar_account" class="form-select" id="edit_ar_account" required>
                                    <option value="">Select AR Account</option>
                                    @php
                                        $list = (isset($selected_feesname) && $selected_feesname->id == 3) ? $othersAccounts : $labAccounts;
                                    @endphp

                                    @foreach($list as $a)
                                        <option value="{{ $a->accountcode }}">
                                            {{ $a->accountcode }} : {{ $a->accountname }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    @if(isset($selected_feesname) && in_array($selected_feesname->id, [2,3]))
                        <!-- Academic Group & Department -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">Academic Group</label>
                                <select id="academicgroup_id" name="academicgroup_id" class="form-select" disabled>
                                    @foreach($academicgroup as $acad)
                                        <option value="{{ $acad->id }}">{{ $acad->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="academicgroup_id" id="edit_academicgroup_id">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">Department</label>
                                <select id="department_id" name="department_id" class="form-select" disabled>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" data-acad="{{ $dept->academicgroup_id }}">
                                            {{ $dept->code }} : {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="department_id" id="edit_department_id">
                            </div>
                        </div>
                    @endif

                    @if(isset($selected_feesname) && in_array($selected_feesname->id, [3]))
                        <!-- Class Schedules Checkboxes -->
                        <div class="mb-3">
                            <label class="fw-bold">Class Schedules:</label>
                            <div id="class-checkboxes" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                @foreach($classschedule as $sched)
                                    @if($sched->subject)
                                        <div class="form-check class-item" data-dept="{{ $sched->department_id ?? '' }}">
                                            <input class="form-check-input" type="checkbox" name="class_schedule_ids[]" 
                                                   value="{{ $sched->id }}" id="class_{{ $sched->id }}">
                                            <label class="form-check-label" for="class_{{ $sched->id }}">
                                                {{ $sched->subject->code }} : {{ $sched->section }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Programs Checkboxes -->
                        <div class="mb-3">
                            <label class="fw-bold">Programs:</label>
                            <div id="program-checkboxes" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                @foreach($programs as $program)
                                    <div class="form-check program-item" data-dept="{{ $program->department_id }}">
                                        <input class="form-check-input" type="checkbox" name="program_ids[]" 
                                               value="{{ $program->id }}" id="program_{{ $program->id }}">
                                        <label class="form-check-label" for="program_{{ $program->id }}">
                                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($selected_feesname) && in_array($selected_feesname->id, [2,3]))
                        <!-- Student Status, Year Level, Year Entry -->
                        <div class="row">
                            <div class="mb-3 {{ $selected_feesname->id == 2 ? 'col-md-4' : 'col-md-6' }}">
                                <label class="fw-bold">Student Status</label>
                                <select name="studentstatus_id" class="form-select" id="edit_studentstatus_id">
                                    <option value="">Select Student Status</option>
                                    @foreach($studentstatus as $studentstat)
                                        <option value="{{ $studentstat->id }}">{{ $studentstat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 {{ $selected_feesname->id == 2 ? 'col-md-4' : 'col-md-6' }}">
                                <label class="fw-bold">Year Level / Grade</label>
                                <select name="year_level" class="form-select" id="edit_year_level">
                                    <option value="">Select Year Level</option>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            @if(isset($selected_feesname) && in_array($selected_feesname->id, [2]))
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Year Entry</label>
                                    <input type="text" name="year_entry" class="form-control" id="edit_year_entry">
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning fw-bold" type="submit">Update {{ $selected_feesname->name ?? 'Fee' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Edit Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editFeeModal');
    
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            // Get all data attributes
            const feeIds = button.getAttribute('data-id');
            const feeName = button.getAttribute('data-fee-name');
            const feeTypesId = button.getAttribute('data-fee-types-id');
            const rate = button.getAttribute('data-rate');
            const arAccount = button.getAttribute('data-ar-account');
            const glAccount = button.getAttribute('data-gl-account');
            const studentstatusId = button.getAttribute('data-studentstatus-id');
            const yearLevel = button.getAttribute('data-year-level');
            const yearEntry = button.getAttribute('data-year-entry');
            const academicgroupId = button.getAttribute('data-academicgroup-id');
            const departmentId = button.getAttribute('data-department-id');
            const classScheduleIds = button.getAttribute('data-class-schedule-ids');
            const programIds = button.getAttribute('data-program-ids');
            const deposit = button.getAttribute('data-deposit');

            console.log('Edit Modal Data:', {
                feeIds, feeName, feeTypesId, rate, arAccount, glAccount,
                studentstatusId, yearLevel, yearEntry, academicgroupId,
                departmentId, classScheduleIds, programIds, deposit
            });

            // Populate form fields
            document.getElementById('edit_fee_ids').value = feeIds;
            
            if (feeName) document.getElementById('edit_fee_name').value = feeName;
            if (feeTypesId) document.getElementById('edit_fee_types_id').value = feeTypesId;
            if (rate) document.getElementById('edit_rate').value = rate;
            if (deposit) document.getElementById('edit_deposit').value = deposit;
            if (academicgroupId) {
                document.getElementById('academicgroup_id').value = academicgroupId;
                document.getElementById('edit_academicgroup_id').value = academicgroupId;
            }
            if (departmentId) {
                document.getElementById('department_id').value = departmentId;
                document.getElementById('edit_department_id').value = departmentId;
            }
            if (studentstatusId) document.getElementById('edit_studentstatus_id').value = studentstatusId;
            if (yearLevel) document.getElementById('edit_year_level').value = yearLevel;
            if (yearEntry) document.getElementById('edit_year_entry').value = yearEntry;

            // Set AR account
            if (arAccount) {
                const arSelect = document.getElementById('edit_ar_account');
                arSelect.value = arAccount;
                
                if (arSelect.value !== arAccount) {
                    const tempOption = new Option(`${arAccount} (Current)`, arAccount, true, true);
                    arSelect.add(tempOption);
                }
            }

            // Set GL account
            if (glAccount) {
                const glSelect = document.getElementById('edit_gl_account');
                glSelect.value = glAccount;
                
                if (glSelect.value !== glAccount) {
                    const tempOption = new Option(`${glAccount} (Current)`, glAccount, true, true);
                    glSelect.add(tempOption);
                }
            }

            // Clear all checkboxes first
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
            allCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Set class schedule checkboxes
            if (classScheduleIds) {
                const selectedClassIds = classScheduleIds.split(',');
                selectedClassIds.forEach(classId => {
                    const trimmedId = classId.trim();
                    const checkbox = document.getElementById('class_' + trimmedId);
                    if (checkbox) checkbox.checked = true;
                });
            }

            // Set program checkboxes
            if (programIds) {
                const selectedProgramIds = programIds.split(',');
                selectedProgramIds.forEach(programId => {
                    const trimmedId = programId.trim();
                    const checkbox = document.getElementById('program_' + trimmedId);
                    if (checkbox) checkbox.checked = true;
                });
            }
        });

        // Reset form when modal is hidden
        editModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('editFeeForm').reset();
            // Remove any temporary options
            const tempOptions = document.querySelectorAll('option[value*="(Current)"]');
            tempOptions.forEach(option => option.remove());
        });
    }

    // Form validation
    const editForm = document.getElementById('editFeeForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const selectedFeesnameId = document.querySelector('input[name="selected_feesname_id"]').value;
            
            // For fee type 4, validate at least one class schedule is selected
            if (selectedFeesnameId === '4') {
                const classCheckboxes = document.querySelectorAll('input[name="class_schedule_ids[]"]:checked');
                if (classCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one subject');
                    return false;
                }
            }
            
            // For fee type 3, validate at least one class schedule or program is selected
            if (selectedFeesnameId === '3') {
                const classCheckboxes = document.querySelectorAll('input[name="class_schedule_ids[]"]:checked');
                const programCheckboxes = document.querySelectorAll('input[name="program_ids[]"]:checked');
                if (classCheckboxes.length === 0 && programCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one class schedule or program');
                    return false;
                }
            }
        });
    }
});
</script>