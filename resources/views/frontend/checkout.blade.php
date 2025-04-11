@extends('frontend.layout.app')

@section('content')

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Checkout</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">Checkout</li>
    </ol>
</div>
<!-- Single Page Header End -->

<!-- Checkout Page Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <h1 class="mb-4">Billing Details</h1>
        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="row g-5">
                <!-- Billing details fields -->
                <div class="col-md-12 col-lg-6 col-xl-7">
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <div class="form-item w-100">
                                <label class="form-label my-3">First Name<sup>*</sup></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="form-item w-100">
                                <label class="form-label my-3">Last Name<sup>*</sup></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Address <sup>*</sup></label>
                        <input type="text" name="address" class="form-control" placeholder="House Number Street Name" required>
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Town/City<sup>*</sup></label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Country<sup>*</sup></label>
                        <input type="text" name="country" class="form-control" required>
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Postcode/Zip<sup>*</sup></label>
                        <input type="text" name="postcode" class="form-control" required>
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Mobile<sup>*</sup></label>
                        <input type="tel" name="mobile" class="form-control" required>
                    </div>
                    <div class="form-item mb-4">
                        <label class="form-label my-3">Email Address<sup>*</sup></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-item">
                        <textarea name="order_notes" class="form-control" spellcheck="false" cols="30" rows="11" placeholder="Order Notes (Optional)"></textarea>
                    </div>
                </div>

                <div class="col-md-12 col-lg-6 col-xl-5">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Products</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(session('cart') && is_array(session('cart')) && count(session('cart')) > 0)
                                    @php
                                        $subtotal = 0; // Initialize subtotal
                                    @endphp
                                    @foreach(session('cart') as $id => $item)
                                        @php
                                            $itemTotal = $item['price'] * $item['quantity'];
                                            $subtotal += $itemTotal; // Calculate subtotal
                                        @endphp
                                        <tr>
                                            <th scope="row">
                                                <div class="d-flex align-items-center mt-2">
                                                    <img src="{{ asset('images/' . $item['image']) }}" class="img-fluid rounded-circle" style="width: 90px; height: 90px;" alt="">
                                                </div>
                                            </th>
                                            <td class="py-5">{{ $item['name'] }}</td>
                                            <td class="py-5">{{ $item['price'] }} kr</td>
                                            <td class="py-5">{{ $item['quantity'] }}</td>
                                            <td class="py-5">{{ $itemTotal }} kr</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-5">No items in the cart</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row"> <div class="card border-secondary mb-5">
                        <div class="card-header bg-secondary border-0">
                            <h4 class="font-weight-semi-bold m-0">Payment Method</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <button type="button" id="stripe-button" class="stripe-btn">
                                    <i class="fab fa-stripe"></i>
                                    Pay with Stripe
                                </button>
                            </div>
                        </div>
                    </div></div>
                    <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                        <button type="submit" class="order-btn">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Checkout Page End -->

@push('scripts')
<script>
    document.getElementById('checkout-form').addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Show loading state
        const stripeButton = document.getElementById('stripe-button');
        stripeButton.disabled = true;
        stripeButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Processing...
        `;
        
        // Submit form data to create checkout session
        fetch('{{ route("stripe.create.session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                formData: Object.fromEntries(new FormData(this))
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                window.location.href = data.url;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Reset button state on error
            stripeButton.disabled = false;
            stripeButton.innerHTML = `
                <i class="fab fa-stripe"></i>
                Pay with Stripe
            `;
        });
    });
</script>
@endpush

@endsection
