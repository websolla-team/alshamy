

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
                                    <p>{{ $Contact->address }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-start justify-content-lg-end">
                            <div class="fancy-box">
                                <div class="item1"><i class="fa-solid fa-envelope"></i></div>
                                <div class="item2">
                                    <h6>{{ __('main.send an email') }}</h6>
                                    <p>{{ $Contact->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-start justify-content-lg-end">
                            <div class="fancy-box">
                                <div class="item1"><i class="fa-regular fa-phone-volume"></i></div>
                                <div class="item2">
                                    <h6>{{ __('main.call us') }}</h6>
                                    <p>+{{ $Contact->phone1 }}</p>
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
                        © All Copyright 2025 by <a href="{{ route('home') }}">Elshamy</a> Created By <a href="https://websolla.com/en">WebSolla</a>
                    </p>

                </div>
            </div>
        </div>
    </footer>


