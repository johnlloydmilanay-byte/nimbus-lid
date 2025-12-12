<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Reservation Successful</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-4">
                <h5 class="fw-bold text-success">Your reservation has been submitted!</h5>

                <p class="mt-3 mb-0 fs-5">
                    <strong>Reference No:</strong><br>
                    <span class="text-dark fw-bold fs-4">
                        {{ session('success_reference') }}
                    </span>
                </p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success w-100" data-bs-dismiss="modal">Okay</button>
            </div>

        </div>
    </div>
</div>

@if (session('success_reference'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif