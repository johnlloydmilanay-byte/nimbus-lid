@extends('layouts.master')

@section('content')
<div class="col-12">

    <h3 class="mt-4 fw-bold mb-4">Subject Management</h3>

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
        <div class="card-header fw-bold">
            <div class="btn-group float-end" role="group">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal"><i class="bi bi-plus-lg"></i> Add New Subject</button>
                @include('Enrollment.SubjectManager.add') 
            </div>
        </div>

        <div class="card-body table-responsive">
            <div class="shadow-sm mb-4">
                @if(isset($subjects) && $subjects->count() > 0)
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center align-middle">
                            <th width="10%">Subject Code</th>
                            <th>Description</th>
                            <th width="5%">Units</th>
                            <th width="7%">Clock Hours</th>
                            <th width="5%">Laboratory</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $subj)
                        <tr class="align-middle">
                            <td>{{ $subj->code }}</td>
                            <td>{{ $subj->name }}</td>
                            <td class="text-center">{{ $subj->units ?? 0 }}</td>
                            <td class="text-center">{{ $subj->clock_hours ?? 0 }}</td>
                            <td class="text-center">{{ $subj->is_lab == 1 ? 'Yes' : 'No' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#editSubjectModal-{{ $subj->id }}">Edit</button>
                                    @include('Enrollment.SubjectManager.edit')
                                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteSubjectModal-{{ $subj->id }}">Delete</button>
                                    @include('Enrollment.SubjectManager.delete') 
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <div class="alert alert-warning text-center mb-0">
                        No subject found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#dept').on('change', function() {
        var deptId = $(this).val();
        var programSelect = $('#prog');

        programSelect.html('<option value="">Loading...</option>');

        if(deptId) {
            $.ajax({
                url: '{{ route("enrollment.subjectmanager.getPrograms", "") }}/' + deptId,
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