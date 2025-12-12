<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4" style="border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
      
      <div class="d-flex justify-content-center">
          <dotlottie-wc src="{{ asset('json/check-success.json') }}"
                        style="width: 500px;"
                        autoplay>
          </dotlottie-wc>
      </div><br>

      <h3 class="fw-bold mb-2">New employee created!</h3>
      <p class="mb-2">Employee ID: <span class="fw-bold fs-5" id="employeeId">{{ session('user_id') }}</span></p><br>

      <a href="{{ route('hr.index') }}" class="btn btn-custom btn-lg w-100">Continue</a>
    </div>
  </div>
</div>