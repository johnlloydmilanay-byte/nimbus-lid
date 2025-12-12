@extends($layout)

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="card shadow-lg p-4 rounded-3" style="max-width: 450px; width: 100%;">

        <!-- Logo -->
        <div class="text-center mb-3">
            <img src="{{ asset('assets/img/new-ust-logo.png') }}" alt="UST Legazpi Logo" class="mb-2" style="width: 120px;">
            <h5 class="fw-bold mt-2">University of Santo Tomas - Legazpi</h5>
            <p class="text-dark mb-1">Campus Online Logs</p>
            <hr class="border-warning" style="opacity: 1; height: 2px; width: 100px; margin: auto;">
        </div>

        <!-- Form -->
        <form action="{{ route('elogs.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="first_name" class="form-label fw-semibold">First Name: <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control" placeholder="Enter First Name" required>
            </div>

            <div class="mb-3">
                <label for="middle_name" class="form-label fw-semibold">Middle Name: <span class="text-danger">*</span></label>
                <input type="text" name="middle_name" class="form-control" placeholder="Enter Middle Name">
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label fw-semibold">Last Name: <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control" placeholder="Enter Last Name" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label fw-semibold">Suffix (Jr., Sr., I, II, etc): <small class="text-muted">(Optional)</small></label>
                <input type="text" name="suffix" class="form-control" placeholder="Enter Suffix">
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label fw-semibold">Department: <span class="text-danger">*</span></label>
                <select class="form-select" name="department_id" required>
                    {{-- <option value="" disabled selected>Choose Department</option> --}}
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->code }} : {{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="purpose" class="form-label fw-semibold">Purpose: <span class="text-danger">*</label>
                 <select name="purpose" id="purpose" class="form-select" required onchange="toggleOtherPurpose(this)">
                    <option value="Admission Inquiry">Admission Inquiry</option>
                    <option value="Admission Follow-up">Admission Follow-up</option>
                    <option value="Others">Others (please specify)</option>
                </select>

                <!-- Hidden input for 'Others' -->
                <input type="text" name="other_purpose" id="other_purpose" 
                    class="form-control mt-2 d-none" 
                    placeholder="Please specify your purpose">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-custom">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Include Success Modal -->
@include('Components.CampusOnlineLogs.success-modal')

<!-- Auto-open modal if session exists -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('show_success_modal'))
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>

<script>
function toggleOtherPurpose(select) {
    const otherInput = document.getElementById('other_purpose');
    if (select.value === 'Others') {
        otherInput.classList.remove('d-none');
        otherInput.required = true;
    } else {
        otherInput.classList.add('d-none');
        otherInput.required = false;
        otherInput.value = '';
    }
}
</script>

@endsection
