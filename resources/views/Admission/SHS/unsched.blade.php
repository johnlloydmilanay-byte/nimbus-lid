@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1 class="mt-4 fw-bold">SHS Admission | Unschedule Paid Applicants</h1>
        <a href="{{ route('admission.shs.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
        </a>
    </div>

    <!-- Applicants Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr class="text-center">
                <th style="width: 5%;">Application Number</th>
                <th style="width: 30%;">Full Name</th>
                <th style="width: 10%;">Exam Date</th>
                <th style="width: 5%;">Status</th>
                <th style="width: 5%;" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($applications as $app)
                <tr style="vertical-align: middle;">
                    <td>{{ $app->application_number }}</td>
                    <td>{{ $app->lastname }}, {{ $app->firstname }} {{ $app->middlename }}</td>
                    <td> {{ $app->exam_schedule_date ? $app->exam_schedule_date->format('F d, Y') : 'UNSCHEDULED APPLICANT' }}</td>
                    <td class="text-center">
                        @if ($app->exam_schedule_date != null)
                            <span class="badge bg-success d-inline-flex align-items-center">
                                <span class="legend-indicator bg-white me-1"></span>
                                Scheduled
                            </span>
                        @else
                            <span class="badge bg-danger d-inline-flex align-items-center">
                                <span class="legend-indicator bg-white me-1"></span>
                                Unscheduled
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-pencil-fill"></i> Edit
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admission.shs.edit', $app->application_number) }}">
                                        <i class="bi-pencil-square me-2"></i> Update
                                    </a>
                                </li>
                            </ul>
                        </div>
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
    <form method="GET" action="{{ route('admission.shs.unsched') }}" class="mb-3">
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