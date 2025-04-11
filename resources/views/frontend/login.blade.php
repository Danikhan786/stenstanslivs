@extends('frontend.layout.app')

@section('content')

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container mt-5 py-5">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <div class="p-5 text-center m-0 m-auto w-50">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                    </div>
                    <form class="user" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <input id="email" type="email"  placeholder="Enter Email Address..." class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                        </div>
                        <div class="form-group mt-4">

                            <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            {{-- <input type="password" class="form-control form-control-user"
                                id="exampleInputPassword" placeholder="Password"> --}}
                        </div>
                        {{-- <div class="form-group">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                <label class="custom-control-label" for="customCheck">Remember
                                    Me</label>
                            </div>
                        </div> --}}
                        <button type="submit" class="btn btn-primary mt-3 btn-user w-100 btn-block">
                            Login
                        </button>
                        {{-- <a href="index.html" class="btn btn-primary btn-user btn-block">
                            Login
                        </a> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart Page End -->
    
@endsection
