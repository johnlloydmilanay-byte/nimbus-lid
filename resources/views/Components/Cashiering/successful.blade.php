<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-body text-center py-5">

        <div class="d-flex justify-content-center">
          <dotlottie-wc src="{{ asset('json/check-success.json') }}"
                        style="width: 500px;"
                        autoplay>
          </dotlottie-wc>
      </div><br>

        <h4 class="fw-bold text-success mt-3">Payment Successful!</h4>
      </div>

    </div>
  </div>
</div>
