@extends('components.ticket_portal.app')

@section('contents')
<div class="container">
    <div class="row">
        <div>
            <img src="{{ asset('public/admin-assets/img/tc_welcome.png') }}" class="img-fluid mx-auto d-block">
        </div>
        <div class="text-center">
            <h5 style="color:#a3a9b1;">Thank you for submitting your issue, our team will get right on it!</h5>
            <h5 style="color:#a3a9b1;">Your Ticket Number is {{$ticket_id ?? ''}}</h5>
            <?php
             $customer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
            ?>
            @if($customer)
                <a href="{{route('auth.customer.vehicle-ticket.create')}}" class="btn home-btn my-2">Back</a>
            @else
                <a href="{{route('admin.web.vehicle-ticket.create')}}" class="btn home-btn my-2">Back</a>
            @endif
        </div>
    </div>
</div>
@endsection