<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('enrollment.subjectmanager.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addSubjectModalLabel">
                        <i class="bi bi-plus-circle"></i> Add New Subject
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Description</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="units" class="form-label">Units</label>
                            <input type="number" class="form-control" id="units" name="units" min="0" value="3" required>
                        </div>
                        <div class="col-md-3">
                            <label for="clock_hours" class="form-label">Clock Hours</label>
                            <input type="number" class="form-control" id="clock_hours" name="clock_hours" min="0" value="3" required>
                        </div>
                        <div class="col-md-3">
                            <label for="is_lab" class="form-label">Laboratory Subject</label>
                            <select name="is_lab" id="is_lab" class="form-select" required>
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="lab_type" class="form-label">Laboratory Type</label>
                            <select name="lab_type" id="lab_type" class="form-select">
                                <option value="0" selected></option>
                                <option value="1">ITC</option>
                                <option value="2">LID</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="is_seminar" class="form-label">Is Seminar Type</label>
                            <select name="is_seminar" id="is_seminar" class="form-select" required>
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="has_conflicts" class="form-label">Allow Conflicts</label>
                            <select name="has_conflicts" id="has_conflicts" class="form-select" required>
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="has_energy" class="form-label">Has Charge Energy Fee</label>
                            <select name="has_energy" id="has_energy" class="form-select" required>
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="is_graded" class="form-label">Is Graded</label>
                            <select name="is_graded" id="is_graded" class="form-select" required>
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="is_evaluated" class="form-label">Is Evaluated</label>
                            <select name="is_evaluated" id="is_evaluated" class="form-select" required>
                                <option value="0">No</option>
                                <option value="1" selected>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="is_major" class="form-label">Is Major</label>
                            <select name="is_major" id="is_major" class="form-select" required>
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 major-field" style="display:none;">
                            <label class="fw-bold">Department</label>
                            <select id="dept" name="department_id" class="form-select" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->code }} : {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 major-field" style="display:none;">
                            <label class="fw-bold">Programs</label>
                            <select class="form-select" id="prog" name="program_id" required>
                                <option value="">-- Select Program --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-custom">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isMajorSelect = document.getElementById('is_major');
    const majorFields = document.querySelectorAll('.major-field');

    function toggleMajorFields() {
        if (isMajorSelect.value === '1') {
            majorFields.forEach(el => el.style.display = 'block');
        } else {
            majorFields.forEach(el => el.style.display = 'none');
        }
    }

    toggleMajorFields();

    isMajorSelect.addEventListener('change', toggleMajorFields);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isMajorSelect = document.getElementById('is_major');
    const majorFields = document.querySelectorAll('.major-field select');

    function toggleMajorFields() {
        if (isMajorSelect.value === '1') {
            majorFields.forEach(select => {
                select.parentElement.style.display = 'block';
                select.setAttribute('required', 'required');
            });
        } else {
            majorFields.forEach(select => {
                select.parentElement.style.display = 'none';
                select.removeAttribute('required');
            });
        }
    }

    toggleMajorFields();
    
    isMajorSelect.addEventListener('change', toggleMajorFields);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isLabSelect = document.getElementById('is_lab');
    const labTypeSelect = document.getElementById('lab_type');
    const hasEnergySelect = document.getElementById('has_energy');

    function toggleLabFields() {
        if (isLabSelect.value === '1') {
            hasEnergySelect.value = '1';
            labTypeSelect.setAttribute('required', 'required');
        } else {
            hasEnergySelect.value = '0';
            labTypeSelect.removeAttribute('required');
        }
    }

    toggleLabFields();

    isLabSelect.addEventListener('change', toggleLabFields);
});
</script>
