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

            <!-- PROGRAM PREFERENCE -->
            <table class="table table-bordered table-condensed report mb-0">
                <tbody>
                    <tr>
                        <td colspan="2" class="text-center"><strong>PROGRAM PREFERENCE</strong></td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="2"><strong>{{ strtoupper($result->program_name) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-12 col-md-6">
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

            <!-- TOTAL RATING AND REMARKS -->
            <table class="table table-bordered table-condensed report mb-0">
                <tbody>
                    <tr>
                        <td class="text-center" colspan="2"><strong>TOTAL RATING</strong></td>
                        <td class="text-center" width="240"><strong>{{ $result->total_rating ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="2"><strong>REMARKS</strong></td>
                        <td class="text-center"><strong>{{ $result->remarks ?? 'N/A' }}</strong></td>
                    </tr>
                </tbody>
            </table><br>

            <div class="text-center">
                <span>TS - Total Score<br>RS - Raw Score<br>API - Academic Potential Index<br>DIQ - Deviation IQ</span>
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
            <td colspan="2"><strong>Kindly bring the following credentials for enrolment purposes:</strong></td>
        </tr>
    </table><br>

   <table width="100%" cellspacing="0" cellpadding="10">
    <tr>
        <!-- Left Column: For Incoming Grade 7 -->
        <td width="50%" valign="top" style="padding-right: 15px;">
            <strong>For Incoming Grade 7:</strong>
            <ol>
                <li>Photocopy of UST-Legazpi Junior High School Admission Test Result</li>
                <li>Original and photocopy of Form 138 or Report Card</li>
                <li>Original copy of Certificate of Good Moral Character</li>
                <li>Two (2) photocopies of PSA Birth Certificate (If born abroad, clear copy of valid Philippine Passport)</li>
                <li>Two (2) copies of 2 x 2 picture with white background and name tag</li>
                <li>Photocopy of Proof of Income (e.g. certificate of employment/indigency, 2022 Income Tax Return)</li>
                <li>One (1) Long Plastic Brown Envelope</li>
                <li>One (1) Long Folder with fastener (Green for Science High Applicants, Blue for General Curriculum Applicants, & Yellow for SPA Applicants)</li>
            </ol>
        </td>

        <!-- Right Column: For Transfer Applicants -->
        <td width="50%" valign="top" style="padding-left: 15px;">
            <strong>For Transfer Applicants (Grade 8–10):</strong>
            <ol>
                <li>Photocopy of UST-Legazpi Junior High School Admission Test Result</li>
                <li>Original and photocopy of Form 138 or Report Card</li>
                <li>Original copy of Certificate of Good Moral Character</li>
                <li>Two (2) photocopies of PSA Birth Certificate (If born abroad, clear copy of valid Philippine Passport)</li>
                <li>Two (2) copies of 2 x 2 picture with white background and name tag</li>
                <li>ESC Certificate (for ESC Grantees from Previous School or if applicable)</li>
                <li>Honorable Dismissal / Certificate of Transfer</li>
                <li>Student's Handwritten Letter of Application to UST-Legazpi</li>
                <li>One (1) Long Brown Plastic Envelope</li>
                <li>One (1) Long Folder with fastener (Green for Science High Applicants, Blue for General Curriculum Applicants, & Yellow for SPA Applicants)</li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 10px;">
            <strong>Record Code:</strong> OGT-TC-10.4
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <em>Note:</em> Hard copy of this result is available at the Office of Guidance and Testing with printing fee of Php 45.00
        </td>
    </tr>
</table>


    </div>

    <script>
        document.querySelector('.print').addEventListener('click', function () {
            window.print();
        });
    </script>

@endsection