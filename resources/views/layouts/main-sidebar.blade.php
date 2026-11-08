<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('dashboard') }}"><img
                src="{{ URL::asset('assets/img/brand/logo-solla.png') }}" class="main-logo" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('dashboard') }}"><img
                src="{{ URL::asset('assets/img/brand/favicon-solla.png') }}" class="logo-icon" alt="logo"></a>
    </div>
    <div class="main-sidemenu">
        <ul class="side-menu">

            {{-- dashboard --}}
            <li class="slide pt-1">
                <a class="side-menu__item" href="{{ route('dashboard') }}"><svg xmlns="http://www.w3.org/2000/svg"
                        class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3" />
                        <path
                            d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
                    </svg><span class="side-menu__label">Dashboard</span></a>
            </li>
            <li class="side-item side-item-category">General</li>

            {{-- sliders --}}
            {{-- <li class="slide">
                <a class="side-menu__item" href="{{ route('sliders.index') }}"><svg xmlns="http://www.w3.org/2000/svg"
                        class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8c.28 0 .5-.22.5-.5 0-.16-.08-.28-.14-.35-.41-.46-.63-1.05-.63-1.65 0-1.38 1.12-2.5 2.5-2.5H16c2.21 0 4-1.79 4-4 0-3.86-3.59-7-8-7zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 10 6.5 10s1.5.67 1.5 1.5S7.33 13 6.5 13zm3-4C8.67 9 8 8.33 8 7.5S8.67 6 9.5 6s1.5.67 1.5 1.5S10.33 9 9.5 9zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 6 14.5 6s1.5.67 1.5 1.5S15.33 9 14.5 9zm4.5 2.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5z"
                            opacity=".3" />
                        <path
                            d="M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10c1.38 0 2.5-1.12 2.5-2.5 0-.61-.23-1.21-.64-1.67-.08-.09-.13-.21-.13-.33 0-.28.22-.5.5-.5H16c3.31 0 6-2.69 6-6 0-4.96-4.49-9-10-9zm4 13h-1.77c-1.38 0-2.5 1.12-2.5 2.5 0 .61.22 1.19.63 1.65.06.07.14.19.14.35 0 .28-.22.5-.5.5-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.14 8 7c0 2.21-1.79 4-4 4z" />
                        <circle cx="6.5" cy="11.5" r="1.5" />
                        <circle cx="9.5" cy="7.5" r="1.5" />
                        <circle cx="14.5" cy="7.5" r="1.5" />
                        <circle cx="17.5" cy="11.5" r="1.5" />
                    </svg><span class="side-menu__label">Sliders</span></a>
            </li> --}}

            {{-- info --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ route('informations.index') }}">
                    <i style="height: unset; color:#5b6e88" class="icon ion-md-paper side-menu__icon"></i>
                    <span class="side-menu__label">Advertising bar</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('informations.index') }}">Advertisings List</a></li>
                    <li><a class="slide-item" href="{{ route('informations.create') }}">Add Advertising</a></li>
                </ul>
            </li> --}}

            {{-- pages --}}
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                        enable-background="new 0 0 24 24" class="side-menu__icon" viewBox="0 0 24 24">
                        <g>
                            <rect fill="none" />
                        </g>
                        <g>
                            <g />
                            <g>
                                <path
                                    d="M21,5c-1.11-0.35-2.33-0.5-3.5-0.5c-1.95,0-4.05,0.4-5.5,1.5c-1.45-1.1-3.55-1.5-5.5-1.5S2.45,4.9,1,6v14.65 c0,0.25,0.25,0.5,0.5,0.5c0.1,0,0.15-0.05,0.25-0.05C3.1,20.45,5.05,20,6.5,20c1.95,0,4.05,0.4,5.5,1.5c1.35-0.85,3.8-1.5,5.5-1.5 c1.65,0,3.35,0.3,4.75,1.05c0.1,0.05,0.15,0.05,0.25,0.05c0.25,0,0.5-0.25,0.5-0.5V6C22.4,5.55,21.75,5.25,21,5z M3,18.5V7 c1.1-0.35,2.3-0.5,3.5-0.5c1.34,0,3.13,0.41,4.5,0.99v11.5C9.63,18.41,7.84,18,6.5,18C5.3,18,4.1,18.15,3,18.5z M21,18.5 c-1.1-0.35-2.3-0.5-3.5-0.5c-1.34,0-3.13,0.41-4.5,0.99V7.49c1.37-0.59,3.16-0.99,4.5-0.99c1.2,0,2.4,0.15,3.5,0.5V18.5z" />
                                <path
                                    d="M11,7.49C9.63,6.91,7.84,6.5,6.5,6.5C5.3,6.5,4.1,6.65,3,7v11.5C4.1,18.15,5.3,18,6.5,18 c1.34,0,3.13,0.41,4.5,0.99V7.49z"
                                    opacity=".3" />
                            </g>
                            <g>
                                <path
                                    d="M17.5,10.5c0.88,0,1.73,0.09,2.5,0.26V9.24C19.21,9.09,18.36,9,17.5,9c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,10.69,16.18,10.5,17.5,10.5z" />
                                <path
                                    d="M17.5,13.16c0.88,0,1.73,0.09,2.5,0.26V11.9c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,13.36,16.18,13.16,17.5,13.16z" />
                                <path
                                    d="M17.5,15.83c0.88,0,1.73,0.09,2.5,0.26v-1.52c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,16.02,16.18,15.83,17.5,15.83z" />
                            </g>
                        </g>
                    </svg><span class="side-menu__label">Pages</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('pages.index') }}">Pages List</a></li>
                    <li><a class="slide-item" href="{{ route('contact') }}">Contact</a></li>
                    <li><a class="slide-item" href="{{ route('social') }}">Social Media</a></li>
                </ul>
            </li>

            {{-- banners --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fe fe-star side-menu__icon"></i>
                    <span class="side-menu__label">Banners</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('banners.index') }}">Banners List</a></li>
                    <li><a class="slide-item" href="{{ route('banners.create') }}">Add Banner</a></li>
                </ul>
            </li> --}}

            {{-- category types --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="si si-list side-menu__icon"></i>
                    <span class="side-menu__label">Categories Types</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('category-types.index') }}">Category Types List</a></li>
                    <li><a class="slide-item" href="{{ route('category-types.create') }}">Add Category Type</a></li>
                </ul>
            </li> --}}

            {{-- categories --}}
           <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="si si-layers side-menu__icon"></i>
                    <span class="side-menu__label">Categories</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('categories.index') }}">Categories List</a></li>
                    <li><a class="slide-item" href="{{ route('categories.create') }}">Add Category</a></li>
                </ul>
            </li> 

            {{-- brands --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="icon ion-md-pricetags side-menu__icon"></i>
                    <span class="side-menu__label">Brands</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('brands.index') }}">Brands List</a></li>
                    <li><a class="slide-item" href="{{ route('brands.create') }}">Add Brand</a></li>
                </ul>
            </li> --}}

            {{-- promo codes --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fa fa-tag side-menu__icon"></i>
                    <span class="side-menu__label">Promo Codes</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('promocodes.index') }}">Promo Codes List</a></li>
                    <li><a class="slide-item" href="{{ route('promocodes.create') }}">Add Promo Code</a></li>
                </ul>
            </li> --}}

            {{-- products --}}
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fe fe-cpu side-menu__icon"></i>
                    <span class="side-menu__label">Products</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('products.index') }}">Products List</a></li>
                    <li><a class="slide-item" href="{{ route('products.create') }}">Add Product</a></li>
                    {{-- <li><a class="slide-item" href="{{ route('product.createMultiple') }}">Add Multiple Products</a>
                    </li> --}}
                </ul>
            </li>
            {{-- offers --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fe fe-star side-menu__icon"></i>
                    <span class="side-menu__label">Offers</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('offers.index') }}">Offers List</a></li>
                    <li><a class="slide-item" href="{{ route('offers.create') }}">Add Offers </a>
                    </li>
                </ul>
            </li> --}}

            {{-- orders --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fe fe-shopping-cart side-menu__icon"></i>
                    <span class="side-menu__label">Orders</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('orders.index') }}">Orders List</a></li>
                </ul>
            </li> --}}

            {{-- users --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fe fe-users side-menu__icon"></i>
                    <span class="side-menu__label">Clients</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('users.index') }}">Clients List</a></li>
                </ul>
            </li> --}}

            {{-- colors --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fe fe-aperture side-menu__icon"></i>
                    <span class="side-menu__label">Extra</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('extra.index') }}">Extra List</a></li>
                    <li><a class="slide-item" href="{{ route('extra.create') }}">Add Extra</a></li>
                </ul>
            </li> --}}

            {{-- sizes --}}
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="ti-ruler-alt side-menu__icon"></i>
                    <span class="side-menu__label">Sizes</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('sizes.index') }}">Sizes List</a></li>
                    <li><a class="slide-item" href="{{ route('sizes.create') }}">Add Size</a></li>
                </ul>
            </li>

            {{-- deliveries --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fa fa-map-marker-alt side-menu__icon"></i>
                    <span class="side-menu__label">Deliveries</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('deliveries.index') }}">Deliveries List</a></li>
                    <li><a class="slide-item" href="{{ route('deliveries.create') }}">Add Delivery</a></li>
                </ul>
            </li> --}}

            {{-- reviews --}}
            {{-- <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="fa fa-comment-dots side-menu__icon"></i>
                    <span class="side-menu__label">Reviews</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('reviews.index') }}">Reviews List</a></li>
                </ul>
            </li> --}}

            {{-- messages --}}
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
                        <path
                            d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
                    </svg><span class="side-menu__label">Messages</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('messages.index') }}">Messages List</a></li>
                </ul>
            </li>

            {{-- subscription --}}
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="typcn typcn-mail side-menu__icon"></i>
                    <span class="side-menu__label">Subscriptions</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('newsletters.index') }}">Subscriptions List</a></li>
                </ul>
            </li>

            {{-- settings --}}
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i style="height: unset; color:#5b6e88" class="icon ion-ios-settings side-menu__icon"></i> <span
                        class="side-menu__label">Settings</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('settings.index') }}">Main Settings</a></li>
                </ul>
            </li>

            <li class="slide mt-4 pt-4">
            </li>

        </ul>
    </div>
</aside>
<!-- main-sidebar -->
