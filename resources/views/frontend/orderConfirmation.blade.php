@extends('frontend.layout.app')

@section('content')

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Order Confirm</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Order Confirmation</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <h1 class="text-center">Order Successful!</h1>
            <p class="text-center">Thank you for your order. We will contact you shortly.</p>
            <div class="text-center">
                <a href="{{ route('index') }}" class="btn btn-primary">Return to Home</a>
            </div>
        </div>
    </div>
    <!-- Cart Page End -->
    
@endsection
