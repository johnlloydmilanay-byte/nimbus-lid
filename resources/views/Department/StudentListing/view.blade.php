@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold"><i class="bi bi-info-circle-fill me-2 text-primary"></i> Student Information</h2>
        <a href="{{ route('department.studentlisting.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="bi-chevron-left"></i> Back
        </a>
    </div>

    <hr class="border-2 border-warning opacity-75 my-4">

    <div class="row justify-content-center gap-4">
        <!-- Left Table: Applicant Info -->
        <div class="col-12 col-md-5">
            <table class="table table-bordered table-condensed">
                <tbody>
                    <tr>
                        <td colspan="2" class="text-center"><strong>APPLICANT INFORMATION</strong></td>
                    </tr>
                    <tr>
                        <td width="200"><strong>Application Number</strong></td>
                        <td>{{ $student->application_number }}</td>
                    </tr>
                    <tr>
                        <td><strong>Full Name</strong></td>
                        <td>{{ $student->lastname }}, {{ $student->firstname }} {{ $student->middlename }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>{{ $student->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nationality</strong></td>
                        <td>{{ $student->nationality }}</td>
                    </tr>
                    <tr>
                        <td><strong>Religion</strong></td>
                        <td>{{ $student->religion }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered table-condensed">
                <tbody>
                    
                    <!-- If the student is from College -->
                    @if ($student instanceof \App\Models\Admission\CollegeAdmission)
                        <tr><td colspan="2"><strong>PROGRAM PREFERENCE</strong></td></tr>
                        <tr><td width="200"><strong>COLLEGE</strong></td><td class="text-center"><strong>{{ strtoupper($student->choice_first_program->dname) }}</strong></td></tr>
                        <tr><td><strong>1st Choice</strong></td><td>{{ $student->choice_first_program->name }}</td></tr>
                        <tr><td><strong>2nd Choice</strong></td><td>{{ $student->choice_second_program->name }}</td></tr>
                        <tr><td><strong>3rd Choice</strong></td><td>{{ $student->choice_third_program->name }}</td></tr>
                    @endif

                    <!-- If the student is from SHS -->
                    @if ($student instanceof \App\Models\Admission\ShsAdmission)
                        <tr><td colspan="2"><strong>PROGRAM PREFERENCE</strong></td></tr>
                        <tr><td colspan="2"><strong>{{ $student->first_choice_name }}</strong></td></tr>
                    @endif

                    <!-- If the student is from JHS -->
                    @if ($student instanceof \App\Models\Admission\JhsAdmission)
                        <tr><td colspan="2"><strong>PROGRAM PREFERENCE</strong></td></tr>
                        <tr><td colspan="2"><strong>{{ $student->program_name }}</strong></td></tr>
                    @endif

                    <!-- If the student is from PSE -->
                    @if ($student instanceof \App\Models\Admission\PseAdmission)
                        <tr><td colspan="2" class="text-center"><strong>INTERVIEWER'S REMARKS</strong></td></tr>
                        <tr><td colspan="2"><strong>{{ $student->interviewer_remarks }}</strong></td></tr>
                    @endif

                    
                </tbody>
            </table>

        </div>

        <!-- Right Table: Admission Result -->
        <div class="col-12 col-md-6">

            <!-- ACADEMIC SUBJECTS -->

            <!-- If the student is from College -->
            @if ($student instanceof \App\Models\Admission\CollegeAdmission)
                <table class="table table-bordered table-condensed report mb-3">
                    <thead>
                        <tr>
                            <th colspan="4" class="text-center">ADMISSION RESULT</th>
                        </tr>
                        <tr>
                            <th class="text-center">SUBJECT</th>
                            <th class="text-center">TS</th>
                            <th class="text-center">RS</th>
                            <th class="text-center">API</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subtests as $r)
                        <tr>
                            <td>{{ strtoupper($r->name) }}</td>
                            <td class="text-center"><strong>{{ $r->ts }}</strong></td>
                            <td class="text-center"><strong>{{ $r->rawscore }}</strong></td>
                            <td class="text-center"><strong>{{ $r->api }}</strong></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">No subtests available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="3"><strong>Total Raw Score</strong></td>
                            <td class="text-center" width="240"><strong>{{ $student->total_rs ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-center" colspan="3"><strong>Overall Average API</strong></td>
                            <td class="text-center"><strong>{{ round($student->total_ave_api, 2) ?? 'N/A' }}</strong></td>
                        </tr>
                    </tbody>
                </table><br>
            @endif

            <!-- If the student is from SHS -->
            @if ($student instanceof \App\Models\Admission\ShsAdmission)
                <table class="table table-bordered table-condensed report mb-3">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-center">ADMISSION RESULT</th>
                        </tr>
                        <tr>
                            <th class="text-center">SUBJECT</th>
                            <th class="text-center">TS</th>
                            <th class="text-center">RS</th>
                            <th class="text-center">API</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicSubtests as $r)
                            <tr>
                                <td><strong>{{ strtoupper($r->name) }}</strong></td>
                                <td class="text-center">{{ $r->ts }}</td>
                                <td class="text-center">{{ $r->rawscore }}</td>
                                <td class="text-center">{{ $r->api }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No academic subtests available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="3"><strong>Total Raw Score</strong></td>
                            <td class="text-center" width="240"><strong>{{ $student->total_rs ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-center" colspan="3"><strong>Overall Average API</strong></td>
                            <td class="text-center"><strong>{{ round($student->total_ave_api, 2) ?? 'N/A' }}</strong></td>
                        </tr>
                    </tbody>
                </table><br>
            @endif

            <!-- If the student is from JHS -->
            @if ($student instanceof \App\Models\Admission\JhsAdmission)
                <!-- ACADEMIC SUBJECTS RESULTS -->
                <table class="table table-bordered table-condensed report mb-0">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-center">ADMISSION RESULT</th>
                        </tr>
                        <tr>
                            <th class="text-center">SUBJECT</th>
                            <th class="text-center">TS</th>
                            <th class="text-center">RS</th>
                            <th class="text-center">API</th>
                            <th class="text-center">PERCENTAGE EQUIVALENT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicSubtests as $r)
                            <tr>
                                <td><strong>{{ strtoupper($r->name) }}</strong></td>
                                <td class="text-center">{{ $r->ts }}</td>
                                <td class="text-center">{{ $r->rawscore }}</td>
                                <td class="text-center">{{ $r->api }}</td>
                                <td class="text-center">{{ $r->equivalent }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No academic subtests available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table><br>

                <!-- IQ TEST RESULTS -->
                <table class="table table-bordered table-condensed report mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">SUBJECT</th>
                            <th class="text-center">RS</th>
                            <th class="text-center">DIQ</th>
                            <th class="text-center">DESCRIPTION</th>
                            <th class="text-center">PERCENTAGE EQUIVALENT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($iqSubtests as $r)
                            <tr>
                                <td><strong>{{ strtoupper($r->name) }}</strong></td>
                                <td class="text-center">{{ $r->rawscore }}</td>
                                <td class="text-center">{{ $r->diq }}</td>
                                <td class="text-center">{{ $r->description }}</td>
                                <td class="text-center">{{ $r->equivalent }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No IQ test results available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table><br>

                <!-- INTERVIEW RESULTS -->
                <table class="table table-bordered table-condensed report mb-0">
                    <thead>
                        <tr>
                            <th colspan="3" class="text-center">INTERVIEW</th>
                        </tr>
                        <tr>
                            <th class="text-center">RS</th>
                            <th class="text-center">Equivalent</th>
                            <th class="text-center">PERCENTAGE EQUIVALENT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($interviewSubtest)
                            <tr>
                                <td class="text-center">{{ $interviewSubtest->rawscore }}</td>
                                <td class="text-center">{{ $interviewSubtest->transmutation }}</td>
                                <td class="text-center">{{ $interviewSubtest->equivalent }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="3" class="text-center">No interview results available</td>
                            </tr>
                        @endif
                    </tbody>
                </table><br>
            @endif

            <!-- If the student is from PSE -->
            @if ($student instanceof \App\Models\Admission\PseAdmission)
                
            @endif

            <!-- TOTAL RATING AND REMARKS -->
            <table class="table table-bordered table-condensed report mb-3">
                <tbody>
                    <tr>
                        <td>
                            <form action="{{ route('department.student.updateRemarks', $student->id) }}" method="POST" class="d-flex gap-2 justify-content-center">
                                @csrf
                                @method('PATCH')
                                @php
                                    $remarks = $student->remarks ?? 'SUBJECT FOR ENTRY';
                                @endphp

                                @if ($remarks !== 'QUALIFIED')
                                    <select name="remarks" class="form-select form-select-sm w-auto">
                                        <option value="QUALIFIED" {{ $remarks === 'QUALIFIED' ? 'selected' : '' }}>QUALIFIED</option>
                                        <option value="SUBJECT FOR ENTRY" {{ $remarks === 'SUBJECT FOR ENTRY' ? 'selected' : '' }}>SUBJECT FOR ENTRY</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                @else
                                    <span class="fw-bold text-success">QUALIFIED</span>
                                @endif
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>


            <div class="text-center">
                <span>TS - Total Score<br>RS - Raw Score<br>API - Academic Potential Index</span>
            </div>

        </div>
    </div>

</div>
@endsection
