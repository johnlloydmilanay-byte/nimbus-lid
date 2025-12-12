@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mt-4 fw-bold">Collections</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Payment Details
        </div>
        <div class="card-body">

            <div class="row justify-content-center">
                <div class="col-12 col-md-10">

                    <form action="{{ route('cashiering.collections.store') }}" method="POST">
                        @csrf

                        <!-- Search Section -->
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold mb-0">Search Applicant</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="searchInput" class="form-control" 
                                    placeholder="Enter Application Number or Applicant Name" 
                                    name="search_input" required>
                            </div>
                            <div class="col-12 col-md-1">
                                <button type="button" id="searchBtn" class="btn btn-warning fw-semibold">Search</button>
                            </div>
                        </div>

                        <!-- Results dropdown -->
                        <div id="searchResults" class="mt-2 d-none">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Select Applicant:</h6>
                                    <div id="resultsList" class="list-group">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-2 border-warning">

                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Name of Payor</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <input type="text" id="payor_name" class="form-control bg-light text-dark" name="payor_name" readonly>
                                <input type="hidden" id="selected_application_number" name="application_number">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">School Year / Term</label>
                            </div>
                            <div class="col-12 col-md-5">
                                <select class="form-select" name="year" required>
                                    {!! App\UserClass\Tool::year_generator(5, 0) !!}
                                </select>
                            </div>
                            <div class="col-12 col-md-5">
                                <select class="form-select" name="term_id" required>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Payment Code Type</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <select class="form-select" id="paymentCodeSelect" name="payment_code_type_id" required>
                                    @foreach($paymentcodes as $code)
                                        <option value="{{ $code->id }}">{{ $code->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Payment For</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <select class="form-select" id="paymentForSelect" name="payment_for_id" required>
                                    @foreach($paymentfor as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Amount Due</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <input type="text" class="form-control bg-light text-dark" name="amount_due" readonly>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Amount to Pay</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <input type="number" class="form-control" name="amount_to_pay" placeholder="Enter Amount to Pay" required>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Amount Tendered</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <input type="number" class="form-control" name="amount_tendered" placeholder="Enter Amount Tendered" required>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Payment Type</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <select class="form-select" id="payment_type_id" name="payment_type_id" required>
                                    @foreach($paymenttype as $payment)
                                        <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-12 col-md-2">
                                <label class="form-label fw-semibold">Remarks</label>
                            </div>
                            <div class="col-12 col-md-10">
                                <textarea class="form-control" name="remarks" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="text-center g-3 mt-3">
                            <button type="submit" class="btn btn-warning px-4 fw-bold">Post Payment</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@component('Components.Cashiering.existing') @endcomponent
@component('Components.Cashiering.error') @endcomponent

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const searchResults = document.getElementById('searchResults');
    const resultsList = document.getElementById('resultsList');
    const payorName = document.getElementById('payor_name');
    const selectedAppNumber = document.getElementById('selected_application_number');

    searchBtn.addEventListener('click', function() {
        const searchTerm = searchInput.value.trim();

        if (!searchTerm) {
            alert('Please enter an application number or applicant name');
            return;
        }

        clearResults();

        fetch('{{ route("cashiering.collections.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                search_term: searchTerm
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.multiple_results) {
                    displayMultipleResults(data.applications);
                } else {
                    payorName.value = data.payor_name;
                    selectedAppNumber.value = data.application_number || searchTerm;
                    searchInput.value = data.application_number || searchTerm; 
                }
            } else {
                if (data.already_paid) {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while searching');
        });
    });

    function displayMultipleResults(applications) {
        resultsList.innerHTML = '';
        
        if (applications.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'list-group-item text-center text-muted';
            noResults.textContent = 'No results found';
            resultsList.appendChild(noResults);
        } else {
            applications.forEach(app => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${app.full_name}</strong><br>
                            <small class="text-muted">App No: ${app.application_number} | Level: ${app.level}</small>
                        </div>
                        <div>
                            <small class="text-primary">Select</small>
                        </div>
                    </div>
                `;
                
                item.addEventListener('click', function() {
                    payorName.value = app.full_name;
                    selectedAppNumber.value = app.application_number;
                    searchResults.classList.add('d-none');
                    searchInput.value = app.application_number;
                });
                
                resultsList.appendChild(item);
            });
        }
        
        searchResults.classList.remove('d-none');
    }

    function clearResults() {
        searchResults.classList.add('d-none');
        resultsList.innerHTML = '';
        payorName.value = '';
        selectedAppNumber.value = '';
    }

    // Clear results when user starts typing
    searchInput.addEventListener('input', function() {
        clearResults();
    });

    // Allow Enter key to trigger search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchBtn.click();
        }
    });
});
</script>

<!-- Payment Code Type Script -->
<script>
document.getElementById('paymentCodeSelect').addEventListener('change', function () {
    let paymentCodeId = this.value;
    let paymentForSelect = document.getElementById('paymentForSelect');

    // Show loading indicator
    paymentForSelect.innerHTML = '<option>Loading...</option>';

    fetch('{{ url('cashier/collections/paymentfor') }}/' + paymentCodeId)
        .then(response => response.json())
        .then(data => {
            console.log('Payment For data:', data); 
            paymentForSelect.innerHTML = '';

            if (data.length === 0) {
                let option = document.createElement('option');
                option.textContent = 'No available payments';
                paymentForSelect.appendChild(option);
            } else {
                data.forEach(item => {
                    let option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    paymentForSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching payment for:', error);
            paymentForSelect.innerHTML = '<option>Error loading data</option>';
        });
});
</script>

@endsection