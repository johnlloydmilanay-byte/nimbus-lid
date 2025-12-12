<div class="modal fade" id="checklistModalJhs" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Requirements Checklist</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="requirementsForm" method="POST"
                      action="{{ route('registrar.studentmanagement.jhsRequirementUpdate', $jhsRequirements->application_number) }}">
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
                                    ['has_jhs_result',      'jhsResultSigner',      'jhs_result_signed_at',      'Photocopy of UST-Legazpi Junior High School Admission Test Result'],
                                    ['has_jhs_report_card', 'jhsReportCardSigner',  'jhs_report_card_signed_at', 'Original and Photocopy of Form 138 or Report Card'],
                                    ['has_jhs_good_moral',  'jhsGoodMoralSigner',   'jhs_good_moral_signed_at',  'Original copy of Certificate of Good Moral Character'],
                                    ['has_jhs_psa',         'jhsPsaSigner',         'jhs_psa_signed_at',         'Two (2) photocopies of PSA Birth Certificate (If born abroad, copy of valid Philippine Passport)'],
                                    ['has_jhs_pic',         'jhsPicSigner',         'jhs_pic_signed_at',         'Two (2) copies of 2x2 picture with white background and name tag'],
                                    ['has_jhs_income',      'jhsIncomeSigner',      'jhs_income_signed_at',      'Photocopy of Proof of Income (e.g. employment/indigency/ITR)'],
                                    ['has_jhs_envelope',    'jhsEnvelopeSigner',    'jhs_envelope_signed_at',    'One (1) Long Plastic Brown Envelope'],
                                    ['has_jhs_folder',      'jhsFolderSigner',      'jhs_folder_signed_at',      'One (1) Long Folder with fastener (Green/Blue/Yellow per program)'],
                                ];
                            @endphp

                            @foreach($requirementsList as [$field, $signer, $dateField, $label])
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <div class="status-box {{ $jhsRequirements->$field ? 'bg-success' : 'bg-danger' }}"></div>
                                            <select class="form-select form-select-sm text-center status-select" name="{{ $field }}">
                                                <option value="1" {{ $jhsRequirements->$field ? 'selected' : '' }}>Completed</option>
                                                <option value="0" {{ !$jhsRequirements->$field ? 'selected' : '' }}>Pending</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($jhsRequirements->$field && $jhsRequirements->$signer)
                                            <small>
                                                {{ $jhsRequirements->$signer->employee->firstname ?? '' }}
                                                {{ $jhsRequirements->$signer->employee->lastname ?? '' }}
                                            </small><br>
                                            <small class="text-muted">{{ optional($jhsRequirements->$dateField)->format('M d, Y h:i A') }}</small>
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
