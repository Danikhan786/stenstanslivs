@extends('frontend.layout.app')

@section('content')

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Contact</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
            <li class="breadcrumb-item active text-white">Contact</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Contact Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center mx-auto" style="max-width: 700px;">
                            <h1 class="text-primary">Get in touch</h1>
                            {{-- <p class="mb-4">The contact form is currently inactive. Get a functional and working
                                contact form with Ajax & PHP in a few minutes. Just copy and paste the files, add a little
                                code and you're done. <a href="https://htmlcodex.com/contact-form">Download Now</a>.</p>
                            --}}
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="h-100 rounded">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d777.4148238091922!2d17.310757259237317!3d62.39112814094628!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4664675c942d9fc1%3A0xe6c3a40ba77ae0db!2sNybrogatan%205%2C%20852%2031%20Sundsvall%2C%20Sweden!5e0!3m2!1sen!2s!4v1743767531387!5m2!1sen!2s"
                                class="rounded w-100" style="height: 400px; allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    {{-- <div class="col-lg-7">
                        <form action="" class="">
                            <input type="text" class="w-100 form-control border-0 py-3 mb-4" placeholder="Your Name">
                            <input type="email" class="w-100 form-control border-0 py-3 mb-4"
                                placeholder="Enter Your Email">
                            <textarea class="w-100 form-control border-0 mb-4" rows="5" cols="10"
                                placeholder="Your Message"></textarea>
                            <button class="w-100 btn form-control border-secondary py-3 bg-white text-primary "
                                type="submit">Submit</button>
                        </form>
                    </div> --}}
                    <div class="col-lg-2"></div>
                    <div class="col-lg-8">
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-map-marker-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Address</h4>
                                <p class="mb-2">Nybrogatan 5 85231 Sundsvall</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-envelope fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Mail Us</h4>
                                <p class="mb-2">stenstanslivs2@gmail.com</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded bg-white">
                            <i class="fa fa-phone-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Telephone</h4>
                                <p class="mb-2">0734764138</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2"></div>

                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->


@endsection