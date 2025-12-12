@extends('layouts.master')

@section('css')
<link rel="preload" href="{{URL::to('/')}}/img/ogt/ucattemplate.jpg" as="image" />
<style>
    
    @media print {
        @page {
            margin: 0in;
        }

        body {
            background-image: url("{{URL::to('/')}}/img/ogt/ucattemplate.jpg") !important;
            background-size: 100vw 100vh !important;
            background-repeat: no-repeat !important;
            padding: 2in 0.5in 0.5in 0.5in !important;
        }

        .remarks-box {
            border: 1px solid #ddd;
            padding: 8px;
            min-height: 40px;
            background-color: #f9f9f9;
            width: 100%;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        
        .inline-table {
            display: inline-table;
            width: 50%;
        }
    }
</style>
@stop

@section('content')
    <div class="hidden-print mb-3">
        <button type="button" class="print btn btn-success">Print Result</button>
    </div>

    <div class="text-center mb-3">
        <strong style="font-size: 16px;">OFFICE OF GUIDANCE AND TESTING</strong><br>
        <strong style="font-size: 16px;">
        @if($result->applicant_status === 'Incoming Nursery' || $result->applicant_status === 'Incoming Preparatory' || $result->applicant_status === 'Incoming Kinder')
            PRESCHOOL READINESS ADMISSION TEST
        @else
            GRADER'S ADMISSION TEST
        @endif
        </strong>
    </div>

    <p style="font-size: 16px;">
        <strong>{{ $result->lastname }}, {{ $result->firstname }} @if($result->suffix){{ $result->suffix }}@endif
            {{ $result->middlename }}</strong><br>
        {{ $result->address }}, {{ $result->zip_code }}<br>
        {{ $result->mobile_no }}
    </p>

    <p style="font-size: 16px;">
        <strong>Dear @if($result->gender == 'Male') Mr. @else Ms. @endif{{ $result->lastname }},</strong><br>
        This is to inform you of your University of Santo Tomas-Legazpi Admission Test result.
    </p>

    <!-- NURSERY, PREPARATORY, KINDER -->
    @if($result->applicant_status === 'Incoming Nursery' || $result->applicant_status === 'Incoming Preparatory' || $result->applicant_status === 'Incoming Kinder')
        <div class="row g-3 mt-1 mb-4">
            <!-- LEFT COLUMN -->
            <div class="col-12 col-md-6">
                <!-- APPLICATION INFORMATION -->
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>APPLICATION INFORMATION</strong></td>
                        </tr>
                        <tr>
                            <td width="200"><strong>Name</strong></td>
                            <td>{{ strtoupper($result->lastname) }}, {{ strtoupper($result->firstname) }}
                                @if($result->suffix){{ strtoupper($result->suffix) }}@endif
                                {{ strtoupper($result->middlename) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Age</strong></td>
                            <td>{{ $result->age ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Birthday</strong></td>
                            <td>{{ $result->dob ? date('F j, Y', strtotime($result->dob)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nationality</strong></td>
                            <td>{{ $result->nationality }}</td>
                        </tr>
                        <tr>
                            <td><strong>Religion</strong></td>
                            <td>{{ $result->religion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>{{ $result->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Name</strong></td>
                            <td>{{ $result->school_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Address</strong></td>
                            <td>{{ $result->school_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Year</strong></td>
                            <td>{{ $result->year }} - {{ $result->year + 1 }}</td>
                        </tr>
                        <tr>
                            <td><strong>Examination Date</strong></td>
                            <td>{{ date('F j, Y', strtotime($result->exam_schedule_date)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>OR Number</strong></td>
                            <td>{{ $result->collection->or_number ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- RIGHT COLUMN -->
            <div class="col-12 col-md-6">
                <!-- INTERVIEWER'S REMARKS -->
                <table class="table table-bordered table-condensed report mb-3">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>INTERVIEWER'S REMARKS</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="remarks-box">{{ $result->interviewer_remarks ?? 'N/A' }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="d-flex">
                    <!-- PRESCHOOL READINESS TEST RECOMMENDED PLACEMENT -->
                    <table class="table table-bordered table-condensed report mb-0 inline-table">
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center"><strong>PRESCHOOL READINESS TEST RECOMMENDED PLACEMENT</strong></td>
                            </tr>
                            <tr>
                                <td>{{ $result->placement ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- REMARKS -->
                    <table class="table table-bordered table-condensed report mb-0 inline-table">
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center"><strong>REMARKS</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="remarks-box">{{ $result->remarks ?? 'N/A' }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SIGNATORIES -->
        <div class="row mt-3 text-center">
            <!-- Certified True and Correct -->
            <div class="col-12 col-md-6 mb-4">
                <div style="text-align:left;">
                    <div>Certified True and Correct:</div>
                    <div style="height:30px;"></div> <!-- space for signature -->
                    <strong>{{ $result->certifier_name ?? 'N/A' }}</strong><br>
                    <span>{{ $result->certifier_designation ?? 'Psychometrician' }}</span>
                </div>
            </div>

            <!-- Verified by -->
            <div class="col-12 col-md-6">
                <div style="text-align:left;">
                    <div>Verified by:</div>
                    <div style="height:30px;"></div> <!-- space for signature -->
                    <strong>{{ $result->verifier_name ?? 'N/A' }}</strong><br>
                    <span>{{ $result->verifier_designation ?? 'Director, Office of Guidance and Testing' }}</span>
                </div>
            </div>
        </div>
        <br>
        <!-- ENROLLMENT CREDENTIALS -->
        <table width="100%">
            <tr>
                <td colspan="2"><strong>Kindly bring the following credentials for enrolment purposes:</strong></td>
            </tr>
        </table><br>
        
        <table width="100%">
            <tr>
                <td width="100%" style="padding-left:10px; vertical-align:top;">
                    <ol>
                        <li>UST-Legazpi Pre-School Readiness Admission Test Result</li>
                        <li>Original copy of Form 138 or Report Card (if not first time learner)</li>
                        <li>Original Copy of Certificate of Good Moral Character (if not first time learner)</li>
                        <li>Original and photocopy of PSA Birth Certificate (if born abroad, clear copy of valid Philippine Passport)</li>
                        <li>2 copies of 2 x 2 picture with white background and name tag</li>
                        <li>1 copy of 1 x 1 picture with white background</li>
                        <li>Medical Certificate</li>
                        <li>Long Brown Envelope with Full Name Written on the Top Left</li>
                    </ol>
                </td>
            </tr>
        </table>

        <div class="mt-3">
            <span>Note: Second copy of this result is available at the Office of Guidance and Testing with printing fee of Php 50.00</span>
        </div>

        <div class="mt-2">
            <span>Record Code: OGT-TC-10.1</span>
        </div>

    <!-- GRADE 1 -->
    @elseif($result->applicant_status === 'Incoming Grade 1')
        <div class="row g-3 mt-1 mb-4">
            <!-- LEFT COLUMN -->
            <div class="col-12 col-md-6">
                <!-- APPLICANT INFORMATION -->
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>APPLICANT INFORMATION</strong></td>
                        </tr>
                        <tr>
                            <td width="200"><strong>Name</strong></td>
                            <td>{{ strtoupper($result->lastname) }}, {{ strtoupper($result->firstname) }}
                                @if($result->suffix){{ strtoupper($result->suffix) }}@endif
                                {{ strtoupper($result->middlename) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Age</strong></td>
                            <td>{{ $result->age ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Birthday</strong></td>
                            <td>{{ $result->dob ? date('F j, Y', strtotime($result->dob)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nationality</strong></td>
                            <td>{{ $result->nationality }}</td>
                        </tr>
                        <tr>
                            <td><strong>Religion</strong></td>
                            <td>{{ $result->religion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>LRN</strong></td>
                            <td>{{ $result->lrn ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>{{ $result->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Name</strong></td>
                            <td>{{ $result->school_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Address</strong></td>
                            <td>{{ $result->school_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Year</strong></td>
                            <td>{{ $result->year }} - {{ $result->year + 1 }}</td>
                        </tr>
                        <tr>
                            <td><strong>Examination Date</strong></td>
                            <td>{{ date('F j, Y', strtotime($result->exam_schedule_date)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Grade Level</strong></td>
                            <td>{{ $result->applicant_status }}</td>
                        </tr>
                    </tbody>
                </table><br>
                
                <!-- INTERVIEWER'S REMARKS -->
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>INTERVIEWER'S REMARKS</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="remarks-box">{{ $result->interviewer_remarks ?? 'N/A' }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- RIGHT COLUMN -->
            <div class="col-12 col-md-6">
                <!-- ADMISSION RESULT -->
                <div id="admission_result_section">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="text-center text-dark fs-5">SCHOOL READINESS TEST RESULT</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Test</th>
                                            <th class="text-center">Maximum Possible Score</th>
                                            <th class="text-center">RS</th>
                                            <th class="text-center">Standards-Based Readiness Rating</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            // Get subtest results for type 1 (Grade 1 readiness tests)
                                            $subtestResults = DB::table('admission_pse_elem_subtest_result')
                                                ->join('admission_pse_elem_subtest', 'admission_pse_elem_subtest_result.subtest_id', '=', 'admission_pse_elem_subtest.id')
                                                ->where('admission_pse_elem_subtest_result.application_number_id', $result->application_number)
                                                ->where('admission_pse_elem_subtest.type', 1)
                                                ->select('admission_pse_elem_subtest.name', 'admission_pse_elem_subtest.maxscore', 'admission_pse_elem_subtest_result.rs', 'admission_pse_elem_subtest_result.percentage')
                                                ->get();
                                        @endphp

                                        @if($subtestResults->count() > 0)
                                            @foreach($subtestResults as $subtest)
                                                <tr>
                                                    <td><b>{{ $subtest->name }}</b></td>
                                                    <td class="text-center">{{ $subtest->maxscore }}</td>
                                                    <td class="text-center">{{ $subtest->rs }}</td>
                                                    <td class="text-center">{{ $subtest->percentage ?: 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">No test results available</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="text-center"><b>Total (Over All) Readiness</b></td>
                                            <td class="text-center"><b>126</b></td>
                                            <td class="text-center">
                                                @if($subtestResults->count() > 0)
                                                    <b>{{ $subtestResults->sum('rs') }}</b>
                                                @else
                                                    <b>0</b>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($subtestResults->count() > 0)
                                                    @if($subtestResults->sum('rs') >= 100)
                                                        <b>Ready</b>
                                                    @else
                                                        <b>Marginally Ready</b>
                                                    @endif
                                                @else
                                                    <b>N/A</b>
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <!-- OVERALL REMARKS -->
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Overall Remarks</strong></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="remarks-box">
                                                @if($subtestResults->count() > 0)
                                                    @if($subtestResults->sum('rs') >= 100)
                                                        Qualified
                                                    @else
                                                        Not Qualified
                                                    @endif
                                                @else
                                                    No results available.
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIGNATORIES -->
        <div class="row mt-3 text-center">
            <!-- Certified True and Correct -->
            <div class="col-12 col-md-6 mb-4">
                <div style="text-align:left;">
                    <div>Certified True and Correct:</div>
                    <div style="height:30px;"></div> <!-- space for signature -->
                    <strong>{{ $result->certifier_name ?? 'N/A' }}</strong><br>
                    <span>{{ $result->certifier_designation ?? 'Psychometrician' }}</span>
                </div>
            </div>

            <!-- Verified by -->
            <div class="col-12 col-md-6">
                <div style="text-align:left;">
                    <div>Verified by:</div>
                    <div style="height:30px;"></div> <!-- space for signature -->
                    <strong>{{ $result->verifier_name ?? 'N/A' }}</strong><br>
                    <span>{{ $result->verifier_designation ?? 'Director, Office of Guidance and Testing' }}</span>
                </div>
            </div>
        </div>

        <br>

        <!-- Principal's Remarks -->
        <table width="100%" class="table-bordered table-striped">
            <tr>
                <td width="75%" style="padding: 8px;"><strong>Principal's Remarks :</strong></td>
                <td width="25%" style="padding: 8px;"><strong>Signature :</strong></td>
            </tr>
        </table>
        <br>
        <!-- ENROLLMENT CREDENTIALS -->
        <table width="100%">
            <tr>
                <td colspan="2"><strong>Kindly bring the following credentials for enrolment purposes:</strong></td>
            </tr>
        </table><br>
        
        <table width="100%">
            <tr>
                <td width="100%" style="padding-left:10px; vertical-align:top;">
                    <ol>
                        <li>UST-Legazpi Graders' Admission Evaluation Test Result</li>
                        <li>Original copy of Form 138 or Report Card with LRN</li>
                        <li>Certification of LRN (if Report Card does not contain the LRN)</li>
                        <li>Original Copy of Certificate of Good Moral Character</li>
                        <li>Clear copy of PSA Birth Certificate (if born abroad, clear copy of valid Philippine Passport)</li>
                    </ol>
                </td>
            </tr>
        </table>

        <div class="mt-3">
            <span>Note: Second copy of this result is available at the Office of Guidance and Testing with printing fee of Php 50.00</span>
        </div>

    <!-- GRADE 2-6 -->
    @elseif($result->applicant_status === 'Transferee (Grade 2-6)')
        <div class="row g-3 mt-1 mb-4">
            <!-- LEFT COLUMN -->
            <div class="col-12 col-md-6">
                <!-- APPLICANT INFORMATION -->
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>APPLICANT INFORMATION</strong></td>
                        </tr>
                        <tr>
                            <td width="200"><strong>Name</strong></td>
                            <td>{{ strtoupper($result->lastname) }}, {{ strtoupper($result->firstname) }}
                                @if($result->suffix){{ strtoupper($result->suffix) }}@endif
                                {{ strtoupper($result->middlename) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Age</strong></td>
                            <td>{{ $result->age ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Birthday</strong></td>
                            <td>{{ $result->dob ? date('F j, Y', strtotime($result->dob)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nationality</strong></td>
                            <td>{{ $result->nationality }}</td>
                        </tr>
                        <tr>
                            <td><strong>Religion</strong></td>
                            <td>{{ $result->religion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>LRN</strong></td>
                            <td>{{ $result->lrn ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Name</strong></td>
                            <td>{{ $result->school_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Address</strong></td>
                            <td>{{ $result->school_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>School Year</strong></td>
                            <td>{{ $result->year }} - {{ $result->year + 1 }}</td>
                        </tr>
                        <tr>
                            <td><strong>Examination Date</strong></td>
                            <td>{{ date('F j, Y', strtotime($result->exam_schedule_date)) }}</td>
                        </tr>
                    </tbody>
                </table><br>
                
                <!-- RECOMMENDED PLACEMENT -->
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>RECOMMENDED PLACEMENT</strong></td>
                        </tr>
                        <tr>
                            <td width="200"><strong>Program Preference</strong></td>
                            <td>{{ $result->program_preference ?? $result->applicant_status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- RIGHT COLUMN -->
            <div class="col-12 col-md-6">
                <!-- ADMISSION RESULT -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center text-dark fs-5">ADMISSION RESULT</th>
                            </tr>
                            <tr>
                                <th class="text-center">Area</th>
                                <th class="text-center">TS</th>
                                <th class="text-center">RS</th>
                                <th class="text-center">Percentage Equivalent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($subtests) && count($subtests) > 0)
                                @foreach($subtests as $subtest)
                                    <tr>
                                        <td><b>{{ $subtest->name }}</b></td>
                                        <td class="text-center">{{ $subtest->ts }}</td>
                                        <td class="text-center">{{ $subtest->rawscore }}</td>
                                        <td class="text-center">{{ $subtest->equivalent }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">No test results available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div><br>
                
                <!-- REMARKS -->
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>REMARKS</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="remarks-box">{{ $result->remarks ?? 'Qualified' }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table><br>
                
                <!-- INTERVIEWER REMARKS -->
                <table class="table table-bordered table-condensed report mb-0">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>INTERVIEWER'S REMARKS</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="remarks-box">{{ $result->interviewer_remarks ?? 'N/A' }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SIGNATORIES -->
        <div class="row mt-3 text-center">
            <!-- Certified True and Correct -->
            <div class="col-12 col-md-6 mb-4">
                <div style="text-align:left;">
                    <div>Certified True and Correct:</div>
                    <div style="height:30px;"></div> <!-- space for signature -->
                    <strong>{{ $result->certifier_name ?? 'N/A' }}</strong><br>
                    <span>{{ $result->certifier_designation ?? 'Psychometrician' }}</span>
                </div>
            </div>

            <!-- Verified by -->
            <div class="col-12 col-md-6">
                <div style="text-align:left;">
                    <div>Verified by:</div>
                    <div style="height:30px;"></div> <!-- space for signature -->
                    <strong>{{ $result->verifier_name ?? 'N/A' }}</strong><br>
                    <span>{{ $result->verifier_designation ?? 'Director, Office of Guidance and Testing' }}</span>
                </div>
            </div>
        </div>

        <br>

        <!-- Principal's Remarks -->
        <table width="100%" class="table-bordered table-striped">
            <tr>
                <td width="75%" style="padding: 8px;"><strong>Principal's Remarks :</strong></td>
                <td width="25%" style="padding: 8px;"><strong>Signature :</strong></td>
            </tr>
        </table>
        <br>
        <!-- ENROLLMENT CREDENTIALS -->
        <table width="100%">
            <tr>
                <td colspan="2"><strong>Kindly bring the following credentials for enrolment purposes:</strong></td>
            </tr>
        </table><br>
        
        <table width="100%">
            <tr>
                <td width="100%" style="padding-left:10px; vertical-align:top;">
                    <ol>
                        <li>UST-Legazpi Pre-School Readiness Admission Test Result</li>
                        <li>Original copy of Form 138 or Report Card (if not first time learner)</li>
                        <li>Original Copy of Certificate of Good Moral Character (if not first time learner)</li>
                        <li>Original and photocopy of PSA Birth Certificate (if born abroad, clear copy of valid Philippine Passport)</li>
                        <li>2 copies of 2 x 2 picture with white background and name tag</li>
                        <li>1 copy of 1 x 1 picture with white background</li>
                        <li>Medical Certificate</li>
                        <li>Long Brown Envelope with Full Name Written on the Top Left</li>
                    </ol>
                </td>
            </tr>
        </table>

        <div class="mt-3">
            <span>Note: Second copy of this result is available at the Office of Guidance and Testing with printing fee of Php 50.00</span>
        </div>
    @endif

    <script>
        document.querySelector('.print').addEventListener('click', function () {
            window.print();
        });
    </script>

@endsection