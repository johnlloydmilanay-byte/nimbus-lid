<div class="modal fade" id="checklistModalShs" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
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
                      action="{{ route('registrar.studentmanagement.shsRequirementUpdate', $shsRequirements->application_number) }}">
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
                                    ['has_shs_result',          'shsResultSigner',      'shs_result_signed_at',          'Photocopy of UST-Legazpi Senior High School Placement Test Result'],
                                    ['has_shs_report_card',     'shsReportCardSigner',  'shs_report_card_signed_at',     'Original and Photocopy of Form 138 or Report Card'],
                                    ['has_shs_good_moral',      'shsGoodMoralSigner',   'shs_good_moral_signed_at',      'Original copy of Certificate of Good Moral Character'],
                                    ['has_shs_psa',             'shsPsaSigner',         'shs_psa_signed_at',             'Three (3) photocopies of PSA Birth Certificate (If born abroad, copy of valid Philippine Passport)'],
                                    ['has_shs_completion_cert', 'shsCompletionSigner',  'shs_completion_cert_signed_at', 'Certified True Copy of Completion Certificate'],
                                    ['has_shs_pic',             'shsPicSigner',         'shs_pic_signed_at',             'Three (3) copies of 2x2 picture with white background and name tag'],
                                    ['has_shs_esc',             'shsEscSigner',         'shs_esc_signed_at',             'ESC Certificate (for ESC Grantees from previous school)'],
                                    ['has_shs_envelope',        'shsEnvelopeSigner',    'shs_envelope_signed_at',        'One (1) Long Brown Envelope with Full Name on Top Left'],
                                    ['has_shs_folder',          'shsFolderSigner',      'shs_folder_signed_at',          'One (1) Long White Folder with Fastener'],
                                ];
                            @endphp

                            @foreach($requirementsList as [$field, $signer, $dateField, $label])
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <div class="status-box {{ $shsRequirements->$field ? 'bg-success' : 'bg-danger' }}"></div>
                                            <select class="form-select form-select-sm text-center status-select" name="{{ $field }}">
                                                <option value="1" {{ $shsRequirements->$field ? 'selected' : '' }}>Completed</option>
                                                <option value="0" {{ !$shsRequirements->$field ? 'selected' : '' }}>Pending</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($shsRequirements->$field && $shsRequirements->$signer)
                                            <small>{{ $shsRequirements->$signer->employee->firstname ?? '' }}
                                                   {{ $shsRequirements->$signer->employee->lastname ?? '' }}</small><br>
                                            <small class="text-muted">{{ optional($shsRequirements->$dateField)->format('M d, Y h:i A') }}</small>
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
