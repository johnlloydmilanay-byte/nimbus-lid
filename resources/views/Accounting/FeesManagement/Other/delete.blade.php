<div class="modal fade" id="deleteFeeModal-{{ $content['id'] }}" tabindex="-1" aria-labelledby="deleteFeeModalLabel-{{ $content['id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="mb-3">
                <div style="width: 60px; height: 60px; border: 2px solid #ff2e63; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                    <span style="color: #ff2e63; font-size: 30px;">&#10006;</span>
                </div>
            </div>

            <h4 class="modal-title mb-2">Are you sure?</h4>
            <p class="text-muted mb-4">Do you really want to delete these records? This process cannot be undone.</p>

            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <form action="{{ route('accounting.feesmanagement.destroy.post') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids[]" value="{{ $content['id'] }}">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>

        </div>
    </div>
</div>