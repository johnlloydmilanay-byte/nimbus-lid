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
    }
</style>
@stop

@section('content')
    <div class="hidden-print mb-3">
        <button type="button" class="print btn btn-success">Print Result</button>
    </div>

    <div class="text-center mb-3">
        <strong style="font-size: 16px;">OFFICE OF GUIDANCE AND TESTING</strong><br>
        <strong style="font-size: 16px;">UNIVERSITY OF SANTO TOMAS-LEGAZPI SENIOR HIGH SCHOOL ADMISSION TEST</strong>
    </div>

    <p style="font-size: 16px;">
        <strong>{{ $result->lastname }}, {{ $result->firstname }} @if($result->suffix){{ $result->suffix }}@endif
            {{ $result->middlename }}</strong><br>
        {{ $result->address }}, {{ $result->zip_code }}<br>
        {{ $result->mobile_no }}
    </p>

    <p style="font-size: 16px;">
        <strong>Dear @if($result->gender == 'Male') Mr. @else Ms. @endif{{ $result->lastname }},</strong><br>
        This is to inform you of your University of Santo Tomas-Legazpi Senior High School Admission Test result.
    </p>

    <div class="row g-3 mt-1 mb-4">

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
                            {{ strtoupper($result->middlename) }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Nationality</strong></td>
                        <td>{{ $result->nationality }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>{{ $result->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Junior High School</strong></td>
                        <td>{{ $result->school_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>School Address</strong></td>
                        <td>{{ $result->school_address }}, {{ $result->zip_code }}</td>
                    </tr>
                    <tr>
                        <td><strong>School Year</strong></td>
                        <td>{{ $result->year }} - {{ $result->year + 1 }}</td>
                    </tr>
                    <tr>
                        <td><strong>Term</strong></td>
                        <td>{{ $result->term }}</td>
                    </tr>
                    <tr>
                        <td><strong>Examination Date</strong></td>
                        <td>{{ date('F j, Y', strtotime($result->exam_schedule_date)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>{{ $result->applicant_status }}</td>
                    </tr>
                    <tr>
                        <td><strong>OR Number</strong></td>
                        <td>{{ $result->collection->or_number ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table><br>

            <!-- STRAND PREFERENCE -->
            <table class="table table-bordered table-condensed report mb-0">
                <tbody>
                    <tr>
                        <td><strong>PROGRAM PREFERENCE</strong></td>
                    </tr>
                    <tr>
                        <td class="text-center"><strong>{{ strtoupper($result->first_choice_name) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-12 col-md-6">
            <!-- ADMISSION RESULT -->
            <table class="table table-bordered table-condensed report mb-0">
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
                        @if($r->rawscore > 0) <!-- Add this condition to check if rawscore is greater than 0 -->
                            <tr>
                                <td><strong>{{ strtoupper($r->name) }}</strong></td>
                                <td class="text-center">{{ $r->ts }}</td>
                                <td class="text-center">{{ $r->rawscore }}</td>
                                <td class="text-center">{{ $r->api }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No subtests available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table><br>
            <table class="table table-bordered table-condensed report mb-0">
                <tbody>
                    <tr>
                        <td class="text-center" colspan="3"><strong>Total Raw Score</strong></td>
                        <td class="text-center" width="240"><strong>{{ $result->total_rs ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="3"><strong>Overall Average API</strong></td>
                        <td class="text-center"><strong>{{ round($result->total_ave_api, 2) ?? 'N/A' }}</strong></td>
                    </tr>
                </tbody>
            </table><br>

            <table class="table table-bordered table-condensed report">
                <tr>
                    <td class="text-center" style="padding: 10px;">
                        <h3 class="fw-bold mb-0">{{ $result->remarks ?? 'N/A' }}</h3>
                    </td>
                </tr>
            </table>

            <div class="text-center">
                <span>TS - Total Score<br>RS - Raw Score<br>API - Academic Potential Index</span>
            </div>
        </div>
    </div>

    <!-- SIGNATORIES -->
    <div class="row mt-3">
        <div class="col-12 col-md-6">
            <table>
                <tr>
                    <td width="200">Certified True and Correct:</td>
                    <td
                        style="height:80px; vertical-align:bottom; background-image: url('{{ url('/img/ogt/' . $result->created_by . '.png') }}'); background-size:150px; background-repeat:no-repeat;">
                        <strong>{{ $result->certifier_name ?? 'N/A' }}</strong><br>{{ $result->certifier_designation ?? 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-12 col-md-6">
            <table>
                <tr>
                    <td width="100" class="text-center">Verified by:</td>
                    <td
                        style="height:80px; vertical-align:bottom; background-image: url('{{ url('/img/ogt/070124A.png') }}'); background-size:150px; background-repeat:no-repeat;">
                        <strong>{{ $result->verifier_name ?? 'N/A' }}</strong><br>{{ $result->verifier_designation ?? 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>
    </div><br>

    <!-- Principal's Remarks -->
    <table width="100%" class="table-bordered table-striped">
        <tr>
            <td width="75%" style="padding: 8px;"><strong>Principal's Remarks :</strong></td>
            <td width="25%" style="padding: 8px;"><strong>Signature :</strong></td>
        </tr>
    </table><br>

    <!-- Enrollment Credentials -->
    <table width="100%">
        <tr>
            <span>Note: Hard copy of this result is available at the Office of Guidance and Testing with printing fee of Php
                50.00</span>
            <td colspan="2"><strong>Kindly bring the following credentials for enrolment purposes:</strong></td>
        </tr>
    </table><br>
    <table width="100%">
        <tr>
            <td width="100%" style="padding-left:10px; vertical-align:top;">
                <strong>For Incoming Senior High School:</strong>
                <ol>
                    <li>Photocopy of UST-Legazpi Senior High School Placement Test Result</li>
                    <li>Original and Photocopy of Form 138 or Report Card</li>
                    <li>Original copy of Certificate of Good Moral Character</li>
                    <li>Three (3) photocopies of PSA Birth Certificate (If born abroad, clear copy of valid Philippine
                        Passport)</li>
                    <li>Certified True Copy of Completion Certificate</li>
                    <li>Three (3) copies of 2 x 2 picture with white background and name tag</li>
                    <li>ESC Certificate (for ESC Grantees from previous school)</li>
                    <li>One (1) Long Brown Envelope with Full Name Written on Top Left</li>
                    <li>One (1) Long White Folder with Fastener</li>
                </ol>
                <strong>Additional Requirements for Transfer Applicant:</strong>
                <ol>
                    Honorable Dismissal/ Certificate of Transfer
                </ol>
            </td>
        </tr>
        <tr>
            <td colspan="2">Record Code: OGT-TC-10.3</td>
        </tr>
    </table>

    </div>

    <script>
        document.querySelector('.print').addEventListener('click', function () {
            window.print();
        });
    </script>

@endsection