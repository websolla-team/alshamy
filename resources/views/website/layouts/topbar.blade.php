@php
    $contactHref = \App\Http\Controllers\Front\ContactLinks::telephone($Contact?->phone1);
    $whatsappUrl = \App\Http\Controllers\Front\ContactLinks::whatsapp($Contact?->whatsapp);
@endphp

<header class="alshamy-topbar">
    <div class="container alshamy-topbar__inner">
        <a class="alshamy-topbar__brand" href="{{ route('home') }}" aria-label="الشامي - الرئيسية">
            <img src="{{ asset('website/assets/img/logo/logo.png') }}" alt="الشامي" width="68" height="68">
        </a>

        <div class="alshamy-topbar__place">
            <span class="alshamy-topbar__city"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> مدينة السادات</span>
            <small>الشامي للدجاج والمشويات</small>
        </div>

        <nav class="alshamy-topbar__actions" aria-label="التواصل مع الشامي">
            @if ($contactHref)
                <a class="alshamy-topbar__action alshamy-topbar__action--call"
                   href="{{ $contactHref }}" aria-label="اتصل بالشامي">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i><span>اتصل</span>
                </a>
            @endif
            @if ($whatsappUrl)
                <a class="alshamy-topbar__action alshamy-topbar__action--whatsapp" href="{{ $whatsappUrl }}"
                   target="_blank" rel="noopener noreferrer" aria-label="تواصل مع الشامي على واتساب">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>واتساب</span>
                </a>
            @endif
            <a class="alshamy-topbar__action alshamy-topbar__action--menu"
               href="{{ route('home') }}#menu-categories" aria-label="انتقل للمنيو">
                <i class="fa-solid fa-utensils" aria-hidden="true"></i><span>المنيو</span>
            </a>
        </nav>
    </div>
</header>

<div class="mouse-cursor cursor-outer" aria-hidden="true"></div>
<div class="mouse-cursor cursor-inner" aria-hidden="true"></div>
<button type="button" id="back-top" class="back-to-top" aria-label="العودة لأعلى الصفحة">
    <i class="fa-regular fa-arrow-up" aria-hidden="true"></i>
</button>
