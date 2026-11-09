@extends('website.layouts.master')
@section('title')
    {{ __('main.site') }} | {{ __('main.contact us') }}
@endsection
@section('css')
@endsection
@section('content')
    @php
        $contactPagePhoneHref = \App\Http\Controllers\Front\ContactLinks::telephone($Contact?->phone1);
    @endphp

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
                            <p>مدينة السادات، مول الزهور، المنطقة الخامسة</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="contact-box style1">
                            <div class="contact-icon"><img src="{{ asset('website/assets/img/icon/gmail.png')}}" alt="icon"></div>
                            <h3 class="title">@if ($Contact?->email)<a href="mailto:{{ $Contact->email }}">{{ $Contact->email }}</a>@endif</h3>

                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="contact-box style1">
                            <div class="contact-icon"><img src="{{ asset('website/assets/img/icon/phone.png')}}" alt="icon"></div>
                            <h3 class="title">
                                @if ($contactPagePhoneHref)
                                    <a href="{{ $contactPagePhoneHref }}">{{ $Contact?->phone1 }}</a>
                                @else
                                    {{ $Contact?->phone1 }}
                                @endif
                            </h3>
                            <p>{{ $Contact?->phone2 }}</p>
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
                                @if ($errors->any())
                                    <p role="alert" class="text-danger">فيه بيانات ناقصة أو مش صحيحة. راجع الحقول وحاول تاني.</p>
                                @endif
                                <div class="col-md-12">
                                    <label class="visually-hidden" for="contact-name">{{ __('main.name') }}</label>
                                    <input id="contact-name" type="text" name="name" autocomplete="name" required
                                           value="{{ old('name') }}" placeholder="{{ __('main.name') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="visually-hidden" for="contact-email">{{ __('main.email') }}</label>
                                    <input id="contact-email" type="email" name="email" autocomplete="email" required
                                           value="{{ old('email') }}" placeholder="{{ __('main.email') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="visually-hidden" for="contact-phone">{{ __('main.phone') }}</label>
                                    <input id="contact-phone" type="tel" name="phone" autocomplete="tel" required
                                           value="{{ old('phone') }}" placeholder="{{ __('main.phone') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="visually-hidden" for="contact-subject">{{ __('main.subject') }}</label>
                                    <input id="contact-subject" type="text" name="subject" required
                                           value="{{ old('subject') }}" placeholder="{{ __('main.subject') }}">
                                </div>
                                <div class="col-12">
                                    <label class="visually-hidden" for="contact-message">{{ __('main.message') }}</label>
                                    <textarea id="contact-message" name="message" class="form-control" required
                                              placeholder="{{ __('main.message') }}" rows="5">{{ old('message') }}</textarea>
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


            <div class="container text-center py-4">
                <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode('مول الزهور، مدينة السادات، المنوفية') }}"
                   target="_blank" rel="noopener noreferrer">
                    ابحث عن مول الزهور بمدينة السادات على الخريطة
                </a>
            </div>
    </div>


    <!-- end/information -->
@endsection

