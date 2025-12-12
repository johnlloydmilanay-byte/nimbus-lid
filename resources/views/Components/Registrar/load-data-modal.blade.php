<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4" style="border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
      
      <div class="d-flex justify-content-center">
          <dotlottie-wc src="{{ asset('json/check-success.json') }}"
                        style="width: 500px;"
                        autoplay>
          </dotlottie-wc>
      </div><br>

      <h4 class="fw-bold mb-2">Student Data Loaded Successfully!</h4><br>

      <button type="button" class="btn btn-custom btn-lg w-100" data-bs-dismiss="modal">OK</button>

    </div>
  </div>
</div>