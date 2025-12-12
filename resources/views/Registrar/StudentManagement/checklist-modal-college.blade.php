<div class="modal fade" id="checklistModalCollege" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="checklistModalLabel">Requirements Checklist</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="requirementsForm" method="POST"
                      action="{{ route('registrar.studentmanagement.collegeRequirementUpdate', $collegeRequirements->application_number) }}">
                    @csrf @method('PATCH')

                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width:50%">Requirements</th>
                                <th style="width:25%">Status</th>
                                <th style="width:25%">Updated By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $requirementsList = [
                                    ['has_college_result',          'collegeResultSigner',      'college_result_signed_at',         'Photocopy of UST-Legazpi College Admission Test Result'],
                                    ['has_college_report_card',     'collegeReportCardSigner',  'college_report_card_signed_at',    'Original copy of Senior High School Report card or Form 138'],
                                    ['has_college_good_moral',      'collegeGoodMoralSigner',   'college_good_moral_signed_at',     'Original copy of Certificate of Good Moral Character'],
                                    ['has_college_psa',             'collegePsaSigner',         'college_psa_signed_at',            'Clear copy of PSA Birth Certificate (if born abroad, clear copy of valid Philippine Passport)'],
                                    ['has_college_pic',             'collegePicSigner',         'college_pic_signed_at',            'Two (2) copies 2 x 2 picture with white background'],
                                    ['has_college_envelope',        'collegeEnvelopeSigner',    'college_envelope_signed_at',       'One (1) Long Brown Envelope with Full Name Written on the Top Left'],
                                ];
                            @endphp

                            @foreach($requirementsList as [$field, $signer, $dateField, $label])
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <div class="status-box {{ $collegeRequirements->$field ? 'bg-success' : 'bg-danger' }}"></div>
                                            <select class="form-select form-select-sm text-center status-select" name="{{ $field }}">
                                                <option value="1" {{ $collegeRequirements->$field ? 'selected' : '' }}>Completed</option>
                                                <option value="0" {{ !$collegeRequirements->$field ? 'selected' : '' }}>Pending</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($collegeRequirements->$field && $collegeRequirements->$signer)
                                            <small>{{ $collegeRequirements->$signer->employee->firstname ?? '' }}
                                                   {{ $collegeRequirements->$signer->employee->lastname ?? '' }}</small><br>
                                            <small class="text-muted">{{ optional($collegeRequirements->$dateField)->format('M d, Y h:i A') }}</small>
                                        @else — @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="submit" form="requirementsForm" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i> Update Requirements
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.status-box {
    width: 18px; height: 18px; border-radius: 4px;
    border: 1px solid #ccc; transition: background-color .3s ease;
}
</style>

<script>
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function () {
        const box = this.closest('td').querySelector('.status-box');
        box.classList.toggle('bg-success', this.value == '1');
        box.classList.toggle('bg-danger', this.value == '0');
    });
});
</script>