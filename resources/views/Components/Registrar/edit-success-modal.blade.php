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

      <h4 class="fw-bold mb-2">Student Update Successful!</h4><br>

      <a href="{{ route('registrar.studentmanagement.edit', $student->application_number) }}" class="btn btn-custom btn-lg w-100">Continue</a>
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