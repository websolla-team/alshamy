<div class="card-product-wrapper">
    <a href="{{ Helper::link('product.details', $product->id, $product->name) }}" class="image-wrap">
        <img class="lazyload img-product" data-src="{{ asset($product->image) }}" src="{{ asset($product->image) }}"
            alt="image-product" loading="lazy">
        @if ($product->oneImage)
            <img class="lazyload img-hover" data-src="{{ asset($product->oneImage->image) }}"
                src="{{ asset($product->oneImage->image) }}" alt="image-product" loading="lazy">
        @else
            <img class="lazyload img-hover" data-src="{{ asset($product->image) }}" src="{{ asset($product->image) }}"
                alt="image-product" loading="lazy">
        @endif
    </a>
    <div class="list-btn-main">
        <a href="#shoppingCart" class="btn-main-product add-to-cart" data-id="{{ $product->id }}"
            data-variant="{{ $product->oneVariant?->id }}">
            {{ __('main.add to cart') }}
        </a>
        <a href="#quickView" data-bs-toggle="modal" class="box-icon quickview tf-btn-loading view-product"
            data-id="{{ $product->id }}" data-variant="{{ $product->oneVariant?->id }}">
            <span class="icon icon-eye"></span>
            <span class="tooltip">{{ __('main.quick view') }}</span>
        </a>
    </div>
</div>
<div class="card-product-info ">
    <a href="{{ Helper::link('product.details', $product->id, $product->name) }}" class="text-body-default link">
        {{ $product->name }}
    </a>
    <div class="price text-body-default">
        @if ($product->oneVariant?->old_price)
            <span class="text-caption-1 old-price">
                {{ __('main.currency') }} {{ $product->oneVariant->old_price }}
            </span>
        @endif
        {{ __('main.currency') }} {{ $product->oneVariant->price }}
    </div>
    <ul class="list-color-product">
        @foreach ($product->variants as $variant)
            <li class="list-color-item color-swatch">
                <span class="swatch-value" title="{{ $variant->color?->name }}"
                    style="background-color: {{ $variant->color?->code }};"></span>
                {{-- <img class="lazyload" data-src="me/pro1.jpg" src="me/pro1.jpg" alt="image-product"> --}}
            </li>
        @endforeach
    </ul>
</div>
