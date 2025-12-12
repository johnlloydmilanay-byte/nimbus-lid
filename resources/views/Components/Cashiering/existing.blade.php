<div class="modal fade" id="alreadyPaidModal" tabindex="-1" aria-labelledby="alreadyPaidModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center border-0 shadow-lg p-4" style="border-radius: 15px;">

      <!-- Icon 70px-->
      <div class="d-flex justify-content-center mb-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center" 
             style="width:70px; height:70px; background: #fff3cd;">
          <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
        </div>
      </div>

      <!-- Title & Message -->
      <h5 class="fw-bold text-warning">Applicant Already Paid</h5>
      <p class="text-muted mb-4">This applicant has already completed the payment and cannot be processed again.</p>

      <!-- Confirm Button -->
      <button type="button" class="btn btn-warning w-100" data-bs-dismiss="modal" style="border-radius: 8px;">
        Confirm
      </button>

    </div>
  </div>
</div>

<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    let appNo = document.getElementById('application_number').value;

    fetch("{{ route('cashiering.collections.search') }}?application_number=" + encodeURIComponent(appNo))
        .then(res => res.json())
        .then(data => {
            console.log("API Response:", data);

            if (data.success) {
                // Fill payor name if found
                document.getElementById('payor_name').value = data.payor_name;
            } else {
                // Clear payor name
                document.getElementById('payor_name').value = '';

                // Show Already Paid Modal
                if (data.already_paid) {
                    let paidModal = new bootstrap.Modal(document.getElementById('alreadyPaidModal'));
                    paidModal.show();
                } else {
                    // Show Error Modal
                    let errorModal = new bootstrap.Modal(document.getElementById('errorModal')); 
                    errorModal.show();
                }
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
});
</script>