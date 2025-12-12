<div class="modal fade" id="checklistModalPse" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
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
                      action="{{ route('registrar.studentmanagement.pseRequirementUpdate', $pseRequirements->application_number) }}">
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
                                    ['has_pse_result', 'pseResultSigner', 'pse_result_signed_at', 'UST-Legazpi Pre-School Readiness Admission Test Result'],
                                    ['has_pse_report_card', 'pseReportCardSigner', 'pse_report_card_signed_at', 'Original copy of Form 138 or Report Card (if not first time learner)'],
                                    ['has_pse_good_moral', 'pseGoodMoralSigner', 'pse_good_moral_signed_at', 'Original Copy of Certificate of Good Moral Character (if not first time learner)'],
                                    ['has_pse_psa', 'psePsaSigner', 'pse_psa_signed_at', 'Original and photocopy of PSA Birth Certificate (if born abroad, clear copy of valid Philippine Passport)'],
                                    ['has_pse_pic', 'psePicSigner', 'pse_pic_signed_at', '2 copies of 2 x 2 picture with white background and name tag'],
                                    ['has_pse_pic1', 'psePic1Signer', 'pse_pic1_signed_at', '1 copy of 1 x 1 picture with white background'],
                                    ['has_pse_medcert', 'pseMedCertSigner', 'pse_medcert_signed_at', 'Medical Certificate'],
                                    ['has_pse_envelope', 'pseEnvelopeSigner', 'pse_envelope_signed_at', 'Long Brown Envelope with Full Name Written on the Top Left'],
                                ];
                            @endphp

                            @foreach($requirementsList as [$field, $signer, $dateField, $label])
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <div class="status-box {{ $pseRequirements->$field ? 'bg-success' : 'bg-danger' }}"></div>
                                            <select class="form-select form-select-sm text-center status-select" name="{{ $field }}">
                                                <option value="1" {{ $pseRequirements->$field ? 'selected' : '' }}>Completed</option>
                                                <option value="0" {{ !$pseRequirements->$field ? 'selected' : '' }}>Pending</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($pseRequirements->$field && $pseRequirements->$signer)
                                            <small>{{ $pseRequirements->$signer->employee->firstname ?? '' }}
                                                   {{ $pseRequirements->$signer->employee->lastname ?? '' }}</small><br>
                                            <small class="text-muted">{{ optional($pseRequirements->$dateField)->format('M d, Y h:i A') }}</small>
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
