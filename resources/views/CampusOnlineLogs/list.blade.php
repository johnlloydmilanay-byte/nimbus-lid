@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1 class="mt-4 fw-bold">Campus Online Logs List</h1>
        <a href="{{ route('elogs.index') }}" class="btn btn-custom">
            <i class="bi-person-plus-fill me-1"></i> Add Applicant
        </a>
    </div>

    <!-- Search & Per Page Controls -->
    <form method="GET" action="{{ route('elogs.list') }}" class="d-flex flex-wrap gap-2 mb-3">
        <div class="flex-grow-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="form-control" placeholder="Search applicant name">
        </div>
        <div class="d-flex align-items-center">
            <label for="per_page" class="me-2 fw-semibold text-custom">Show</label>
            <select name="per_page" id="per_page" class="form-select form-select-custom w-auto" onchange="this.form.submit()">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
            </select>
        </div>
        <button type="submit" class="btn btn-custom">Search</button>
    </form>

    <!-- Applicants Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr class="text-center">
                <th style="width: 10%;">Date</th>
                <th style="width: 20%;">Full Name</th>
                <th style="width: 20%;">Purpose</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($applications as $app)
                <tr>
                    <td>{{ $app->created_at->format('F d, Y') }}</td>
                    <td>{{ $app->last_name }}, {{ $app->first_name }} {{ $app->middle_name }}</td>
                    <td>{{ $app->purpose }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No applicants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="text-custom mb-2">
            Showing <strong>{{ $applications->firstItem() ?? 0 }}</strong> to 
            <strong>{{ $applications->lastItem() ?? 0 }}</strong> of 
            <strong>{{ $applications->total() }}</strong> entries
        </div>
        <div class="pagination-custom">
            {{ $applications->appends(['search' => request('search'), 'per_page' => request('per_page')])
                ->onEachSide(1)
                ->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
@endsection
