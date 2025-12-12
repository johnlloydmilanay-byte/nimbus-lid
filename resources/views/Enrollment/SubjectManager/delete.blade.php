<div class="modal fade" id="deleteSubjectModal-{{ $subj->id }}" tabindex="-1" aria-hidden="true">
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
                <form action="{{ route('enrollment.subjectmanager.destroy', $subj->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>

        </div>
    </div>
</div>