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
            <h1 class="h3 mb-0 text-gray-800">Edite Product </h1>
            {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
        </div>

        <!-- Content Row -->
        <div class="row w-100">           
            <form action="{{ route('products.update', $product->id) }}" method="POST" class="w-100" enctype="multipart/form-data">
                @csrf
                @method('PUT') 
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter product name" value="{{ $product->name }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="price">Product Price</label>
                        <input type="text" name="price" class="form-control" placeholder="Enter product price" value="{{ $product->price }}">
                    </div>
                </div>
            
                <div class="col-md-6 form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                <div class="col-md-12 form-group">
                    <label for="shortDescription">Short Description</label>
                    <textarea name="shortDescription" class="form-control" >{{ $product->shortDescription }}</textarea>
                </div>
            
                <div class="col-md-12 form-group">
                    <label for="longDescription">Long Description</label>
                    <textarea name="longDescription" class="form-control">{{ $product->longDescription }}</textarea>
                </div>
            
                <div class="col-md-12 form-group">
                    <input type="file" name="productImage" class="form-control-file">
                    <img src="{{ asset('images/' . $product->productImage) }}" width="20%" alt="Product Image">
                </div>
            
                <button type="submit" class="btn btn-primary">Update Product</button>
            </form>            
        </div>
    </div>
    <!-- /.container-fluid -->

@endsection