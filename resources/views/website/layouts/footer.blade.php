

    @php
        $footerPhoneHref = \App\Http\Controllers\Front\ContactLinks::telephone($Contact?->phone1);
    @endphp
    <!-- Footer Section    S T A R T -->
    <footer class="footer-section bg-title fix">
        <div class="footer-widgets-wrapper">
            <div class="shape1 float-bob-y d-none d-xxl-block"><img src="{{asset('website/assets/img/shape/footerShape1_1.png')}}"
                    alt="shape"></div>
            <div class="shape2 d-none d-xxl-block"><img src="{{asset('website/assets/img/shape/footerShape1_2.png')}}" alt="shape"></div>
            <div class="shape3 d-none d-xxl-block"><img src="{{asset('website/assets/img/shape/footerShape1_3.png')}}" alt="shape"></div>
            <div class="shape4  d-none d-xxl-block"><img src="{{asset('website/assets/img/shape/footerShape1_4.png')}}" alt="shape"></div>
            <div class="container-fluid">
                <div class="footer-top">
                    <div class="row gy-4">
                        <div class="col-lg-4">
                            <div class="fancy-box">
                                <div class="item1"><i class="fa-solid fa-location-dot"></i></div>
                                <div class="item2">
                                    <h6>{{ __('main.address') }}</h6>
                                    <p>مدينة السادات، مول الزهور، المنطقة الخامسة</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-start justify-content-lg-end">
                            <div class="fancy-box">
                                <div class="item1"><i class="fa-solid fa-envelope"></i></div>
                                <div class="item2">
                                    <h6>{{ __('main.send an email') }}</h6>
                                    <p>
                                        @if ($Contact?->email)
                                            <a href="mailto:{{ $Contact->email }}">{{ $Contact->email }}</a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-start justify-content-lg-end">
                            <div class="fancy-box">
                                <div class="item1"><i class="fa-regular fa-phone-volume"></i></div>
                                <div class="item2">
                                    <h6>{{ __('main.call us') }}</h6>
                                    <p>
                                        @if ($footerPhoneHref)
                                            <a href="{{ $footerPhoneHref }}">{{ $Contact?->phone1 }}</a>
                                        @else
                                            {{ $Contact?->phone1 }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-wrapper d-flex align-items-center justify-content-between">
                    <p class="wow fadeInLeft" data-wow-delay=".3s">
                        © {{ date('Y') }} <a href="{{ route('home') }}">الشامي</a> — جميع الحقوق محفوظة
                    </p>
                    <nav class="alshamy-footer-links" aria-label="روابط الموقع">
                        <a href="{{ route('about') }}">من نحن</a>
                        <a href="{{ route('contacts') }}">تواصل معنا</a>
                    </nav>

                </div>
            </div>
        </div>
    </footer>


