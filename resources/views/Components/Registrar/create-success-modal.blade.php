<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>

<div class="modal fade" id="successSaveModal" tabindex="-1" aria-labelledby="successSaveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4" style="border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
      
      <div class="d-flex justify-content-center">
        <dotlottie-wc 
          src="{{ asset('json/check-success.json') }}"
          style="width: 300px;"
          autoplay>
        </dotlottie-wc>
      </div><br>

      <h3 class="fw-bold mb-2">Submission Successful!</h3><br>

      <p class="text-muted mb-4" style="font-size: 0.95rem;">
        The student’s personal data has been securely saved to the system. You can review, edit, or add more details anytime from the student management dashboard.
      </p>

      <a href="{{ route('registrar.studentmanagement.index') }}" class="btn btn-custom btn-lg w-100">Continue</a>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
      @if(session('show_success_save_modal'))
          var successSaveModal = new bootstrap.Modal(document.getElementById('successSaveModal'));
          successSaveModal.show();
      @endif
  });
</script>