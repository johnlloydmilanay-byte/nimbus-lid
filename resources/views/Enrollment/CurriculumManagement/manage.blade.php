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

    <table class="table table-bordered table-hover">
        <tr><th width="15%">Department</th><td>{{ $department->code ?? '' }} - {{ $department->name ?? '' }}</td></tr>
        <tr><th>Program</th><td>{{ $program->name ?? '' }}</td></tr>
        <tr><th>Curriculum Year</th><td>{{ $curriculumYear }} - {{ $curriculumYear + 1 }}</td></tr>
    </table>

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">
            <i class="fa fa-folder-open"></i> Curriculum Subjects
            <div class="btn-group float-end" role="group">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal"><i class="bi bi-plus-lg"></i> Add New Subject</button>
                @include('Enrollment.CurriculumManagement.add-subject') 
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered curriculum-details">

                @foreach($yearLevelDetails as $yl)
                    <tr>
                        <td class="text-center bg-primary text-white" colspan="8">
                            <strong>{{ $yl->description ?? $yl->name }}</strong>
                        </td>
                    </tr>

                    @if(isset($groupedSubjects[$yl->id]))
                        @foreach($groupedSubjects[$yl->id] as $termId => $subjects)
                            <tr>
                                <td class="text-center bg-info text-white" colspan="8"><strong>{{ $subjects->first()->term->name ?? 'Term '.$termId }}</strong></td>
                            </tr>
                            <tr class="text-center">
                                <th>Code</th>
                                <th>Name</th>
                                <th>Units</th>
                                <th>Clock Hours</th>
                                <th>Prerequisite</th>
                                <th>Major?</th>
                                <th>Action</th>
                            </tr>

                            @foreach($subjects as $item)
                                <tr class="align-middle">
                                    <td>{{ $item->subject->code }}</td>
                                    <td>{{ $item->subject->name }}</td>
                                    <td class="text-center">{{ $item->subject->units }}</td>
                                    <td class="text-center">{{ $item->subject->clock_hours }}</td>
                                    <td class="text-center">{!! $item->prerequisites->map(fn($p) => ($p->prereqSubject->code ?? '') . ' : ' . ($p->prereqSubject->name ?? ''))->implode('<br>') !!}</td>
                                    <td class="text-center">{{ $item->subject->is_major ? 'Yes' : 'No' }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $item->id }}">Delete</button>
                                        @include('Enrollment.CurriculumManagement.delete') 
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endif

                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
