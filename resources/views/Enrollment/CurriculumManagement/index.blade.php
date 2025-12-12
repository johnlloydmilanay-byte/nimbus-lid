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

    <h3 class="mt-4 fw-bold mb-4">Curriculum Management</h3>

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold"><i class="fa fa-info-circle"></i> Filter Options: </div>
        <div class="card-body">
            <form method="GET" action="{{ route('enrollment.curriculum.index') }}">

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

                <div class="row g-2 align-items-center mt-1">
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold">Programs</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <select class="form-select" id="prog" name="program_id" required>
                            <option value="">-- Select Program --</option>
                        </select>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success fw-bold">Manage Curriculum</button>
                </div>
            </form>
        </div>
    </div>

    @if(request()->filled('department_id') || request()->filled('program_id'))
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            <i class="fa fa-folder-open"></i>
            @if(request('department_id')) {{ $departments->firstWhere('id', request('department_id'))->code ?? 'N/A' }} @endif - 
            @if(request('program_id')) {{ $programs->firstWhere('id', request('program_id'))->name ?? 'N/A' }} @endif
            <div class="btn-group float-end" role="group">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCurriculumModal"><i class="bi bi-plus-lg"></i> Add New Curriculum</button>
                @include('Enrollment.CurriculumManagement.add-curriculum') 
            </div>
        </div>

        <div class="card-body table-responsive">
            <div class="shadow-sm mb-4">
                @if(isset($curriculumyear) && $curriculumyear->count() > 0)
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center align-middle">
                            <th>Program</th>
                            <th>Curriculum Year</th>
                            <th>Years</th>
                            <th>Terms</th>
                        <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($curriculumyear as $curr)
                        <tr class="align-middle">
                            <td>{{ $curr->program->name }}</td>
                            <td class="text-center">{{ $curr->curriculum_year }} - {{ $curr->curriculum_year + 1 }}</td>
                            <td class="text-center">{{ $curr->lec_units ?? 0 }}</td>
                            <td class="text-center">{{ $curr->lab_units ?? 0 }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#editCurriculumModal-{{ $curr->id }}">Edit</button>
                                    <a href="{{ route('enrollment.curriculum.manage', ['program' => $curr->program_id, 'curriculumyear' => $curr->curriculum_year]) }}" class="btn btn-secondary btn-sm rounded-end">Manage</a>
                                    @include('Enrollment.CurriculumManagement.edit') 
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <div class="alert alert-warning text-center mb-0">
                        No curriculum year found.
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#dept').on('change', function() {
        var deptId = $(this).val();
        var programSelect = $('#prog');

        programSelect.html('<option value="">Loading...</option>');

        if(deptId) {
            $.ajax({
                url: '{{ route("enrollment.curriculum.getPrograms", "") }}/' + deptId,
                type: 'GET',
                success: function(data) {
                    programSelect.empty();
                    programSelect.append('<option value="">-- Select Program --</option>');

                    $.each(data, function(index, program) {
                        programSelect.append('<option value="' + program.id + '">' + program.name + '</option>');
                    });

                    var selectedProgram = '{{ request("program_id") }}';
                    if(selectedProgram) {
                        programSelect.val(selectedProgram);
                    }
                },
                error: function() {
                    programSelect.html('<option value="">-- No Programs Found --</option>');
                }
            });
        } else {
            programSelect.html('<option value="">-- Select Program --</option>');
        }
    });

    if($('#dept').val()) {
        $('#dept').trigger('change');
    }
});
</script>
@endsection