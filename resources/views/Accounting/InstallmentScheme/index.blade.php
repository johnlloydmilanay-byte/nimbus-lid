@extends('layouts.master')

@section('content')
<div class="col-12">

    @if(session('message'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: '{{ session("message_type") ?? "success" }}',
            title: '{{ session("message") }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            width: '400px',
        });
    </script>
    @endif

    <h3 class="mt-4 fw-bold mb-4">{{ $moduleName }}</h3>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold"><i class="fa fa-info-circle"></i> Filter Options</div>
        <div class="card-body">
            <form method="GET" id="feestypeForm">
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-12 col-md-2"><label class="form-label fw-semibold">School Year / Term</label></div>
                    <div class="col-12 col-md-5">
                        <select class="form-select" name="year" id="year" required>
                            {!! App\UserClass\Tool::year_generator(5, 0, request('year')) !!}
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <select class="form-select" name="term_id" required>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-12 col-md-2"><label class="form-label fw-semibold">Academic Group</label></div>
                    <div class="col-12 col-md-10">
                        <select class="form-select" id="academicgroup_id" name="academicgroup_id" required>
                            @foreach($academicgroup as $acad)
                                <option value="{{ $acad->id }}" {{ request('academicgroup_id') == $acad->id ? 'selected' : '' }}>
                                    {{ $acad->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-custom fw-bold">Get {{ $title }}</button>
                </div>
            </form>
        </div>
    </div>

    @if(request()->filled(['year']) && request()->filled('term_id') && request()->filled('academicgroup_id'))
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">
            <i class="fa fa-folder-open"></i>
            {{ $title }} :
            @if(request('academicgroup_id')) {{ $academicgroup->firstWhere('id', request('academicgroup_id'))->name ?? '' }} @endif |
            @if(request('year')) SY {{ request('year') }}-{{ request('year') + 1 }} @endif |
            @if(request('term_id')) {{ $terms->firstWhere('id', request('term_id'))->name ?? '' }} @endif
            <div class="btn-group float-end" role="group">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importInstallmentSchemeModal"><i class="bi bi-box-arrow-in-down"></i> Import {{ $title }}</button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addInstallmentSchemeModal"><i class="bi bi-plus-lg"></i> Add New {{ $title }}</button>
                @include('Accounting.InstallmentScheme.add')
            </div>
        </div>

        <div class="card-body table-responsive">
            @if($schemes->count() > 0)
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="25%">Scheme Name</th>
                            <th width="15%">Payment Count</th>
                            <th width="15%">Installment Fee</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schemes as $s)
                            <tr>
                                <td>{{ $s->scheme_name }}</td>
                                <td class="text-center">{{ $s->payment_count }}</td>
                                <td class="text-center">Php {{ number_format($s->installment_fee, 2) }}</td>
                                <td class="text-center">
                                    <div class="btn-group d-flex">
                                        <a href="{{ request()->fullUrlWithQuery(['installment_scheme_id' => $s->id]) }}"class="btn btn-secondary btn-sm flex-fill">Manage</a>
                                        <button type="button" class="btn btn-success btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#editInstallmentSchemeModal{{ $s->id }}">Edit</button>
                                        <button class="btn btn-danger btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $s->id }}">Delete</button>
                                    </div>
                                </td>
                                @include('Accounting.InstallmentScheme.edit', ['s' => $s])
                                @include('Template.delete', [
                                    'id' => $s->id,
                                    'route' => route('accounting.installmentscheme.destroy', $s->id),
                                    'title' => 'Are you sure?',
                                    'message' => "Do you really want to delete these records? This process cannot be undone."
                                ])
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning text-center mb-0">
                    No {{ $selected_feesname->name ?? 'Tuition Fees' }} found for the selected parameters.
                </div>
            @endif

        </div>
    </div>
    @endif

    @if($manageScheme)
    <div class="card shadow-sm mt-4">
        <div class="card-header fw-bold">
            <i class="fa fa-folder-open"></i>
            {{ $manageTitle }} :
            {{ $manageScheme->academicgroup->name }} |
            SY {{ $manageScheme->year }}-{{ $manageScheme->year + 1 }} |
            {{ $terms->firstWhere('id', $manageScheme->term_id)->name }}
        </div>

        <form action="{{ route('accounting.installmentscheme.save_manage', $manageScheme->id) }}" method="POST">
            @csrf     

            <input type="hidden" name="installment_scheme_id" value="{{ $manageScheme->id }}">

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th width="20%">Description</th>
                            <th width="10%">Date From</th>
                            <th width="10%">Date To</th>
                            <th width="10%">Period Start</th>
                            <th width="10%">Period End</th>
                            <th>Exam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= $manageScheme->payment_count; $i++)
                            @php
                                $schedule = $schedules->firstWhere('order', $i);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td><input type="text" class="form-control" name="description[{{ $i }}]" value="{{ $schedule->description ?? ($i == 1 ? 'Due upon enrollment' : '') }}" @if($i == 1) readonly @endif></td>
                                <td><input type="date" class="datepicker form-control" name="date_from[{{ $i }}]" value="{{ $schedule && $schedule->date_from ? \Carbon\Carbon::parse($schedule->date_from)->format('Y-m-d') : now()->format('Y-m-d') }}"></td>
                                <td><input type="date" class="datepicker form-control" name="date_to[{{ $i }}]" value="{{ $schedule && $schedule->date_to ? \Carbon\Carbon::parse($schedule->date_to)->format('Y-m-d') : now()->format('Y-m-d') }}"></td>
                                <td><input type="date" class="datepicker form-control" name="period_start[{{ $i }}]" value="{{ $schedule && $schedule->period_start ? \Carbon\Carbon::parse($schedule->period_start)->format('Y-m-d') : '' }}"></td>
                                <td><input type="date" class="datepicker form-control" name="period_end[{{ $i }}]" value="{{ $schedule && $schedule->period_end ? \Carbon\Carbon::parse($schedule->period_end)->format('Y-m-d') : '' }}"></td>
                                <td><input type="text" class="form-control" name="exam[{{ $i }}]" value="{{ $schedule->exam ?? '' }}"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">Save {{ $manageTitle }}</button>
                </div>
            </div>
        </form>
    </div>
    @endif
          
    @if($manageScheme && $rawSchedules->count() > 0)
    <div class="card shadow-sm mt-4">
        <div class="card-header fw-bold">
            <i class="fa fa-folder-open"></i>
            {{ $manageTitle2 }} :
            {{ $manageScheme->academicgroup->name }} |
            SY {{ $manageScheme->year }}-{{ $manageScheme->year + 1 }} |
            {{ $terms->firstWhere('id', $manageScheme->term_id)->name }}
        </div>

        <form action="{{ route('accounting.installmentscheme.store_fees', $manageScheme->id) }}" method="POST">
            @csrf

            <div class="card-body table-responsive">
                <table class="table table-condensed no-margin breakdown">
                    <thead class="text-center">
                        <tr>
                            <th rowspan="2" class="pc-col">Fee Name</th>
                            <th colspan="{{ $manageScheme->payment_count }}">Payment Count</th>
                        </tr>
                        <tr>
                            @for ($i = 1; $i <= $manageScheme->payment_count; $i++)
                                <th class="pc-col">{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>

                        <!-- Main Fees -->
                        @foreach ($mainFeeDetails as $fee)
                        <tr>
                            <td class="fw-semibold">{{ $fee['name'] }}</td>
                            @foreach ($fee['rates'] as $rate)
                            <td class="pc-col align-middle">
                                <div class="input-group input-group-sm pc-input w-50 mx-auto">
                                    <input type="number" name="rates[{{ $fee['id'] }}][]" class="form-control text-end p-1" min="0" max="100" step="1" value="{{ $rate }}">
                                    <span class="input-group-text p-1">%</span>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        <!-- Miscellaneous Fees -->
                        <tr><td class="text-center" colspan="{{ $manageScheme->payment_count + 1 }}"><strong>Miscellaneous Fees</strong></td></tr>
                        @foreach ($miscFeeDetails as $misc)
                        <tr>
                            <td>{{ $misc['name'] }}</td>

                            @foreach ($misc['rates'] as $rate)
                            <td class="pc-col align-middle">
                                <div class="input-group input-group-sm pc-input w-50 mx-auto">
                                    <input type="number" name="rates[{{ $misc['id'] }}][]" class="form-control text-end p-1" min="0" max="100" step="1" value="{{ $rate }}">
                                    <span class="input-group-text p-1">%</span>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        <!-- Other Fees -->
                        <tr><td class="text-center" colspan="{{ $manageScheme->payment_count + 1 }}"><strong>Other Fees</strong></td></tr>
                        @foreach ($otherFeeDetails as $other)
                        <tr>
                            <td>{{ $other['name'] }}</td>

                            @foreach ($other['rates'] as $rate)
                            <td class="pc-col align-middle">
                                <div class="input-group input-group-sm pc-input w-50 mx-auto">
                                    <input type="number" name="rates[{{ $other['id'] }}][]" class="form-control text-end p-1" min="0" max="100" step="1" value="{{ $rate }}">
                                    <span class="input-group-text p-1">%</span>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">Save {{ $manageTitle2 }}</button>
                </div>
            </div>
        </form>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Select all percentage inputs inside the fees table
    const percentageInputs = document.querySelectorAll('input[name^="rates"]');

    percentageInputs.forEach(input => {
        // Enforce min/max while typing
        input.addEventListener('input', function () {
            let value = parseInt(this.value);

            if (isNaN(value)) {
                this.value = '';
                return;
            }

            if (value < 0) this.value = 0;
            if (value > 100) this.value = 100;
        });

        // Optional: prevent non-numeric characters
        input.addEventListener('keypress', function (e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9]/.test(char)) {
                e.preventDefault();
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const percentageRows = document.querySelectorAll('tbody tr');

    percentageRows.forEach(row => {
        const inputs = row.querySelectorAll('input[type="number"]');

        inputs.forEach(input => {
            input.addEventListener('input', function () {
                let total = 0;

                inputs.forEach(i => {
                    total += parseInt(i.value) || 0;
                });

                if (total > 100) {
                    // Show notification
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Oops! Total percentage for this fee cannot exceed 100%.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    // Adjust value to keep total 100%
                    this.value = 100 - (total - (parseInt(this.value) || 0));
                }

                // Keep value within 0-100
                if (parseInt(this.value) < 0) this.value = 0;
                if (parseInt(this.value) > 100) this.value = 100;
            });
        });
    });
});
</script>


@endsection
