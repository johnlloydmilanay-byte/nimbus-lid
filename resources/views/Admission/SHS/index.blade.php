@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1 class="mt-4 fw-bold">Senior High School Admission</h1>
        <a href="{{ route('admission.shs.create') }}" class="btn btn-custom">
            <i class="bi-person-plus-fill me-1"></i> Add Applicant
        </a>
    </div>

    <!-- Statistic Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card custom-card card-1 h-100">
                <div class="card-body">
                    <i class="bi-clipboard-check-fill fs-1 mb-2"></i>
                    <span class="label">Total Applicants</span>
                    <h2 id="totalApplicants">{{ $totalApplicants }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card custom-card card-5 h-100">
                <div class="card-body">
                    <i class="bi-people-fill fs-1 mb-2"></i>
                    <span class="label">Total Applicants This Semester</span>
                    <h2 id="totalApplicantsBySem">{{ $totalApplicantsBySem }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-dark stretched-link" href="{{ route('admission.shs.totalapplicants') }}">View Details</a>
                    <div class="small text-dark"><i class="bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card custom-card card-2 h-100">
                <div class="card-body">
                    <i class="bi-person-check-fill fs-1 mb-2"></i>
                    <span class="label">Total Enrolled this Semester</span>
                    <h2 id="totalEnrolledbySem">{{ $totalEnrolledbySem }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-dark stretched-link" href="{{ route('admission.shs.totalenrolled') }}">View Details</a>
                    <div class="small text-dark"><i class="bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card custom-card card-4 h-100">
                <div class="card-body">
                    <i class="bi-hourglass-split fs-1 mb-2"></i>
                    <span class="label">Unscheduled Paid Applicants</span>
                    <h2 id="totalUnscheduledPaidApplicants">{{ $totalUnscheduledPaidApplicants }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-dark stretched-link" href="{{ route('admission.shs.unsched') }}">View Details</a>
                    <div class="small text-dark"><i class="bi-arrow-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('admission.shs.index') }}" class="d-flex mb-3">
        <input type="text" name="search" value="{{ request('search') }}" 
               class="form-control me-2" placeholder="Search applicant by name or application no.">
        <button type="submit" class="btn btn-custom">Search</button>
    </form>

    <!-- Applicants Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr class="text-center">
                <th style="width: 10%;">Application Number</th>
                <th style="width: 20%;">Full Name</th>
                <th style="width: 10%;">Application Date</th>
                <th style="width: 5%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($applications as $app)
                <tr>
                    <td>{{ $app->application_number }}</td>
                    <td>{{ $app->lastname }}, {{ $app->firstname }} {{ $app->middlename }}</td>
                    <td class="text-center">{{ $app->created_at->format('F d, Y') }}</td>
                    <td class="text-center">
                        @if ($app->status != 0)
                            <span class="badge bg-success d-inline-flex align-items-center">
                                <span class="legend-indicator bg-white me-1"></span>
                                Paid
                            </span>
                        @else
                            <span class="badge bg-danger d-inline-flex align-items-center">
                                <span class="legend-indicator bg-white me-1"></span>
                                Unpaid
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No applicants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Per page control -->
    <form method="GET" action="{{ route('admission.shs.index') }}" class="mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            
            <!-- Dropdown -->
            <div class="d-flex align-items-center mb-2">
                <label for="per_page" class="me-2 fw-semibold text-custom">Show</label>
                <select name="per_page" id="per_page" 
                        class="form-select form-select-custom w-auto"
                        onchange="this.form.submit()">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                </select>
                <span class="ms-2 text-custom">
                    entries of <strong>{{ $applications->total() }} applicants</strong>
                </span>
            </div>

            <!-- Pagination -->
            <div class="pagination-custom">
                {{ $applications->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <input type="hidden" name="search" value="{{ request('search') }}">
    </form>


</div>
@endsection