@extends('layouts.master')

@section('content')

<div class="container-fluid px-4">

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h1 class="mt-4 fw-bold">
        <i class="bi bi-diagram-3-fill me-3 text-primary"></i> Student Listing
    </h1>
</div>
<br>

<!-- Qualified Students Table -->
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-success text-white fw-semibold">
        <i class="bi bi-check-circle-fill me-2"></i> Qualified Students
    </div>
    <div class="card-body p-0">
        @if($qualifiedStudents->count() > 0)
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Application Number</th>
                        <th style="width: 25%;">Full Name</th>
                        <th style="width: 25%;">Department</th>
                        <th style="width: 15%;">Remarks</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($qualifiedStudents as $student)
                        <tr>
                            <td>{{ $student->application_number }}</td>
                            <td>{{ $student->lastname }}, {{ $student->firstname }} {{ $student->middlename }}</td>
                            <td>{{ $student->program_info->dname ?? '-' }}</td>
                            <td>{{ $student->remarks ?? 'QUALIFIED' }}</td>
                            <td class="text-center">
                                <a href="{{ route('department.studentlisting.view', $student->application_number) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye-fill me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top flex-wrap">
                <div class="text-muted small mb-2 mb-md-0">
                    Showing {{ $qualifiedStudents->firstItem() }} to {{ $qualifiedStudents->lastItem() }} of {{ $qualifiedStudents->total() }} entries
                </div>
                <div>
                    {{ $qualifiedStudents->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="bi bi-exclamation-circle"></i> No qualified students found.
            </div>
        @endif
    </div>
</div>

<!-- Not Qualified Students Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white fw-semibold">
        <i class="bi bi-x-circle-fill me-2"></i> Subject for Entry Students
    </div>
    <div class="card-body p-0">
        @if($notQualifiedStudents->count() > 0)
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Application Number</th>
                        <th style="width: 25%;">Full Name</th>
                        <th style="width: 25%;">Department</th>
                        <th style="width: 15%;">Remarks</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notQualifiedStudents as $student)
                        <tr>
                            <td>{{ $student->application_number }}</td>
                            <td>{{ $student->lastname }}, {{ $student->firstname }} {{ $student->middlename }}</td>
                            <td>{{ $student->program_info->dname ?? '-' }}</td>
                            <td>{{ $student->remarks ?? 'SUBJECT FOR ENTRY' }}</td>
                            <td class="text-center">
                                <a href="{{ route('department.studentlisting.view', $student->application_number) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye-fill me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top flex-wrap">
                <div class="text-muted small mb-2 mb-md-0">
                    Showing {{ $notQualifiedStudents->firstItem() }} to {{ $notQualifiedStudents->lastItem() }} of {{ $notQualifiedStudents->total() }} entries
                </div>
                <div>
                    {{ $notQualifiedStudents->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="bi bi-exclamation-circle"></i> No subject for entry students found.
            </div>
        @endif
    </div>
</div>

</div>

@endsection
