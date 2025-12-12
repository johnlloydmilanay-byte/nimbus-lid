<form method="POST" action="{{ route('accounting.feesmanagement.store.post') }}">
    @csrf

    <input type="hidden" name="year" value="{{ request('year') }}">
    <input type="hidden" name="term" value="{{ request('term') }}">
    <input type="hidden" name="academicgroup_id" value="{{ request('academicgroup_id') }}">

    <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addFeeModalLabel">
            <i class="fa fa-plus"></i> Add New {{ $selected_feesname->name }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        @if(isset($selected_feesname) && in_array($selected_feesname->id, [2,3]))
            <!-- Fee Name & Type -->
            <div class="mb-3">
                <label class="fw-bold">Fee Name</label>
                <input type="text" name="fee_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Fee Type</label>
                <select name="fee_types_id" class="form-select" required>
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
                                <input class="form-check-input" type="checkbox" name="class_schedule_ids[]" value="{{ $sched->id }}" id="class_{{ $sched->id }}">
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
            <div class="col-12 {{ $selected_feesname->id == 4 ? 'col-md-6 mb-3' : 'col-md-4' }}">
                <label class="fw-bold">Rate</label>
                <input type="number" step="0.01" name="rate" class="form-control" required>
            </div>
            @if(isset($selected_feesname) && $selected_feesname->id == 4)
                <div class="col-md-6">
                    <label class="fw-bold">Deposit</label>
                    <input type="number" step="0.01" name="deposit" class="form-control" required>
                </div>
            @endif
            <div class="col-12 {{ $selected_feesname->id == 4 ? 'col-md-6' : 'col-md-4' }}">
                <label class="fw-bold">School Year</label>
                <input type="text" class="form-control" value="{{ $year }}-{{ $year+1 }}" disabled>
            </div>
            <div class="col-12 {{ $selected_feesname->id == 4 ? 'col-md-6' : 'col-md-4' }}">
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
                <label class="form-label fw-semibold">{{ $selected_feesname->id == 2 ? 'GL Account' : 'AR Account' }}</label>

                @if(isset($selected_feesname) && $selected_feesname->id == 2)
                    <option value=""></option>
                    <select name="gl_account" class="form-select" required>
                        @foreach($accounts as $a)
                            <option value="{{ $a->accountcode }}">
                                {{ $a->accountcode }} : {{ $a->accountname }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <select name="ar_account" class="form-select" required>
                        <option value=""></option>
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
                            <option value="{{ $acad->id }}" {{ request('academicgroup_id') == $acad->id ? 'selected' : '' }}>
                                {{ $acad->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold">Department</label>
                    <select id="department_id" name="department_id" class="form-select">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" data-acad="{{ $dept->academicgroup_id }}">
                                {{ $dept->code }} : {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        @if(isset($selected_feesname) && in_array($selected_feesname->id, [3]))
            <!-- Class Schedules Checkboxes -->
            <div class="mb-3">
                <label class="fw-bold">Class Schedules</label>
                <div id="class-checkboxes" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                    @foreach($classschedule as $sched)
                        @if($sched->subject)
                            <div class="form-check class-item" data-dept="{{ $sched->department_id ?? '' }}">
                                <input class="form-check-input" type="checkbox" name="class_schedule_ids[]" value="{{ $sched->id }}" id="class_{{ $sched->id }}">
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
                <label class="fw-bold">Programs</label>
                <div id="program-checkboxes" class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                    @foreach($programs as $program)
                        <div class="form-check program-item" data-dept="{{ $program->department_id }}">
                            <input class="form-check-input" type="checkbox" name="program_ids[]" value="{{ $program->id }}" id="program_{{ $program->id }}">
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
                    <select name="studentstatus_id" class="form-select">
                        <option value=""></option>
                        @foreach($studentstatus as $studentstat)
                            <option value="{{ $studentstat->id }}">{{ $studentstat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 {{ $selected_feesname->id == 2 ? 'col-md-4' : 'col-md-6' }}">
                    <label class="fw-bold">Year Level / Grade</label>
                    <select name="year_level" class="form-select">
                        <option value=""></option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                @if(isset($selected_feesname) && in_array($selected_feesname->id, [2]))
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Year Entry</label>
                        <input type="text" name="year_entry" class="form-control">
                    </div>
                @endif
            </div>
        @endif

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-custom fw-bold" type="submit">Save {{ $selected_feesname->name }}</button>
    </div>
</form>
