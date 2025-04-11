@extends('backend.layout.app')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Order </h1>
        </div>

        <div class="row">
            <!-- DataTales Example -->
            <div class="card shadow w-100 mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primaryTwo">Order List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Info</th>
                                    <th>Products</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>
                                            <strong>Name:</strong> {{ $order->first_name }} {{ $order->last_name }}<br>
                                            <strong>Email:</strong> {{ $order->email }}<br>
                                            <strong>Address:</strong> {{ $order->address }}<br>
                                            <strong>Phone:</strong> {{ $order->mobile }}
                                        </td>
                                        <td>
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->items as $item)
                                                        <tr>
                                                            <td>
                                                                <img src="{{ asset('images/' . $item->product->productImage) }}" 
                                                                     alt="{{ $item->product->name }}"
                                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                            </td>
                                                            <td>{{ $item->product->name }}</td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>${{ number_format($item->price, 2) }}</td>
                                                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                       
                                        <td>
                                            <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}<br>
                                            <strong>Payment Status:</strong> 
                                            <span class="badge badge-{{ $order->payment_status === 'Paid' ? 'success' : 'warning' }}">
                                                {{ $order->payment_status }}
                                            </span>
                                            @if($order->transaction_id)
                                                <br>
                                                <strong>Transaction ID:</strong> {{ $order->transaction_id }}
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            {{-- <span class="badge badge-{{ $order->status === 'Pending' ? 'warning' : 'success' }}">
                                                {{ $order->status }}
                                            </span> --}}
                                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection

@push('styles')
<style>
    .table-sm {
        margin-bottom: 0;
    }
    .table-sm td, .table-sm th {
        padding: 0.3rem;
        font-size: 0.9rem;
    }
    .badge {
        padding: 0.5em 1em;
    }
    .badge-warning {
        background-color: #f6c23e;
        color: #fff;
    }
    .badge-success {
        background-color: #1cc88a;
        color: #fff;
    }
</style>
@endpush
