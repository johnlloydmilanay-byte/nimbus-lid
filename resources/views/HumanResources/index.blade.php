@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1 class="mt-4 fw-bold">Employee Management</h1>
        <a href="{{ route('hr.create') }}" class="btn btn-custom">
            <i class="bi-person-plus-fill me-1"></i> Add Employee
        </a>
    </div>

    <!-- Statistic Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card custom-card card-1 h-100">
                <div class="card-body">
                    <i class="bi-clipboard-check-fill fs-1 mb-2"></i>
                    <span class="label">Total Employees</span>
                    <h2 id="totalApplicants">{{ $totalEmployee }}</h2>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection