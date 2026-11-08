@extends('website.layouts.master')
@section('title')
    {{ __('main.site') }} | {{ $page->title }}
@endsection
@section('css')
    <style>
        .page-image {
            max-height: 400px;
            object-fit: contain;
        }

        .text ul,
        .text ol {
            padding: 0 30px;
            list-style: auto;
        }

        .text li {
            list-style: auto;
            margin-bottom: 13px;
            line-height: 1.5;
        }
    </style>
@endsection
@section('content')
    <!-- breadcrumb area start -->
    <!-- Breadcumb Section   S T A R T -->
    <div class="breadcumb-section">
        <div class="breadcumb-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcumb-content">
                            <h1 class="breadcumb-title">{{ __('main.about') }} </h1>
                            <ul class="breadcumb-menu">
                                <li><a href="{{ route('home') }}">{{ __('main.home') }}</a></li>
                                <li class="text-white">/</li>
                                <li class="active"> {{ $page->title }} </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <!-- Testimonial section  S T A R T -->
    <section class="testimonial-section fix section-padding">
        <div class="testimonial-wrapper style2">
            <div class="container">

                <div class="testimonial-wrap style2">
                    <div class="row gx-80 gy-5">
                        <div class="col-xl-7 order-2 order-xl-1">
                            <div class="title-area">
                                <div class="sub-title wow fadeInUp" data-wow-delay="0.5s">
                                    <img class="me-1" src="{{ asset('website/assets/img/icon/titleIcon.svg') }}" alt="icon">{{ $page->title }}<img
                                        class="ms-1" src="{{ asset('website/assets/img/icon/titleIcon.svg')}}" alt="icon">
                                </div>
                                <h2 class="title  wow fadeInUp" data-wow-delay="0.7s">
                                    {{ $page->title }}
                                </h2>
                                <p>{!!  $page->text !!}</p>
                            </div>




                        </div>
                        <div class="col-xl-5 order-1 order-xl-2">
                            <div class="testimonial-thumb">
                                <img src="{{ asset($page->image) }}" alt="thumb">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
