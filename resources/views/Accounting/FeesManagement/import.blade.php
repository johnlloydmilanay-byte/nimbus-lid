<div class="modal fade" id="importFeeModal" tabindex="-1" aria-labelledby="importFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"> 	
            <div class="modal-header">
                <h5 class="modal-title" id="importFeeModalLabel">
                    <i class="fa fa-download"></i> Import {{ $selected_feesname->name }} to SY {{ $year.'-'.($year + 1) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            @if($selected_feesname->id == 1)
            <!-- Tuition Fees Import Form -->
            <form class="validate-form" method="post" action="{{ route('fees.import.tuition', ['year' => $year, 'department_id' => $department_id ?? request('department_id')]) }}">
            @else
            <!-- Other Fees Import Form -->
            <form class="validate-form" method="post" action="{{ route('fees.import.post', $year) }}">
            @endif
            
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="selected_feesname_id" value="{{ $selected_feesname->id }}">
                    <input type="hidden" name="term" value="{{ request('term') }}">
                    <input type="hidden" name="academicgroup_id" value="{{ request('academicgroup_id') }}">
                    <input type="hidden" name="department_id" value="{{ $department_id ?? request('department_id') }}">
                    
                    <div class="form-group">
                        <label for="year_source" class="form-label">Source School Year</label>
                        <select class="form-control" id="year_source" name="year_source" required>
                            @php
                                $selectedSourceYear = request('year_source', old('year_source'));
                            @endphp
                            {!! App\UserClass\Tool::year_generator(5, $selectedSourceYear) !!}
                        </select>
                        <div class="form-text">
                            Select the school year you want to import data from
                        </div>
                    </div>
                    
                    @if($selected_feesname->id == 1)
                    <div class="alert alert-info">
                        <small>
                            <i class="fa fa-info-circle"></i> 
                            This will import tuition fees from the selected source school year 
                            for the current department ({{ $departments->firstWhere('id', $department_id)->code ?? 'N/A' }})
                            to SY {{ $year.'-'.($year + 1) }}.
                        </small>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <small>
                            <i class="fa fa-info-circle"></i> 
                            This will import all {{ strtolower($selected_feesname->name) }} from the selected source school year 
                            and term to SY {{ $year.'-'.($year + 1) }}.
                            @if(request('academicgroup_id'))
                                <br>Academic Group: {{ $academicgroup->firstWhere('id', request('academicgroup_id'))->name ?? 'N/A' }}
                            @endif
                            @if(request('department_id'))
                                <br>Department: {{ $departments->firstWhere('id', request('department_id'))->code ?? 'N/A' }}
                            @endif
                        </small>
                    </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-download"></i> Import {{ $selected_feesname->name }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>