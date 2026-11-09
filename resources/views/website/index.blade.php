@extends('website.layouts.master')
@section('title')
    {{ __('main.site') }} | {{ __('main.home') }}
@endsection

@section('content')
    @php
        $orderPhoneHref = \App\Http\Controllers\Front\ContactLinks::telephone($Contact?->phone1);
        $orderWhatsApp = \App\Http\Controllers\Front\ContactLinks::whatsapp($Contact?->whatsapp);
    @endphp

    <main id="menu-categories" class="alshamy-menu-page">
        <div class="container">
            <div class="alshamy-menu-intro">
                <h1>قائمة الشامي</h1>
                <p>اختار القسم وشوف الأصناف والأسعار بسهولة</p>
            </div>

            @if ($categories->isEmpty())
                <p class="alshamy-menu-empty">المنيو مش متاح حاليًا. تواصل معانا لمعرفة الأصناف المتوفرة.</p>
            @else
                <div class="food-menu-tab alshamy-menu">
                    <nav aria-label="أقسام المنيو" class="alshamy-categories">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            @foreach ($categories as $index => $category)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                            id="pills-{{ $category->id }}-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-{{ $category->id }}" type="button" role="tab"
                                            aria-controls="pills-{{ $category->id }}"
                                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                        {{ $category->title }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    <div class="tab-content" id="pills-tabContent">
                        @foreach ($categories as $index => $category)
                            <section class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                     id="pills-{{ $category->id }}" role="tabpanel"
                                     aria-labelledby="pills-{{ $category->id }}-tab" tabindex="0">
                                <h2 class="alshamy-category-title">{{ $category->title }}</h2>
                                @foreach ($addonsBySection->get($category->getTranslation('title', 'ar', false), collect()) as $addon)
                                    @if (count($addon['choices']) > 0)
                                        <aside class="alshamy-addon" aria-label="إضافة {{ $addon['name'] }}">
                                            <div class="alshamy-addon__intro">
                                                <span class="alshamy-addon__tag">إضافة للبيتزا</span>
                                                <strong>{{ $addon['name'] }}</strong>
                                                <p>إضافة اختيارية للبيتزا — السعر بيتضاف على سعر البيتزا الأساسي.</p>
                                            </div>
                                            <div class="alshamy-addon__prices" aria-label="أسعار الإضافة حسب الحجم">
                                                @foreach ($addon['choices'] as $choice)
                                                    <span><bdi>{{ $choice['size'] }} +{{ $choice['price'] }} جنيه</bdi></span>
                                                @endforeach
                                            </div>
                                        </aside>
                                    @endif
                                @endforeach
                                <div class="alshamy-products">
                                    @foreach ($category->products as $product)
                                        @php
                                            $firstVariant = $product->variants->first();
                                            $hasPrice = $firstVariant && (float) $firstVariant->price > 0;
                                            $showOldPrice = $hasPrice && (float) $firstVariant->old_price > (float) $firstVariant->price;
                                            $description = trim(strip_tags(html_entity_decode((string) $product->description)));
                                            $orderMessage = 'السلام عليكم، عايز أطلب ' . $product->name
                                                . ($firstVariant?->size ? ' - ' . $firstVariant->size : '') . '.';
                                        @endphp
                                        <article class="alshamy-menu-card" data-product-id="{{ $product->id }}">
                                            <div class="alshamy-menu-card__image">
                                                @if ($product->image)
                                                    <img src="{{ asset($product->image) }}"
                                                         alt="{{ $product->name }}" width="88" height="88"
                                                         loading="{{ $index === 0 && $loop->first ? 'eager' : 'lazy' }}"
                                                         @if ($index === 0 && $loop->first) fetchpriority="high" @endif
                                                         decoding="async">
                                                @else
                                                    <span class="alshamy-menu-card__no-image" aria-hidden="true">الشامي</span>
                                                @endif
                                            </div>
                                            <div class="alshamy-menu-card__content">
                                                <div class="alshamy-menu-card__heading">
                                                    <h3>{{ $product->name }}</h3>
                                                    @if ($product->variants->count() === 1 && $firstVariant?->size)
                                                        <span class="alshamy-menu-card__single-size">{{ $firstVariant->size }}</span>
                                                    @endif
                                                </div>

                                                @if ($description)
                                                    <p>{{ \Illuminate\Support\Str::limit($description, 100) }}</p>
                                                @endif

                                                @if ($product->variants->count() > 1)
                                                    <fieldset class="alshamy-size-picker">
                                                        <legend class="visually-hidden">اختر حجم {{ $product->name }}</legend>
                                                        @foreach ($product->variants as $variant)
                                                            <label class="alshamy-size-chip">
                                                                <input type="radio" name="size-{{ $product->id }}"
                                                                       value="{{ $variant->id }}"
                                                                       data-size="{{ $variant->size }}"
                                                                       data-price="{{ $variant->price }}"
                                                                       data-old-price="{{ $variant->old_price }}"
                                                                       {{ $loop->first ? 'checked' : '' }}>
                                                                <span>{{ $variant->size }}</span>
                                                            </label>
                                                        @endforeach
                                                    </fieldset>
                                                @endif

                                                <div class="alshamy-menu-card__bottom">
                                                    <div class="alshamy-menu-card__prices">
                                                        <strong class="alshamy-menu-card__price" aria-live="polite">
                                                            <bdi>{{ $hasPrice ? $firstVariant->price . ' جنيه' : 'السعر عند الطلب' }}</bdi>
                                                        </strong>
                                                        <del class="alshamy-menu-card__old-price"
                                                             @unless ($showOldPrice) hidden @endunless>
                                                            <bdi>@if ($showOldPrice){{ $firstVariant->old_price }} جنيه@endif</bdi>
                                                        </del>
                                                    </div>
                                                    <a class="alshamy-menu-card__order"
                                                       href="{{ $orderWhatsApp ? $orderWhatsApp . '?text=' . rawurlencode($orderMessage) : ($orderPhoneHref ?: route('contacts')) }}"
                                                       @if ($orderWhatsApp) data-whatsapp-base="{{ $orderWhatsApp }}"
                                                       data-product-name="{{ $product->name }}" target="_blank" rel="noopener noreferrer" @endif
                                                       aria-label="{{ $orderWhatsApp ? 'اطلب عبر واتساب' : ($orderPhoneHref ? 'اتصل لطلب' : 'تواصل لطلب') }} {{ $product->name }}">
                                                        {{ $orderWhatsApp ? 'اطلب عبر واتساب' : ($orderPhoneHref ? 'اتصل للطلب' : 'تواصل للطلب') }}
                                                        <i class="{{ $orderWhatsApp ? 'fa-brands fa-whatsapp' : 'fa-solid fa-phone' }}" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection

@section('js')
    <script>
        const categoryTabs = Array.from(document.querySelectorAll('#pills-tab [data-bs-toggle="pill"]'));
        const activateLinkedCategory = () => {
            const matchingTab = categoryTabs.find((tab) =>
                '#' + tab.getAttribute('aria-controls') === window.location.hash
            );
            if (matchingTab && window.bootstrap?.Tab) {
                window.bootstrap.Tab.getOrCreateInstance(matchingTab).show();
                matchingTab.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }
        };

        categoryTabs.forEach((tab) => {
            tab.addEventListener('shown.bs.tab', () => {
                const hash = '#' + tab.getAttribute('aria-controls');
                if (window.location.hash !== hash) {
                    window.history.replaceState(null, '', hash);
                }
                tab.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            });
        });
        activateLinkedCategory();
        window.addEventListener('hashchange', activateLinkedCategory);

        document.querySelectorAll('.alshamy-size-picker').forEach((picker) => {
            picker.addEventListener('change', (event) => {
                if (!event.target.matches('input[type="radio"]')) return;

                const card = event.target.closest('.alshamy-menu-card');
                const price = Number(event.target.dataset.price);
                const oldPrice = Number(event.target.dataset.oldPrice);
                const display = card.querySelector('.alshamy-menu-card__price bdi');
                const oldDisplay = card.querySelector('.alshamy-menu-card__old-price');
                const money = (amount) => amount.toLocaleString('en-US', { maximumFractionDigits: 2 }) + ' جنيه';

                display.textContent = Number.isFinite(price) && price > 0 ? money(price) : 'السعر عند الطلب';
                oldDisplay.hidden = !(Number.isFinite(oldPrice) && oldPrice > price && price > 0);
                oldDisplay.querySelector('bdi').textContent = oldDisplay.hidden ? '' : money(oldPrice);

                const orderLink = card.querySelector('[data-whatsapp-base]');
                if (orderLink) {
                    const size = event.target.dataset.size?.trim();
                    const message = 'السلام عليكم، عايز أطلب ' + orderLink.dataset.productName
                        + (size ? ' - ' + size : '') + '.';
                    orderLink.href = orderLink.dataset.whatsappBase + '?text=' + encodeURIComponent(message);
                }
            });
        });
    </script>
@endsection
