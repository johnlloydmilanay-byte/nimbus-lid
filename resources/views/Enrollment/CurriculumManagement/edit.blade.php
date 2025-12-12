<div class="modal fade" id="editCurriculumModal-{{ $curr->id }}" tabindex="-1" aria-labelledby="editCurriculumModalLabel-{{ $curr->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('enrollment.curriculum.update') }}" method="POST">
                @csrf

                <input type="hidden" name="id" value="{{ $curr->id }}">
                <input type="hidden" name="department_id" value="{{ $curr->department_id }}">
                <input type="hidden" name="program_id" value="{{ $curr->program_id }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editCurriculumModalLabel-{{ $curr->id }}">
                        <i class="bi bi-pencil-square"></i> Edit Curriculum
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold">Department</label>
                        <select class="form-select" disabled>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->code }} : {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Program</label>
                        <select class="form-select" disabled>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Curriculum Year</label>
                        <select class="form-select" name="curriculum_year" required>
                            {!! App\UserClass\Tool::year_generator(5, 0, request('curriculum_year')) !!}
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-custom fw-bold">Update Curriculum</button>
                </div>

            </form>
        </div>
    </div>
</div>
