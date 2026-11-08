<div class="modal-content">
    <div class="tf-quick-view-image">
        <div class="wrap-quick-view wrapper-scroll-quickview">
            {{-- <div class="quickView-item item-scroll-quickview" data-scroll-quickview="beige">
                <img class="lazyload" data-src="images/shop/quickview-slidebar-1.jpg"
                    src="images/shop/quickview-slidebar-1.jpg" alt="">
            </div> --}}
            <div class="quickView-item item-scroll-quickview">
                <img class="lazyload" data-src="{{ asset($product->image) }}" src="{{ asset($product->image) }}"
                    alt="image">
            </div>
            @foreach ($product->images as $image)
                <div class="quickView-item item-scroll-quickview">
                    <img class="lazyload" data-src="{{ asset($image->image) }}" src="{{ asset($image->image) }}"
                        alt="image">
                </div>
            @endforeach
        </div>
    </div>
    <div class="wrap">
        <div class="header">
            <h5 class="title">{{ __('main.quick view') }}</h5>
            <span class="icon-close icon-close-popup" data-bs-dismiss="modal"></span>
        </div>
        <div class="tf-product-info-list">
            <div class="tf-product-info-heading">
                <div class="tf-product-info-name">
                    <h3 class="name">{{ $product->name }}</h3>
                    <div class="sub">
                        <div class="tf-product-info-rate">
                            <div class="list-star-default">
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tf-product-info-desc">
                    <div class="tf-product-info-price">
                        <h5 class="price-on-sale">{{ __('main.currency') }} {{ $product->oneVariant?->price }}</h5>
                        @if ($product->oneVariant?->old_price)
                            <div class="compare-at-price">{{ __('main.currency') }}
                                {{ $product->oneVariant?->old_price }}</div>
                        @endif
                        {{-- <div class="badges-on-sale text-btn-uppercase">
                            -25%
                        </div> --}}
                    </div>
                    <p>{!! Helper::limit($product->description, 200) !!}</p>
                </div>
            </div>
            <div class="tf-product-info-choose-option">
                <div class="variant-picker-item">
                    <div class="variant-picker-label mb_12">
                        {{ __('main.color') }}:<span class="text-title variant-picker-label-value">
                            {{ $product->oneVariant?->color?->name }}
                        </span>
                    </div>
                </div>
                <div class="variant-picker-item">
                    <div class="d-flex justify-content-between mb_12">
                        <div class="variant-picker-label">
                            {{ __('main.size') }}:<span class="text-title variant-picker-label-value">
                                {{ $product->oneVariant?->size }}
                            </span>
                        </div>
                        <a href="{{ Helper::link('product.details', $product->id, $product->name) }}"
                            class="size-guide text-title link">{{ __('main.more colors and sizes') }}</a>
                    </div>
                </div>
                <div>
                    <div class="tf-product-info-by-btn mb_10">
                        <a class="tf-btn btn-onsurface flex-grow-1 show-shopping-cart add-to-cart"
                            data-id="{{ $product->id }}" data-variant="{{ $product->oneVariant?->id }}">
                            <span>{{ __('main.add to cart') }} - </span>
                            <span class="tf-qty-price total-price">
                                {{ __('main.currency') }} {{ $product->oneVariant?->price }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
