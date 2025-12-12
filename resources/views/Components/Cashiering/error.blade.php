<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center border-0 shadow-lg p-4" style="border-radius: 15px;">
        
      <!-- Icon 70px -->
      <div class="d-flex justify-content-center mb-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center" 
             style="width:70px; height:70px; background: #f8d7da;">
          <i class="bi bi-search text-danger fs-1"></i>
        </div>
      </div>

      <!-- Title & Message -->
      <h5 class="fw-bold text-danger">Application Number Not Found</h5>
      <p class="text-muted mb-4">The application number you entered does not exist in our records. Please check and try again.</p>

      <!-- Confirm Button -->
      <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal" style="border-radius: 8px;">
        Confirm
      </button>

    </div>
  </div>
</div>


<!-- JS to trigger error modal -->
{{-- <script>
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
                // Clear payor name and show error modal
                document.getElementById('payor_name').value = '';
                let errorModal = new bootstrap.Modal(document.getElementById('errorModal')); 
                errorModal.show();
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
});
</script> --}}
