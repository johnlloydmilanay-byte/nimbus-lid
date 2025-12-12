<div class="modal fade" id="editInstallmentSchemeModal{{ $s->id }}" tabindex="-1" aria-labelledby="editInstallmentSchemeModalLabel{{ $s->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('accounting.installmentscheme.update', $s->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="year" value="{{ request('year') }}">
                <input type="hidden" name="term_id" value="{{ request('term_id') }}">
                <input type="hidden" name="academicgroup_id" value="{{ request('academicgroup_id') }}">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editInstallmentSchemeModalLabel{{ $s->id }}">
                        <i class="bi bi-pencil-square"></i> Edit {{ $title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Scheme Name</label>
                            <input type="text" class="form-control" name="scheme_name" value="{{ $s->scheme_name }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Count</label>
                            <select class="form-select" name="payment_count" required>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $s->payment_count == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Installment Fee</label>
                            <input type="number" class="form-control" name="installment_fee" value="{{ $s->installment_fee }}" required>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-custom">Save Changes</button>
                </div>

            </form>
        </div>
    </div>
</div>
