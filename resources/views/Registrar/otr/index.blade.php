@extends('layouts.master')

@section('content')
<div class="col-12">

    <h3 class="mt-4 fw-bold mb-4">OTR Management</h3>

    @if(session('success'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end', 
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            width: '400px',
        });
    </script>
    @endif

    @if(session('message'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end', 
            icon: 'success',
            title: '{{ session('message') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            width: '400px',
        });
    </script>
    @endif

    <div class="card shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">OTR Records</h3>
            <div>
                <a href="{{ route('registrar.otr.import') }}" class="btn btn-info me-2">
                    <i class="fas fa-file-import me-1"></i> Import Excel
                </a>
                <a href="{{ route('registrar.otr.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create New
                </a>
            </div>
        </div>

        <div class="card-body table-responsive">
            @if(isset($otrs) && $otrs->count() > 0)
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr class="text-center align-middle">
                        <th width="25%">Student Name</th>
                        <th width="15%">Student ID</th>
                        <th>Degree Course</th>
                        <th width="15%">Graduation Date</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($otrs as $otr)
                    <tr class="align-middle">
                        <td>
                            {{ $otr->Last_Name }}, {{ $otr->First_Name }}
                            <br>
                            <small class="text-muted">{{ $otr->Middle_Name }}</small>
                        </td>
                        <td class="text-center">{{ $otr->Student_ID }}</td>
                        <td>
                            {{ $otr->program ? $otr->program->name : 'Not Assigned' }}
                        </td>
                        <td class="text-center">
                            {{ $otr->Date_of_Graduation ? $otr->Date_of_Graduation->format('Y-m-d') : '-' }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('registrar.otr.show', $otr->id) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('registrar.otr.edit', $otr->id) }}" class="btn btn-success btn-sm">Edit</a>
                                <form action="{{ route('registrar.otr.destroy', $otr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="alert alert-warning text-center mb-0">
                    No records found.
                </div>
            @endif
            
            {{-- Pagination --}}
            {{ $otrs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection