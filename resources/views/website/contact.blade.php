@extends('website.layouts.master')
@section('title')
    {{ __('main.site') }} | {{ __('main.contact us') }}
@endsection
@section('css')
@endsection
@section('content')

 <div class="breadcumb-section">
        <div class="breadcumb-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcumb-content">
                            <h1 class="breadcumb-title">{{ __('main.contact us') }}</h1>
                            <ul class="breadcumb-menu">
                                 <li><a href="{{ route('home') }}">{{ __('main.home') }} </a></li>
                                <li class="text-white">/</li>
                                <li class="active">{{ __('main.contact us') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Contact Us Section    S T A R T -->
    <div class="contact-us-section section-padding fix">
        <div class="contact-box-wrapper style1">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="contact-box style1">
                            <div class="contact-icon"><img src="{{ asset('website/assets/img/icon/location.png')}}" alt="icon"></div>
                            <h3 class="title">{{ __('main.address') }}</h3>
                            <p>{{ $Contact->address }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="contact-box style1">
                            <div class="contact-icon"><img src="{{ asset('website/assets/img/icon/gmail.png')}}" alt="icon"></div>
                            <h3 class="title">{{ $Contact->email }}</h3>

                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="contact-box style1">
                            <div class="contact-icon"><img src="{{ asset('website/assets/img/icon/phone.png')}}" alt="icon"></div>
                            <h3 class="title">Hot: +{{ $Contact->phone1 }}</h3>
                            <p>{{ $Contact->phone2 }}.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="contact-box style1">
                            <div class="contact-icon"><img src="{{ asset('website/assets/img/icon/clock.png')}}" alt="icon"></div>
                            <h3 class="title">{{ __('main.open time') }}</h3>
                            <p>{{ $Contact->time }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Form Section    S T A R T -->
    <div class="contact-form-section section-padding pt-0 fix">
        <div class="contact-form-wrapper style2">
            <div class="container">
                <div class="row gx-60 gy-5">
                    <div class="col-xl-6">
                        <div class="contact-form-thumb">
                            <img src="{{ asset('website/assets/img/contact/contactThumb2_1.png')}}" alt="thumb">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="contact-form style2">
                            <h2>{{ __('main.get in touch') }}</h2>
                              <form action="{{ route('contact.mail') }}" method="POST">
                            @csrf
                                <div class="col-md-12">
                                    <input type="text" name="name" placeholder="{{ __('main.name') }}">
                                </div>
                                <div class="col-md-12">
                                    <input type="email" name="email" placeholder="{{ __('main.email') }}">
                                </div>
                                <div class="col-md-12">
                                    <input type="number"name="phone" placeholder="{{ __('main.phone') }}">
                                </div>
                                <div class="col-md-12">
                                 <input type="text"name="subject" placeholder="{{ __('main.subject') }}">
                                </div>
                                <div class="col-12">
                                    <textarea id="message" name="message"  class="form-control" placeholder="{{ __('main.message') }}"
                                        rows="5"></textarea>
                                </div>

                                <div class="col-12 form-group mb-0">
                                    <button class="theme-btn w-100">{{ __('main.send message') }}<i
                                            class="fa-sharp fa-regular fa-arrow-right-long bg-transparent text-white"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section    S T A R T -->
    <div class="map-wrapper" style="line-height: 0;">


            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d6903.536910543796!2d31.345772000000004!3d30.100816999999996!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1458151915f49835%3A0x40c63e255b5b3b35!2z2YjZitioINiz2YjZhNinIC0gd2ViIHNvbGxh!5e0!3m2!1sar!2seg!4v1765808756627!5m2!1sar!2seg"  loading="lazy"    height="550" style="border:0; width: 100%;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>


    <!-- end/information -->
@endsection
@section('js')

