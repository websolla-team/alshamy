@php
    $footerPhoneHref = \App\Http\Controllers\Front\ContactLinks::telephone($Contact?->phone1);
@endphp

<footer class="alshamy-footer">
    <div class="container">
        <div class="alshamy-footer__row">
            <div class="alshamy-footer__item">
                <span>العنوان</span>
                <p>مدينة السادات، مول الزهور، المنطقة الخامسة</p>
            </div>
            @if ($footerPhoneHref)
                <div class="alshamy-footer__item">
                    <span>اطلب</span>
                    <p><a href="{{ $footerPhoneHref }}">{{ $Contact?->phone1 }}</a></p>
                </div>
            @endif
            @if ($Contact?->email)
                <div class="alshamy-footer__item">
                    <span>إيميل</span>
                    <p><a href="mailto:{{ $Contact->email }}">{{ $Contact->email }}</a></p>
                </div>
            @endif
        </div>

        <div class="alshamy-footer__bottom">
            <p>© {{ date('Y') }} الشامي — جميع الحقوق محفوظة</p>
            <nav aria-label="روابط الموقع">
                <a href="{{ route('about') }}">من نحن</a>
                <a href="{{ route('contacts') }}">تواصل معنا</a>
            </nav>
        </div>
    </div>
</footer>
