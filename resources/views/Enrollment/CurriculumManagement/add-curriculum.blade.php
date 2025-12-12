<div class="modal fade" id="addCurriculumModal" tabindex="-1" aria-labelledby="addCurriculumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('enrollment.curriculum.store') }}" method="POST">
                @csrf

                <input type="hidden" name="year" value="{{ request('year') }}">
                <input type="hidden" name="term_id" value="{{ request('term') }}">
                <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                <input type="hidden" name="program_id" value="{{ request('program_id') }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addCurriculumModalLabel">
                        <i class="bi bi-plus-circle"></i> Add New Curriculum Year
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">Department</label>
                            <select id="department_id" name="department_id" class="form-select" disabled>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->code }} : {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">Programs</label>
                            <select id="program_id" name="program_id" class="form-select" disabled>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                                        {{ $prog->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">Curriculum Year</label>
                            <select class="form-select" name="curriculum_year" id="curriculum_year" required>
                                {!! App\UserClass\Tool::year_generator(5, 0, request('year')) !!}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-custom fw-bold">Add Curriculum Year</button>
                </div>
            </form>
        </div>
    </div>
</div>