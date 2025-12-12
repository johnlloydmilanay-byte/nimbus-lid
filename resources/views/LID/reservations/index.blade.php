@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <h2 class="mt-4 fw-bold">Chemical Reservations</h2>
    
    <div class="card mt-4">
        <div class="card-body">
            <p>List of reservations will appear here.</p>
            
            <a href="{{ route('lid.reservations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Reservation
            </a>
        </div>
    </div>
</div>
@endsection