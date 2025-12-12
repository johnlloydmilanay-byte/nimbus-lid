@extends('layouts.master')

@section('css')
<link rel="preload" href="{{URL::to('/')}}/img/ogt/ucattemplate.jpg" as="image" />
	<style>
			@media print {
				@page {
					margin: 0in;
				}
				body{
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
    <strong style="font-size: 16px;">UNIVERSITY OF SANTO TOMAS-LEGAZPI COLLEGE ADMISSION TEST</strong>
</div>

<p style="font-size: 16px;">
    <strong>{{ $result->lastname }}, {{ $result->firstname }} @if($result->suffix){{ $result->suffix }}@endif {{ $result->middlename }}</strong><br>
    {{ $result->address }}, {{ $result->zip_code }}<br>
    {{ $result->mobile_no }}
</p>

<p style="font-size: 16px;">
    <strong>Dear @if($result->gender == 'Male') Mr. @else Ms. @endif{{ $result->lastname }},</strong><br>
    This is to inform you of your University of Santo Tomas-Legazpi College Admission Test result.
</p>

<div class="row g-3 mt-1 mb-4">
    
    <div class="col-12 col-md-6">
        
        <!-- APPLICANT INFORMATION -->
        <table class="table table-bordered table-condensed report mb-0">
            <tbody>
                <tr><td colspan="2" class="text-center"><strong>APPLICANT INFORMATION</strong></td></tr>
                <tr><td width="200"><strong>Name</strong></td><td>{{ strtoupper($result->lastname) }}, {{ strtoupper($result->firstname) }} @if($result->suffix){{ strtoupper($result->suffix) }}@endif {{ strtoupper($result->middlename) }}</td></tr>
                <tr><td><strong>Nationality</strong></td><td>{{ $result->nationality }}</td></tr>
                <tr><td><strong>Email</strong></td><td>{{ $result->email }}</td></tr>
                <tr><td><strong>Senior High School Track and Strand</strong></td><td>{{ $result->strand->program ?? 'N/A' }}</td></tr>
                <tr><td><strong>School Name</strong></td><td>{{ $result->school_name }}</td></tr>
                <tr><td><strong>School Address</strong></td><td>{{ $result->school_address }}, {{ $result->zip_code }}</td></tr>
                <tr><td><strong>School Year</strong></td><td>{{ $result->year }} - {{ $result->year + 1 }}</td></tr>
                <tr><td><strong>Term</strong></td><td>{{ $result->term }}</td></tr>
                <tr><td><strong>Examination Date</strong></td><td>{{ date('F j, Y', strtotime($result->exam_schedule_date)) }}</td></tr>
                <tr><td><strong>Status</strong></td><td>{{ $result->applicant_status }}</td></tr>
                <tr><td><strong>OR Number</strong></td><td>{{ $result->collection->or_number }}</td></tr>
            </tbody>
        </table><br>

        <!-- PROGRAM PREFERENCES -->
        <table class="table table-bordered table-condensed report mb-0">
            <tbody>
                <tr><td colspan="2" class="text-center"><strong>PROGRAM PREFERENCES</strong></td></tr>
                <tr><td width="200"><strong>COLLEGE</strong></td><td class="text-center"><strong>{{ strtoupper($result->choice_first_program->dname ?? 'N/A') }}</strong></td></tr>
                <tr><td><strong>1st Choice</strong></td><td>{{ $result->choice_first_program->name ?? 'N/A' }}</td></tr>
                <tr><td><strong>2nd Choice</strong></td><td>{{ $result->choice_second_program->name ?? 'N/A' }}</td></tr>
                <tr><td><strong>3rd Choice</strong></td><td>{{ $result->choice_third_program->name ?? 'N/A' }}</td></tr>
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
            </table><br>
            <table class="table table-bordered table-condensed report mb-0">
                <tbody>
                    <tr><td class="text-center"><strong>Total Raw Score</strong></td><td class="text-center" width="240"><strong>{{ $result->total_rs }}</strong></td></tr>
                    <tr><td class="text-center"><strong>Average Academic Potential Index</strong></td><td class="text-center"><strong>{{ round($result->total_ave_api, 2) }}</strong></td></tr>
                </tbody>
            </table><br>

            <table class="table table-bordered table-condensed report">
                <tr><td class="text-center" style="padding: 10px;">{!! $result->condition !!}</td></tr>
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
                    <td style="height:80px; vertical-align:bottom; background-image: url('{{ url('/img/ogt/' . $result->created_by . '.png') }}'); background-size:150px; background-repeat:no-repeat;">
                        <strong>{{ $result->certifier_name }}</strong><br>{{ $result->certifier_designation }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-12 col-md-6">
            <table>
                <tr>
                    <td width="100" class="text-center">Verified by:</td>
                    <td style="height:80px; vertical-align:bottom; background-image: url('{{ url('/img/ogt/070124A.png') }}'); background-size:150px; background-repeat:no-repeat;">
                        <strong>{{ $result->verifier_name }}</strong><br>{{ $result->verifier_designation }}
                    </td>
                </tr>
            </table>
        </div>
    </div><br>

    <!-- Dean's Remarks -->
    <table width="100%" class="table-bordered table-striped">
        <tr>
            <td width="75%" style="padding: 8px;"><strong>Dean's Remarks :</strong></td>
            <td width="25%" style="padding: 8px;"><strong>Signature :</strong></td>
        </tr>
    </table><br>

    <!-- Enrollment Credentials -->
    <table width="100%">
        <tr>
            <span>Note: Hard copy of this result is available at the Office of Guidance and Testing with printing fee of Php 50.00</span>
            <td colspan="2"><strong>Kindly bring the following credentials for enrolment purposes:</strong></td>
        </tr>
    </table><br>
    <table width="100%">
        <tr>
            <td width="50%" style="padding-left:10px; vertical-align:top;">
                <strong>For Incoming First Year:</strong>
                <ol>
                    <li>Photocopy of UST-Legazpi College Admission Test Result</li>
                    <li>Original copy of Senior High School Report card or Form 138</li>
                    <li>Original copy of Certificate of Good Moral Character</li>
                    <li>Clear copy of PSA Birth Certificate (if born abroad, clear copy of valid Philippine Passport)</li>
                    <li>Two (2) copies 2 x 2 picture with white background</li>
                    <li>One (1) Long Brown Envelope with Full Name Written on the Top Left</li>
                </ol>
            </td>
            <td width="50%">
                <strong>For Transfer Applicants:</strong>
                <ol>
                    <li>Photocopy of UST-Legazpi College Admission Test Result</li>
                    <li>Honorable Dismissal/Certificate of Transfer</li>
                    <li>Original copy of Transcript of Records (Official/Copy for UST-Legazpi)</li>
                    <li>Original Copy of Good Moral Character</li>
                    <li>Three (3) clear copies of evaluated curriculum</li>
                    <li>NSTP Serial Number (if completed NSTP)</li>
                    <li>Clear copy of PSA Birth Certificate</li>
                    <li>Two (2) copies 2 x 2 picture with white background</li>
                    <li>One (1) Long Brown Envelope with Full Name written on the top left</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td colspan="2">Record Code: OGT-TC-10.4</td>
        </tr>
    </table>

</div>


@endsection
