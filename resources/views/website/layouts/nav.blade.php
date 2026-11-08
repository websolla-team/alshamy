
    <!-- Preloader Start -->
    <div id="preloader" class="preloader" dir="ltr">
        <div class="animation-preloader">
            <div class="spinner">
            </div>
            <div class="txt-loading">
                <span data-text-preloader="E" class="letters-loading">
                    E
                </span>
                <span data-text-preloader="L" class="letters-loading">
                    L
                </span>
                <span data-text-preloader="S" class="letters-loading">
                    S
                </span>
                <span data-text-preloader="H" class="letters-loading">
                    H
                </span>
                <span data-text-preloader="A" class="letters-loading">
                    A
                </span>
                <span data-text-preloader="M" class="letters-loading">
                    M
                </span>
                <span data-text-preloader="Y" class="letters-loading">
                    Y
                </span>

            </div>
            <p class="text-center">Elshamy</p>
        </div>
        <div class="loader">
            <div class="row">
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
            </div>
        </div>
    </div>

    <!--<< Mouse Cursor Start >>-->
    <div class="mouse-cursor cursor-outer"></div>
    <div class="mouse-cursor cursor-inner"></div>

    <!-- Back To Top Start -->
    <button id="back-top" class="back-to-top">
        <i class="fa-regular fa-arrow-up"></i>
    </button>

    <!-- Offcanvas Area Start -->
    <div class="fix-area" dir="rtl">
        <div class="offcanvas__info">
            <div class="offcanvas__wrapper">
                <div class="offcanvas__content">

                    <!-- Top -->
                    <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                        <div class="offcanvas__logo">
                            <a href="{{ route('home') }}">
                                <img src="{{asset('website/assets/img/logo/logo.png')}}" alt="شعار المطعم">
                            </a>
                        </div>
                        <div class="offcanvas__close">
                            <button>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Text -->
                    <p class="text d-none d-lg-block text-end">
                        الشامي مش مجرد مطعم،
                        دي بداية لكل اللي نفسك تاكله 😉
                    </p>

                    <!-- Mobile Menu -->
                    <div class="mobile-menu fix mb-3"></div>

                    <!-- Contact -->
                    <div class="offcanvas__contact text-end">
                        <h4>معلومات التواصل</h4>

                        <ul>
                            <li class="d-flex align-items-center  justify-content-flex-end">
                                <div class="offcanvas__contact-icon ms-15">
                                    <i class="fal fa-map-marker-alt"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a target="_blank" href="#">
                                        {{ $Contact->address }}
                                    </a>
                                </div>


                            </li>

                            <li class="d-flex align-items-center  justify-content-flex-end">
                                <div class="offcanvas__contact-icon ms-15">
                                    <i class="fal fa-envelope"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="mailto:{{ $Contact->email }}">
                                       {{ $Contact->email }}
                                    </a>
                                </div>


                            </li>

                            <li class="d-flex align-items-center  justify-content-flex-end">
                                <div class="offcanvas__contact-icon ms-15">
                                    <i class="fal fa-clock"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="#">
                                       {{ $Contact->time }}
                                    </a>
                                </div>


                            </li>

                            <li class="d-flex align-items-center  justify-content-flex-end">
                                <div class="offcanvas__contact-icon ms-15">
                                    <i class="far fa-phone"></i>
                                </div>


                                <div class="offcanvas__contact-text">
                                    <a href="tel:+{{ $Contact->phone1 }}">
                                       {{ $Contact->phone1 }}
                                    </a>
                                </div>



                            </li>
                        </ul>

                        <!-- Social -->
                        <div class="social-icon d-flex align-items-center justify-content-end">
                            <a href="{{ $Contact->facebook }}"><i class="fab fa-facebook-f"></i></a>
                            <a href="{{ $Contact->twitter }}"><i class="fab fa-twitter"></i></a>
                            <a href="{{ $Contact->youtube }}"><i class="fab fa-youtube"></i></a>
                            <a href="{{ $Contact->linkedin }}"><i class="fab fa-linkedin-in"></i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas__overlay"></div>

    <!-- Header Section Start -->
    <header class="header-section">

        <div class="container-fluid">
            <div class="main-header-wrapper">
                <div class="logo-image">
                    <a href="{{ route('home') }}">
                        <img src="{{asset('website/assets/img/logo/logo.png')}}" alt="img">
                    </a>
                </div>
                <div class="main-header-items">

                    <div id="header-sticky" class="header-1">
                        <div class="mega-menu-wrapper">
                            <div class="header-main">
                                <div class="logo">
                                    <a href="{{ route('home') }}" class="header-logo">
                                        <img src="{{asset('website/assets/img/logo/logo.png')}}" alt="logo-img">
                                    </a>
                                </div>
                                <div class="header-left" dir="rtl">
                                    <div class="mean__menu-wrapper">
                                        <div class="main-menu">
                                            <nav id="mobile-menu">
                                                <ul class="text-end">

                                                    <li class="has-dropdown active menu-thumb">
                                                        <a href="{{ route('home') }}">
                                                            المنيو
                                                        </a>
                                                    </li>

                                                    <li class="has-dropdown">
                                                        <a href="{{ route('about') }}">
                                                            من نحن
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="{{ route('contacts') }}">
                                                            تواصل معنا
                                                        </a>
                                                    </li>

                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>

                                <div class="header-right d-flex justify-content-end align-items-center">




                                    <div class="header__hamburger d-xl-block my-auto">
                                        <div class="sidebar__toggle">
                                            <i class="fas fa-bars"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

