<form method="POST" action="{{ route('accounting.feesmanagement.store.tuition') }}">
    @csrf

    <input type="hidden" name="year" value="{{ request('year') }}">

    <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addFeeModalLabel">
            <i class="fa fa-plus"></i> Add New {{ $selected_feesname->name }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <!-- Program Type Filter -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="fw-bold">Program Type</label>
                <select id="program_type_filter" class="form-select">
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
                <div id="program-checkboxes" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                    @foreach($tuitionPrograms as $program)
                        <div class="form-check program-item" data-board="{{ $program->is_board_course }}">
                            <input class="form-check-input" type="checkbox" name="programs[]" value="{{ $program->id }}" id="program_{{ $program->id }}">
                            <label class="form-check-label" for="program_{{ $program->id }}">
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
                <select class="form-select" name="setup_type" required>
                    <option value="1" selected>Per Unit</option>
                    <option value="0">Fixed Amount</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">AR Account</label>
                <select name="ar_account" class="form-select" required>
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
                        <input type="number" class="form-control" name="rate_regular" value="" required>
                    </div>
                    <div class="col-md-3 text-center">
                        <label class="fw-bold">Major Rate</label>
                        <input type="number" class="form-control" name="rate_major" value="" required>
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="fw-bold">GL Account</label>
                        <select class="form-select" name="gl_account" required>
                            <option value="" selected></option>
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
        <button class="btn btn-custom fw-bold" type="submit">Save {{ $selected_feesname->name }}</button>
    </div>
</form>