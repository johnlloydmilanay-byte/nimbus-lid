@extends('layouts.master')

@section('content')

<div class="col-12">

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="mt-4 fw-bold mb-4">Student Management</h3>
        <a href="{{ route('registrar.studentmanagement.create') }}" class="btn btn-custom"><i class="bi bi-person-plus-fill me-1"></i> Add Student</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold"><i class="fa fa-folder-open"></i> Student Search</div>
        <div class="card-body">
            <form action="{{ route('registrar.studentmanagement.index') }}" method="GET" class="mb-4">
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-12 col-md-2"><label class="form-label fw-semibold">Student Number / Lastname</label></div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Enter Student Number or Lastname or Firstname" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-custom"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Table -->
    <div style="width: 500px;" class="small">
        <div class="card-body p-0">
            @if($students->count() > 0)
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Student ID</th>
                            <th>Application Number</th>
                            <th>Full Name</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr class="text-left align-middle">
                                <td>{{ $student->student_number }}</td>
                                <td>{{ $student->application_number }}</td>
                                <td>{{ $student->lastname }}, {{ $student->firstname }} {{ $student->middlename }}</td>
                                <td class="text-center"><a href="{{ route('registrar.studentmanagement.edit', $student->application_number) }}" class="btn btn-sm btn-custom">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $students->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
