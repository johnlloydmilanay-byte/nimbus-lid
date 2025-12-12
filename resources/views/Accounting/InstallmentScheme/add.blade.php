<!-- Add Subject Modal -->
<div class="modal fade" id="addInstallmentSchemeModal" tabindex="-1" aria-labelledby="addInstallmentSchemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('accounting.installmentscheme.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="year" value="{{ request('year') }}">
                <input type="hidden" name="term_id" value="{{ request('term_id') }}">
                <input type="hidden" name="academicgroup_id" value="{{ request('academicgroup_id') }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addInstallmentSchemeModalLabel">
                        <i class="bi bi-plus-circle"></i> Add New {{ $title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Scheme Name</label>
                            <input type="text" class="form-control" id="scheme_name" name="scheme_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Count</label>
                            <select class="form-select" id="payment_count" name="payment_count" required>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Installment Fee</label>
                            <input type="number" class="form-control" id="installment_fee" name="installment_fee" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-custom">Add {{ $title }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
