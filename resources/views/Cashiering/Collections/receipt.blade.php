@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Payment Information
        </div>
        <div class="card-body">
            <form>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                        <label for="or_number" class="form-label">OR Number</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="or_number" value="{{ $collection->or_number }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                        <label for="sy_term" class="form-label">School Year / Term</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="sy_term" value="{{ $collection->year }} - {{ $collection->year + 1 }} / {{ $collection->term->name }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold">Name of Payor</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="name" value="{{ $collection->payor_name }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                        <label for="payment_for" class="form-label">Payment for</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="payment_for" value="{{ $collection->paymentFor->name ?? '' }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                       <label for="amount_to_pay" class="form-label">Amount to pay</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="amount_to_pay" value="{{ $collection->amount_to_pay }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                       <label for="amount_tendered" class="form-label">Amount tendered</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="amount_tendered" value="{{ $collection->amount_tendered }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                       <label for="change" class="form-label">Change</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="change" value="{{ $collection->change }}" readonly>
                    </div>
                </div>
                 <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                       <label for="payment_type" class="form-label bg-light text-dark">Payment Type</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="payment_type" value="{{ $collection->paymentType->name ?? '' }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-2">
                       <label for="remarks" class="form-label">Remarks</label>
                    </div>
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control bg-light text-dark" id="remarks" value="{{ $collection->remarks }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-success">Print Receipt</button>
                    {{-- <a href="{{ route('payments.create') }}" class="btn btn-warning">New Transaction</a> --}}
                </div>
            </form>
        </div>
    </div>
</div>

@include('Components.Cashiering.successful')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('show_success_modal'))
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>

@endsection
