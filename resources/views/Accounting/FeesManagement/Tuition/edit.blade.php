<div class="modal fade" id="editFeeModal" tabindex="-1" aria-labelledby="editFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('accounting.feesmanagement.update.tuition') }}" id="editTuitionForm">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="tuition_ids" id="edit_tuition_ids">
                <input type="hidden" name="year" value="{{ request('year') }}">
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editFeeModalLabel">
                        <i class="fa fa-edit"></i> Edit Tuition Fee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Program Type Filter -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="fw-bold">Program Type</label>
                            <select id="edit_program_type_filter" class="form-select">
                                <option value="">All</option>
                                <option value="1">Board Course</option>
                                <option value="0">Non-Board Course</option>
                            </select>
                        </div>
                    </div>

                    <!-- Programs Checkboxes -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">Programs</label>
                            <div id="edit_program-checkboxes" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                @foreach($tuitionPrograms as $program)
                                    <div class="form-check edit-program-item" data-board="{{ $program->is_board_course }}">
                                        <input class="form-check-input" type="checkbox" name="programs[]" value="{{ $program->id }}" id="edit_program_{{ $program->id }}">
                                        <label class="form-check-label" for="edit_program_{{ $program->id }}">
                                            {{ $program->dcode }} : {{ $program->code }} - {{ $program->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Year - Setup Type - AR Account -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold">School Year</label>
                            <input type="text" class="form-control" value="{{ $year }}-{{ $year+1 }}" disabled>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold">Setup Type</label>
                            <select class="form-select" name="setup_type" id="edit_setup_type" required>
                                <option value="1">Per Unit</option>
                                <option value="0">Fixed Amount</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">AR Account</label>
                            <select name="ar_account" class="form-select" id="edit_ar_account" required>
                                @foreach($accounts as $a)
                                    <option value="{{ $a->accountcode }}">
                                        {{ $a->accountcode }} : {{ $a->accountname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Rates & GL Account -->
                    <div class="card mt-3">
                        <div class="card-header bg-light fw-bold text-center">
                            Rates & GL Account (Apply to specific Year Levels or All)
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 align-items-center mb-3">
                                <div class="col-md-3 text-center">
                                    <label class="fw-bold">Regular Rate</label>
                                    <input type="number" class="form-control" name="rate_regular" id="edit_rate_regular" required step="0.01" min="0">
                                </div>
                                <div class="col-md-3 text-center">
                                    <label class="fw-bold">Major Rate</label>
                                    <input type="number" class="form-control" name="rate_major" id="edit_rate_major" required step="0.01" min="0">
                                </div>
                                <div class="col-md-6 text-center">
                                    <label class="fw-bold">GL Account</label>
                                    <select class="form-select" name="gl_account" id="edit_gl_account" required>
                                        <option value="">Select GL Account</option>
                                        @foreach($glAccounts as $a)
                                            <option value="{{ $a->accountcode }}">
                                                {{ $a->accountcode }} : {{ $a->accountname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-custom fw-bold" type="submit">Update Tuition Fee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Edit Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editFeeModal');
    
    // Program type filter functionality - exactly like add modal
    const programTypeFilter = document.getElementById('edit_program_type_filter');
    if (programTypeFilter) {
        programTypeFilter.addEventListener('change', function() {
            const filterValue = this.value;
            const programItems = document.querySelectorAll('.edit-program-item');
            
            programItems.forEach(function(item) {
                if (filterValue === '' || item.dataset.board === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            // Get data attributes
            const tuitionIds = button.getAttribute('data-id');
            const setupType = button.getAttribute('data-setup-type');
            const rateRegular = button.getAttribute('data-rate-regular');
            const rateMajor = button.getAttribute('data-rate-major');
            const arAccount = button.getAttribute('data-ar-account');
            const glAccount = button.getAttribute('data-gl-account');
            const programIds = button.getAttribute('data-program-ids');
            
            console.log('Edit Modal Data:', {
                tuitionIds,
                setupType,
                rateRegular,
                rateMajor,
                arAccount,
                glAccount,
                programIds
            });
            
            // Populate form fields
            document.getElementById('edit_tuition_ids').value = tuitionIds;
            document.getElementById('edit_setup_type').value = setupType;
            document.getElementById('edit_rate_regular').value = rateRegular;
            document.getElementById('edit_rate_major').value = rateMajor;
            
            // Set AR account
            if (arAccount) {
                const arSelect = document.getElementById('edit_ar_account');
                arSelect.value = arAccount;
                
                // If value not found in options, add it temporarily
                if (arSelect.value !== arAccount) {
                    console.warn(`AR Account "${arAccount}" not found, adding temporary option`);
                    const tempOption = new Option(`${arAccount} (Current)`, arAccount, true, true);
                    arSelect.add(tempOption);
                }
            }
            
            // Set GL account - FIXED
            if (glAccount) {
                const glSelect = document.getElementById('edit_gl_account');
                console.log('Setting GL Account to:', glAccount);
                console.log('Available GL Options:', Array.from(glSelect.options).map(opt => opt.value));
                
                // First try to set the value directly
                glSelect.value = glAccount;
                
                // If value not set, try to find and select the option
                if (glSelect.value !== glAccount) {
                    console.warn(`GL Account "${glAccount}" not found in initial options, searching...`);
                    
                    // Try to find the option
                    const glOption = Array.from(glSelect.options).find(option => option.value === glAccount);
                    if (glOption) {
                        glOption.selected = true;
                        console.log('GL Account found and selected');
                    } else {
                        console.warn(`GL Account "${glAccount}" not found in dropdown, adding temporary option`);
                        // Add temporary option to show current value
                        const tempOption = new Option(`${glAccount} (Current Value)`, glAccount, true, true);
                        glSelect.add(tempOption);
                    }
                } else {
                    console.log('GL Account successfully set to:', glAccount);
                }
            } else {
                console.warn('No GL Account data provided');
            }
            
            // Clear all program checkboxes first
            const programCheckboxes = document.querySelectorAll('#edit_program-checkboxes input[type="checkbox"]');
            programCheckboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
            
            // Check the programs that are currently selected
            if (programIds) {
                const selectedProgramIds = programIds.split(',');
                console.log('Selected Program IDs:', selectedProgramIds);
                
                selectedProgramIds.forEach(function(programId) {
                    const trimmedId = programId.trim();
                    const checkbox = document.getElementById('edit_program_' + trimmedId);
                    if (checkbox) {
                        checkbox.checked = true;
                    } else {
                        console.warn(`Program ID "${trimmedId}" not found in checkboxes`);
                    }
                });
            }
            
            // Reset program type filter
            document.getElementById('edit_program_type_filter').value = '';
            const programItems = document.querySelectorAll('.edit-program-item');
            programItems.forEach(function(item) {
                item.style.display = 'block';
            });
        });
        
        // Reset form when modal is hidden
        editModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('editTuitionForm').reset();
            // Remove any temporary options that were added
            const tempOptions = document.querySelectorAll('option[value*="(Current)"]');
            tempOptions.forEach(option => option.remove());
        });
    }
    
    // Form validation
    const editForm = document.getElementById('editTuitionForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            // Verify at least one program is selected
            const checkedPrograms = document.querySelectorAll('#edit_program-checkboxes input[type="checkbox"]:checked');
            if (checkedPrograms.length === 0) {
                e.preventDefault();
                alert('Please select at least one program');
                return false;
            }
        });
    }
});
</script>