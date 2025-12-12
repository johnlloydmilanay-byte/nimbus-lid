@extends('layouts.master')

@section('content')
<div class="col-12">

    <h3 class="mt-4 fw-bold mb-4">Fees Management</h3>

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

    <!-- Manage Fees -->
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold"><i class="fa fa-folder-open"></i> Manage Fees</div>
        <div class="card-body">
            <form method="GET" id="feestypeForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-2"><label class="form-label fw-semibold">Manage Fees</label></div>
                    <div class="col-md-10">
                        <select class="form-select" name="feesname" id="feesname" required>
                            <option value="">-- Select Fee Type --</option>
                            @foreach($feesname as $type)
                                <option value="{{ $type->id }}" 
                                    {{ (isset($selected_feesname) && $selected_feesname->id == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fee Details -->
    @if($selected_feesname)
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold"><i class="fa fa-info-circle"></i> Fee Details: {{ $selected_feesname->name }}</div>
            <div class="card-body">
                <form method="GET" action="{{ route('accounting.feesmanagement.index') }}">
                    <input type="hidden" name="feesname" value="{{ $selected_feesname->id }}">

                    <div class="row g-2 align-items-center mt-1">
                        <div class="col-12 col-md-2">
                            <label class="form-label fw-semibold">{{ $selected_feesname->id == 1 ? 'School Year' : 'School Year / Term' }}</label>
                        </div>
                        <div class="col-12 {{ $selected_feesname->id == 1 ? 'col-md-10' : 'col-md-5' }}">
                            <select class="form-select" name="year" id="year" required>
                                {!! App\UserClass\Tool::year_generator(5, 0, request('year')) !!}
                            </select>
                        </div>
                        @if(!in_array($selected_feesname->id, [1]))
                            <div class="col-12 col-md-5">
                                <select class="form-select" name="term" required>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ request('term') == $term->id ? 'selected' : '' }}>
                                            {{ $term->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    @if(!in_array($selected_feesname->id, [2,3,4,5]))
                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Department</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <select class="form-select" id="dept" name="department_id" required>
                                    <option value="">-- Select Department --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->code }} : {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    @if(!in_array($selected_feesname->id, [1,4,5]))
                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Academic Group</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <select class="form-select" id="academicgroup_id" name="academicgroup_id" required>
                                    <option value="">-- Select Academic Group --</option>
                                    @foreach($academicgroup as $acad)
                                        <option value="{{ $acad->id }}" {{ request('academicgroup_id') == $acad->id ? 'selected' : '' }}>
                                            {{ $acad->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success fw-bold">Manage {{ $selected_feesname->name }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE CONTENTS -->
        @if(request()->filled(['year']) && (request()->filled('department_id') || request()->filled('academicgroup_id') || request()->filled('term')))
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    <i class="fa fa-folder-open"></i>
                    {{ $selected_feesname->name }}
                    @if(request('year')) : SY {{ request('year') }}-{{ request('year') + 1 }} @endif
                    @if(request('term')) {{ $terms->firstWhere('id', request('term'))->name ?? 'N/A' }} @endif
                    @if(request('department_id')) {{ $departments->firstWhere('id', request('department_id'))->code ?? 'N/A' }} @endif
                    @if(request('academicgroup_id')) {{ $academicgroup->firstWhere('id', request('academicgroup_id'))->name ?? 'N/A' }} @endif
                    <div class="btn-group float-end" role="group">
                        @include('Accounting.FeesManagement.import') <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importFeeModal"><i class="bi bi-box-arrow-in-down"></i> Import {{ $selected_feesname->name }}</button>
                        @include('Accounting.FeesManagement.add') <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFeeModal"><i class="bi bi-plus-lg"></i> Add New {{ $selected_feesname->name }}</button>
                    </div>
                </div>

                <div class="card-body table-responsive">
                    @if(isset($selected_feesname) && in_array($selected_feesname->id, [1]))
                        @include('Accounting.FeesManagement.Tuition.list') 
                    @else
                        @include('Accounting.FeesManagement.Other.list') 
                    @endif
                </div>


            </div>
        @endif

    @endif
</div>

<script>
    document.getElementById('feesname').addEventListener('change', function() {
        const id = this.value;
        if(id) {
            window.location.href = "{{ route('accounting.feesmanagement.index') }}" + "?feesname=" + id;
        }
    });
</script>
@endsection
