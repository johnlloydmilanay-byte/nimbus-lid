<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('enrollment.curriculum.manageStore') }}" method="POST">
                @csrf

                <input type="hidden" name="curriculum_year_id" value="{{ $curriculumyears->first()->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addSubjectModalLabel">
                        <i class="bi bi-plus-circle"></i> Add New Subject
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">Subject</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subject as $subj)
                                    <option value="{{ $subj->id }}">
                                        {{ $subj->code }} : {{ $subj->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold">Term</label>
                            <select name="term_id" class="form-select" required>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ request('term') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Grade/Year Level</label>
                            <select name="year_level_id" class="form-select" required>
                                <option value=""></option>
                                @foreach($yearLevelDetails as $yl)
                                    <option value="{{ $yl->id }}">{{ $yl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">Required Subject/s</label>
                            <select name="required_subject_id[]" class="form-select" multiple>
                                <option value=""></option>
                                @foreach($prerequisite as $prereq)
                                    <option value="{{ $prereq->id }}">{{ $prereq->subject->code }} : {{ $prereq->subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-custom fw-bold">Save Curriculum</button>
                </div>
            </form>
        </div>
    </div>
</div>