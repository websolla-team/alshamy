@extends('website.layouts.master')
@section('title')
    {{ __('main.site') }} | {{ __('main.home') }}
@endsection

@section('content')
    @php
        $orderPhoneHref = \App\Http\Controllers\Front\ContactLinks::telephone($Contact?->phone1);
        $orderWhatsApp = \App\Http\Controllers\Front\ContactLinks::whatsapp($Contact?->whatsapp);
    @endphp

    <main id="menu-categories" class="alshamy-page">
        <div class="container">
            <div class="alshamy-layout">
                <div class="alshamy-main">
                    <header class="alshamy-hero">
                        <h1>المنيو</h1>
                        <p>اختار القسم، ودوس على أي صنف للطلب</p>
                    </header>

                    @if ($categories->isEmpty())
                        <p class="alshamy-empty">المنيو مش متاح حاليًا. تواصل معانا لمعرفة الأصناف المتوفرة.</p>
                    @else
                        <p class="visually-hidden" role="status" aria-live="polite" data-search-status></p>

                        <nav class="alshamy-cats nav" aria-label="أقسام المنيو">
                            @foreach ($categories as $index => $category)
                                <button class="alshamy-cat nav-link {{ $index === 0 ? 'active' : '' }}"
                                        id="pills-{{ $category->id }}-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-{{ $category->id }}" type="button" role="tab"
                                        aria-controls="pills-{{ $category->id }}"
                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                    {{ $category->menu_title }}
                                    <b>{{ $category->products->count() }}</b>
                                </button>
                            @endforeach
                        </nav>

                        <div class="tab-content" id="pills-tabContent">
                            @foreach ($categories as $index => $category)
                                <section class="alshamy-pane tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                         id="pills-{{ $category->id }}" role="tabpanel"
                                         aria-labelledby="pills-{{ $category->id }}-tab" tabindex="0">
                                    <div class="alshamy-pane__head">
                                        <h2>{{ $category->menu_title }}</h2>
                                        <span class="alshamy-pane__count">{{ $category->products->count() }} صنف</span>
                                    </div>

                                    @foreach ($addonsBySection->get($category->getTranslation('title', 'ar', false), collect()) as $addon)
                                        @if (count($addon['choices']) > 0)
                                            <aside class="alshamy-addon" aria-label="إضافة {{ $addon['name'] }}">
                                                <div>
                                                    <span class="alshamy-addon__title">{{ $addon['name'] }}</span>
                                                    <p class="alshamy-addon__note">إضافة اختيارية للبيتزا، السعر بيتضاف على سعر البيتزا.</p>
                                                </div>
                                                <span class="alshamy-addon__prices">
                                                    {{ collect($addon['choices'])->map(fn ($choice) => $choice['size'] . ' +' . $choice['price'] . ' جنيه')->implode(' · ') }}
                                                </span>
                                            </aside>
                                        @endif
                                    @endforeach

                                    <div class="alshamy-list">
                                        @foreach ($category->products as $product)
                                            @php
                                                $firstVariant = $product->variants->first();
                                                $hasPrice = $firstVariant && (float) $firstVariant->price > 0;
                                                $showOldPrice = $hasPrice && (float) $firstVariant->old_price > (float) $firstVariant->price;
                                                $description = trim(strip_tags(html_entity_decode((string) $product->description)));
                                                $imagePath = trim((string) $product->image);
                                                // The dashboard stores this path when no photo was uploaded.
                                                $hasImage = $imagePath !== '' && $imagePath !== '/' && ! str_contains($imagePath, 'logo/logo.png');
                                                $orderMessage = 'السلام عليكم، عايز أطلب ' . $product->name
                                                    . ($firstVariant?->size ? ' - ' . $firstVariant->size : '') . '.';
                                                $searchText = $product->name . ' ' . $product->variants->pluck('size')->implode(' ');
                                            @endphp
                                            <article class="alshamy-row {{ $hasImage ? 'alshamy-row--with-image' : '' }}"
                                                     data-product-id="{{ $product->id }}"
                                                     data-search="{{ $searchText }}">
                                                @if ($hasImage)
                                                    <div class="alshamy-row__thumb">
                                                        <img src="{{ asset($imagePath) }}" alt=""
                                                             width="64" height="64"
                                                             loading="{{ $index === 0 && $loop->first ? 'eager' : 'lazy' }}"
                                                             @if ($index === 0 && $loop->first) fetchpriority="high" @endif
                                                             decoding="async">
                                                    </div>
                                                @endif

                                                <div class="alshamy-row__main">
                                                    <div class="alshamy-row__title">
                                                        <h3>
                                                            <a class="alshamy-row__link"
                                                               href="{{ $orderWhatsApp ? $orderWhatsApp . '?text=' . rawurlencode($orderMessage) : ($orderPhoneHref ?: route('contacts')) }}"
                                                               @if ($orderWhatsApp) data-whatsapp-base="{{ $orderWhatsApp }}"
                                                               data-product-name="{{ $product->name }}" target="_blank" rel="noopener noreferrer" @endif
                                                               aria-label="{{ $orderWhatsApp ? 'اطلب عبر واتساب' : ($orderPhoneHref ? 'اتصل لطلب' : 'تواصل لطلب') }} {{ $product->name }}">{{ $product->name }}</a>
                                                        </h3>
                                                        @if ($product->variants->count() === 1 && $firstVariant?->size)
                                                            <span class="alshamy-row__size">{{ $firstVariant->size }}</span>
                                                        @endif
                                                    </div>

                                                    @if ($description)
                                                        <p class="alshamy-row__desc">{{ $description }}</p>
                                                    @endif

                                                    @if ($product->variants->count() > 1)
                                                        <fieldset class="alshamy-sizes">
                                                            <legend class="visually-hidden">اختر حجم {{ $product->name }}</legend>
                                                            @foreach ($product->variants as $variant)
                                                                <label class="alshamy-size">
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
                                                </div>

                                                <div class="alshamy-row__prices">
                                                    <strong class="alshamy-row__price" aria-live="polite">
                                                        @if ($hasPrice)
                                                            <bdi>{{ $firstVariant->price }}<small>جنيه</small></bdi>
                                                        @else
                                                            <bdi>السعر عند الطلب</bdi>
                                                        @endif
                                                    </strong>
                                                    <del class="alshamy-row__old" @unless ($showOldPrice) hidden @endunless>
                                                        <bdi>@if ($showOldPrice){{ $firstVariant->old_price }} جنيه@endif</bdi>
                                                    </del>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>

                        <p class="alshamy-no-results" hidden>مفيش صنف بالاسم ده في المنيو.</p>
                    @endif
                </div>

                @unless ($categories->isEmpty())
                    <aside class="alshamy-rail" aria-label="أقسام المنيو">
                        <p class="alshamy-rail__title">الأقسام</p>
                        <ul>
                            @foreach ($categories as $index => $category)
                                <li>
                                    <a href="#pills-{{ $category->id }}"
                                       class="{{ $index === 0 ? 'is-active' : '' }}"
                                       data-rail-target="pills-{{ $category->id }}-tab">
                                        {{ $category->menu_title }}
                                        <b>{{ $category->products->count() }}</b>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </aside>
                @endunless
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script>
        (function () {
            const page = document.querySelector('.alshamy-page');
            if (!page) return;

            const tabs = Array.from(document.querySelectorAll('#pills-tabContent')).length
                ? Array.from(document.querySelectorAll('[data-bs-toggle="pill"]'))
                : [];
            const railLinks = Array.from(document.querySelectorAll('[data-rail-target]'));
            const searchInput = document.getElementById('alshamy-search-input');
            const searchPanel = document.getElementById('alshamy-search-panel');
            const searchToggle = document.querySelector('[data-search-toggle]');
            const searchClose = document.querySelector('[data-search-close]');
            const searchStatus = document.querySelector('[data-search-status]');
            const noResults = document.querySelector('.alshamy-no-results');
            const panes = Array.from(document.querySelectorAll('.alshamy-pane'));
            const normalize = (value) => (value || '')
                .toLowerCase()
                .replace(/[\u064B-\u0652\u0640]/g, '')
                .replace(/[أإآٱ]/g, 'ا')
                .replace(/ى/g, 'ي')
                .replace(/ة/g, 'ه')
                .replace(/\s+/g, ' ')
                .trim();

            const syncRail = (targetId) => {
                railLinks.forEach((link) => {
                    link.classList.toggle('is-active', link.dataset.railTarget === targetId);
                });
            };

            /* Category navigation (kept from the previous implementation) */
            const activateLinkedCategory = () => {
                const matchingTab = tabs.find((tab) =>
                    '#' + tab.getAttribute('aria-controls') === window.location.hash
                );
                if (matchingTab && window.bootstrap?.Tab) {
                    window.bootstrap.Tab.getOrCreateInstance(matchingTab).show();
                }
            };

            document.querySelector('.alshamy-cats')?.addEventListener('keydown', (event) => {
                if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
                const index = tabs.indexOf(event.target.closest('[data-bs-toggle="pill"]'));
                if (index < 0) return;
                event.preventDefault();
                event.stopImmediatePropagation();
                const rtl = document.documentElement.dir === 'rtl';
                let next = index;
                if (event.key === 'Home') next = 0;
                if (event.key === 'End') next = tabs.length - 1;
                if (event.key === 'ArrowLeft') next = index + (rtl ? 1 : -1);
                if (event.key === 'ArrowRight') next = index + (rtl ? -1 : 1);
                const target = tabs[(next + tabs.length) % tabs.length];
                target.focus();
                if (window.bootstrap?.Tab) window.bootstrap.Tab.getOrCreateInstance(target).show();
            }, true);

            tabs.forEach((tab) => {
                tab.addEventListener('shown.bs.tab', () => {
                    const hash = '#' + tab.getAttribute('aria-controls');
                    if (window.location.hash !== hash) {
                        window.history.replaceState(null, '', hash);
                    }
                    tab.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                    syncRail(tab.id);
                });
            });

            activateLinkedCategory();
            window.addEventListener('hashchange', activateLinkedCategory);

            /* Search across every category without duplicating rows */
            let searching = false;
            const applySearch = () => {
                const query = normalize(searchInput?.value);
                searching = query.length > 0;
                page.classList.toggle('is-searching', searching);

                let total = 0;
                panes.forEach((pane) => {
                    let matches = 0;
                    pane.querySelectorAll('.alshamy-row').forEach((row) => {
                        const hit = !searching || normalize(row.dataset.search).includes(query);
                        row.hidden = searching && !hit;
                        if (hit) matches += 1;
                    });
                    pane.classList.toggle('is-empty', searching && matches === 0);
                    const count = pane.querySelector('.alshamy-pane__count');
                    if (count) {
                        if (!count.dataset.total) count.dataset.total = count.textContent.trim();
                        count.textContent = searching
                            ? matches + ' من ' + count.dataset.total
                            : count.dataset.total;
                    }
                    total += matches;
                });

                if (noResults) noResults.hidden = !searching || total > 0;
                if (searchStatus) {
                    searchStatus.textContent = !searching
                        ? ''
                        : total > 0
                            ? total + ' نتيجة'
                            : 'مفيش نتائج مطابقة';
                }
            };

            /* Search lives in the header, revealed by its icon */
            const setSearchOpen = (open) => {
                if (!searchPanel || !searchToggle) return false;
                searchPanel.hidden = !open;
                searchToggle.setAttribute('aria-expanded', String(open));
                if (open) {
                    searchInput?.focus();
                } else if (searchPanel.contains(document.activeElement)) {
                    searchToggle.focus();
                }
                return open;
            };

            searchToggle?.addEventListener('click', () => {
                if (searchPanel.hidden) {
                    setSearchOpen(true);
                } else {
                    if (searchInput) searchInput.value = '';
                    applySearch();
                    setSearchOpen(false);
                }
            });

            searchClose?.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                applySearch();
                setSearchOpen(false);
                searchToggle?.focus();
            });

            searchPanel?.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                event.preventDefault();
                if (searchInput?.value) {
                    searchInput.value = '';
                    applySearch();
                } else {
                    setSearchOpen(false);
                }
            });

            searchInput?.addEventListener('input', applySearch);
            searchInput?.addEventListener('search', applySearch);

            /* Switching category clears an active search */
            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    if (!searching) return;
                    searchInput.value = '';
                    applySearch();
                });
            });

            /* Image fallback for products that do get a photo later */
            document.querySelectorAll('.alshamy-row__thumb img').forEach((image) => {
                const removeThumb = () => {
                    if (!image.isConnected) return;
                    const row = image.closest('.alshamy-row');
                    image.closest('.alshamy-row__thumb')?.remove();
                    row?.classList.remove('alshamy-row--with-image');
                };
                image.addEventListener('error', removeThumb, { once: true });
                if (image.complete && image.naturalWidth === 0) removeThumb();
            });

            /* Size selection updates the price and the WhatsApp message */
            document.querySelectorAll('.alshamy-sizes').forEach((picker) => {
                picker.addEventListener('change', (event) => {
                    if (!event.target.matches('input[type="radio"]')) return;

                    const row = event.target.closest('.alshamy-row');
                    const price = Number(event.target.dataset.price);
                    const oldPrice = Number(event.target.dataset.oldPrice);
                    const display = row.querySelector('.alshamy-row__price bdi');
                    const oldDisplay = row.querySelector('.alshamy-row__old');
                    const money = (amount) => amount.toLocaleString('en-US', { maximumFractionDigits: 2 });

                    display.innerHTML = Number.isFinite(price) && price > 0
                        ? money(price) + '<small>جنيه</small>'
                        : 'السعر عند الطلب';
                    oldDisplay.hidden = !(Number.isFinite(oldPrice) && oldPrice > price && price > 0);
                    oldDisplay.querySelector('bdi').textContent = oldDisplay.hidden
                        ? ''
                        : money(oldPrice) + ' جنيه';

                    const orderLink = row.querySelector('[data-whatsapp-base]');
                    if (orderLink) {
                        const size = event.target.dataset.size?.trim();
                        const message = 'السلام عليكم، عايز أطلب ' + orderLink.dataset.productName
                            + (size ? ' - ' + size : '') + '.';
                        orderLink.href = orderLink.dataset.whatsappBase + '?text=' + encodeURIComponent(message);
                    }
                });
            });
        })();
    </script>
@endsection
