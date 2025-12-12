<div class="card-body table-responsive">
    @if($contents->count() > 0)
        <table class="table table-striped table-bordered">
            <thead>
                <tr class="text-center">
                    <th>Programs</th>
                    <th>Setup Type</th>
                    <th>Regular Rate</th>
                    <th>Major Rate</th>
                    <th>AR Account</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contents as $content)
                    <tr>
                        <td>{!! $content['programs'] !!}</td>
                        <td class="text-center align-middle">{{ $content['setup_type'] == 1 ? 'Per Unit' : 'Fixed' }}</td>
                        <td class="text-center align-middle">PHP {{ number_format($content['rate_regular'], 2) }}</td>
                        <td class="text-center align-middle">PHP {{ number_format($content['rate_major'], 2) }}</td>
                        <td class="text-center align-middle">{{ $content['ar_account'] }}</td>
                        <td class="text-center align-middle">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editFeeModal" data-id="{{ $content['id'] }}" data-setup-type="{{ $content['setup_type'] ?? '' }}" data-rate-regular="{{ $content['rate_regular'] ?? '' }}" data-rate-major="{{ $content['rate_major'] ?? '' }}" data-ar-account="{{ $content['ar_account'] ?? '' }}" data-gl-account="{{ $content['gl_account'] ?? '' }}" data-program-ids="{{ $content['program_ids'] ?? '' }}"></i> Edit </button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteFeeModal-{{ $content['id'] }}">Delete</button>
                                @include('accounting.feesmanagement.tuition.delete')
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning text-center mb-0">
            No {{ $selected_feesname->name ?? 'Tuition Fees' }} found for the selected parameters.
        </div>
    @endif
</div>

@include('accounting.feesmanagement.tuition.edit')