<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal-{{ $subj->id }}" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('enrollment.subjectmanager.update', $subj->id) }}" method="POST">
                @csrf
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square"></i> Edit Subject
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Subject Code</label>
                            <input type="text" class="form-control" name="code" value="{{ $subj->code }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="name" value="{{ $subj->name }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Units</label>
                            <input type="number" class="form-control" name="units" min="0" value="{{ $subj->units }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Clock Hours</label>
                            <input type="number" class="form-control" name="clock_hours" min="0" value="{{ $subj->clock_hours }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Laboratory Subject</label>
                            <select name="is_lab" id="is_lab_{{ $subj->id }}" class="form-select">
                                <option value="0" {{ $subj->is_lab == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $subj->is_lab == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Laboratory Type</label>
                            <select name="lab_type" id="lab_type_{{ $subj->id }}" class="form-select">
                                <option value="0" {{ $subj->lab_type == 0 ? 'selected' : '' }}></option>
                                <option value="1" {{ $subj->lab_type == 1 ? 'selected' : '' }}>ITC</option>
                                <option value="2" {{ $subj->lab_type == 2 ? 'selected' : '' }}>LID</option>
                            </select>   
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Is Seminar</label>
                            <select name="is_seminar" class="form-select">
                                <option value="0" {{ $subj->is_seminar == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $subj->is_seminar == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allow Conflicts</label>
                            <select name="has_conflicts" class="form-select">
                                <option value="0" {{ $subj->has_conflicts == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $subj->has_conflicts == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Has Charge Energy Fee</label>
                            <select name="has_energy" id="has_energy_{{ $subj->id }}" class="form-select">
                                <option value="0" {{ $subj->has_energy == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $subj->has_energy == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Is Graded</label>
                            <select name="is_graded" class="form-select">
                                <option value="0" {{ $subj->is_graded == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $subj->is_graded == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Is Evaluated</label>
                            <select name="is_evaluated" class="form-select">
                                <option value="0" {{ $subj->is_evaluated == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $subj->is_evaluated == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Is Major</label>
                            <select name="is_major" id="is_major_{{ $subj->id }}" class="form-select">
                                <option value="0" {{ $subj->is_major == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $subj->is_major == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 major-field-{{ $subj->id }}" style="{{ $subj->is_major ? '' : 'display:none;' }}">
                            <label class="fw-bold">Department</label>
                            <select id="dept_{{ $subj->id }}" name="department_id" class="form-select">
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $dept->id == $subj->department_id ? 'selected' : '' }}>
                                        {{ $dept->code }} : {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 major-field-{{ $subj->id }}" style="{{ $subj->is_major ? '' : 'display:none;' }}">
                            <label class="fw-bold">Program</label>
                            <select id="prog_{{ $subj->id }}" name="program_id" class="form-select" data-selected="{{ $subj->program_id }}">
                                <option value="">-- Select Program --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-custom">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    @foreach($subjects as $subj)
        initEditScripts({{ $subj->id }});
    @endforeach
});
</script>

<script>
function initEditScripts(id) {
    const isLab = document.getElementById('is_lab_' + id);
    const labType = document.getElementById('lab_type_' + id);
    const hasEnergy = document.getElementById('has_energy_' + id);

    function labLogic() {
        if (isLab.value == "1") {
            hasEnergy.value = "1";
            labType.required = true;
        } else {
            hasEnergy.value = "0";
            labType.required = false;
            labType.value = "";
        }
    }
    labLogic();
    isLab.addEventListener('change', labLogic);

    const isMajor = document.getElementById('is_major_' + id);
    const majorFields = document.querySelectorAll('.major-field-' + id);

    function majorLogic() {
        if (isMajor.value == "1") {
            majorFields.forEach(e => e.style.display = 'block');
        } else {
            majorFields.forEach(e => e.style.display = 'none');
        }
    }

    majorLogic();
    isMajor.addEventListener('change', majorLogic);
}
</script>

