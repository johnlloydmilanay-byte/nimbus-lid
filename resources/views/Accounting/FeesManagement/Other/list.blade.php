@if(!in_array($selected_feesname->id, [4]))
    <div class="card-body table-responsive">
        @if(isset($contents) && $contents->count() > 0)
            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="text-center">
                        <th style="width: 20%;">Fee Name</th>
                        <th style="width: 20%;">Fee Type</th>
                        <th style="width: 15%;">Rate</th>
                        <th style="width: 25%;">Applies to</th>
                        <th style="width: 15%;">AR Account</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contents as $content)
                        <tr>
                            <td class="text-left align-middle">{{ $content['fee_name'] ?? '' }}</td>
                            <td class="text-left align-middle">{{ $content['fee_type_name'] ?? '' }}</td>
                            <td class="text-center align-middle">PHP {{ number_format($content['rate'] ?? 0, 2) }}</td>
                            @if(!in_array($selected_feesname->id, [2]))
                                <td class="text-left align-middle">{!! $content['others_applies_to'] ?? '' !!}</td>
                                <td class="text-center align-middle">{{ $content['others_ar_account'] ?? '' }}</td>
                            @else
                                <td class="text-center align-middle">{!! $content['others_studentstatus'] !!}</td>
                                <td class="text-center align-middle"></td>
                            @endif
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#editFeeModal"  data-id="{{ $content['id'] }}">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteFeeModal-{{ $content['id'] }}">Delete</button>
                                    @include('accounting.feesmanagement.other.delete')
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-warning text-center mb-0">
                No {{ $selected_feesname->name ?? 'Fees' }} found for the selected parameters.
            </div>
        @endif
    </div>
@else
    <div class="card-body table-responsive">
        @if(isset($contents) && $contents->count() > 0)
            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="text-center">
                        <th style="width: 20%;">Subject</th>
                        <th style="width: 15%;">Rate</th>
                        <th style="width: 15%;">Deposit</th>
                        <th style="width: 15%;">AR Account</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contents as $content)
                        <tr>
                            <!-- <td class="text-left align-middle">{{ $content['combined_subject'] ?? '' }}</td> -->
                            <td class="text-left align-middle">{!! $content['combined_subject'] ?? '' !!}</td>
                            <td class="text-center align-middle">PHP {{ number_format($content['rate'] ?? 0, 2) }}</td>
                            <td class="text-center align-middle">PHP {{ number_format($content['deposit'] ?? 0, 2) }}</td>
                            <td class="text-left align-middle">{{ $content['ar_account'] ?? '' }}</td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#editFeeModal"  data-id="{{ $content['id'] }}">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteFeeModal" data-id="{{ $content['id'] }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-warning text-center mb-0">
                No {{ $selected_feesname->name ?? 'Fees' }} found for the selected parameters.
            </div>
        @endif
    </div>
@endif
    
@include('accounting.feesmanagement.other.edit')