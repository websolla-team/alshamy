@extends('website.layouts.master')
@section('title')
    {{ __('main.site') }} | {{ __('main.home') }}
@endsection

@section('content')
    @php
        $orderPhone = preg_replace('/[^\d+]/', '', $Contact?->phone1 ?? '');
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
                                <div class="alshamy-products">
                                    @foreach ($category->products as $product)
                                        @php
                                            $firstVariant = $product->variants->first();
                                            $hasPrice = $firstVariant && (float) $firstVariant->price > 0;
                                            $showOldPrice = $hasPrice && (float) $firstVariant->old_price > (float) $firstVariant->price;
                                            $description = trim(strip_tags(html_entity_decode((string) $product->description)));
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
                                                            @if ($hasPrice)
                                                                <bdi>{{ $firstVariant->price }} جنيه</bdi>
                                                            @else
                                                                السعر عند الطلب
                                                            @endif
                                                        </strong>
                                                        <del class="alshamy-menu-card__old-price"
                                                             @unless ($showOldPrice) hidden @endunless>
                                                            @if ($showOldPrice)<bdi>{{ $firstVariant->old_price }} جنيه</bdi>@endif
                                                        </del>
                                                    </div>
                                                    <a class="alshamy-menu-card__order"
                                                       href="{{ $orderPhone ? 'tel:' . $orderPhone : route('contacts') }}"
                                                       aria-label="{{ $orderPhone ? 'اتصل لطلب' : 'تواصل لطلب' }} {{ $product->name }}">
                                                        {{ $orderPhone ? 'اتصل للطلب' : 'تواصل للطلب' }}
                                                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
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
        document.querySelectorAll('.alshamy-size-picker').forEach((picker) => {
            picker.addEventListener('change', (event) => {
                if (!event.target.matches('input[type="radio"]')) return;

                const card = event.target.closest('.alshamy-menu-card');
                const price = Number(event.target.dataset.price);
                const oldPrice = Number(event.target.dataset.oldPrice);
                const display = card.querySelector('.alshamy-menu-card__price');
                const oldDisplay = card.querySelector('.alshamy-menu-card__old-price');
                const money = (amount) => amount.toLocaleString('en-US', { maximumFractionDigits: 2 }) + ' جنيه';

                display.textContent = Number.isFinite(price) && price > 0 ? money(price) : 'السعر عند الطلب';
                oldDisplay.hidden = !(Number.isFinite(oldPrice) && oldPrice > price && price > 0);
                oldDisplay.textContent = oldDisplay.hidden ? '' : money(oldPrice);
            });
        });
    </script>
@endsection
