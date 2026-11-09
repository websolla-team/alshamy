@php
    $contactPhone = preg_replace('/[^\d+]/', '', $Contact?->phone1 ?? '');
    $whatsappValue = trim($Contact?->whatsapp ?? '');
    $whatsappUrl = null;
    if (preg_match('~^https://(?:wa\.me/|api\.whatsapp\.com/send(?:\?|/))~i', $whatsappValue)) {
        $whatsappUrl = $whatsappValue;
    } elseif (preg_match('/^\+?[\d\s()-]{9,20}$/', $whatsappValue)) {
        $whatsappDigits = preg_replace('/\D/', '', $whatsappValue);
        if (str_starts_with($whatsappDigits, '0')) {
            $whatsappDigits = '20' . substr($whatsappDigits, 1);
        }
        $whatsappUrl = 'https://wa.me/' . $whatsappDigits;
    }
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
            @if ($contactPhone)
                <a class="alshamy-topbar__action alshamy-topbar__action--call"
                   href="tel:{{ $contactPhone }}" aria-label="اتصل بالشامي">
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
