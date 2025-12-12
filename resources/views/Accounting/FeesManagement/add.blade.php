<!-- Add Fee Modal -->
<div class="modal fade" id="addFeeModal" tabindex="-1" aria-labelledby="addFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            @if(isset($selected_feesname) && in_array($selected_feesname->id, [1]))
                @include('Accounting.FeesManagement.Tuition.add') 
            @endif

            @if(isset($selected_feesname) && in_array($selected_feesname->id, [2, 3, 4]))
                @include('Accounting.FeesManagement.Other.add') 
            @endif

        </div>
    </div>
</div>

<!-- JS for Program Filter and Department Filter -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Program Type Filter ---
    const typeFilter = document.getElementById('program_type_filter');
    const programItems = document.querySelectorAll('#program-checkboxes .program-item');
    if(typeFilter) {
        function filterPrograms() {
            const selectedType = typeFilter.value;
            programItems.forEach(item => {
                item.style.display = (selectedType === "" || item.dataset.board === selectedType) ? 'block' : 'none';
            });
        }
        typeFilter.addEventListener('change', filterPrograms);
        filterPrograms();
    }

    // --- Academic Group -> Department Filter ---
    const acadSelect = document.getElementById('academicgroup_id');
    const deptSelect = document.getElementById('department_id');
    if(acadSelect && deptSelect) {
        function filterDepartments() {
            const selectedAcad = acadSelect.value;
            [...deptSelect.options].forEach(option => {
                if(option.value === "") { option.hidden = false; return; }
                option.hidden = option.getAttribute('data-acad') !== selectedAcad;
            });
            deptSelect.value = "";
        }
        acadSelect.addEventListener('change', filterDepartments);
        filterDepartments();
    }
});
</script>
