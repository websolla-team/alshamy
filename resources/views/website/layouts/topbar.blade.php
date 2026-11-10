@php
    $contactHref = \App\Http\Controllers\Front\ContactLinks::telephone($Contact?->phone1);
    $whatsappUrl = \App\Http\Controllers\Front\ContactLinks::whatsapp($Contact?->whatsapp);
    $isMenuPage = request()->routeIs('home');
@endphp

<header class="alshamy-topbar">
    <div class="container alshamy-topbar__inner">
        <a class="alshamy-topbar__brand" href="{{ route('home') }}" aria-label="الشامي - الرئيسية">
            <img src="{{ asset('website/assets/img/logo/logo.png') }}" alt="" width="40" height="40">
            <span class="alshamy-topbar__name">الشامي</span>
            <span class="alshamy-topbar__place">دجاج ومشويات · مدينة السادات</span>
        </a>

        <nav class="alshamy-topbar__actions" aria-label="التواصل مع الشامي">
            @if ($isMenuPage)
                <button type="button" class="alshamy-action alshamy-action--quiet alshamy-searchbtn"
                        data-search-toggle aria-expanded="false" aria-controls="alshamy-search-panel"
                        aria-label="ابحث في المنيو">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                </button>
            @endif

            @if ($whatsappUrl)
                @if ($contactHref)
                    <a class="alshamy-action alshamy-action--quiet" href="{{ $contactHref }}" aria-label="اتصل بالشامي">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    </a>
                @endif
                <a class="alshamy-action" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>اطلب على واتساب</span>
                </a>
            @elseif ($contactHref)
                <a class="alshamy-action" href="{{ $contactHref }}">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i><span>اطلب بالتليفون</span>
                </a>
            @endif
        </nav>
    </div>

    @if ($isMenuPage)
        <div class="alshamy-searchpanel" id="alshamy-search-panel" hidden>
            <div class="container">
                <div class="alshamy-search">
                    <label class="visually-hidden" for="alshamy-search-input">ابحث في المنيو</label>
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input id="alshamy-search-input" type="search" placeholder="ابحث في المنيو"
                           autocomplete="off" enterkeyhint="search">
                    <button type="button" class="alshamy-search__close" data-search-close
                            aria-label="إغلاق البحث">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</header>

<div class="mouse-cursor cursor-outer" aria-hidden="true"></div>
<div class="mouse-cursor cursor-inner" aria-hidden="true"></div>
<button type="button" id="back-top" class="back-to-top" aria-label="العودة لأعلى الصفحة">
    <i class="fa-regular fa-arrow-up" aria-hidden="true"></i>
</button>
