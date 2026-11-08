@extends('website.layouts.master')
@section('title')
    {{ __('main.site') }} | {{ __('main.home') }}
@endsection
@section('css')
@endsection
@section('content')









    <!-- Food Menu section  S T A R T -->
 <section class="food-menu-section fix section-padding">
        <div class="burger-shape">
            <img src="{{asset('website/assets/img/shape/burger-shape.png')}}" alt="img">
        </div>
        <div class="fry-shape">
            <img src="{{asset('website/assets/img/shape/fry-shape.png')}}" alt="img">
        </div>
        <div class="food-menu-wrapper style1">
            <div class="container">
                <div class="food-menu-tab-wrapper style-bg">
                    <div class="title-area">
                        <div class="sub-title text-center wow fadeInUp" data-wow-delay="0.5s">
                            <img class="me-1" src="{{asset('website/assets/img/icon/titleIcon.svg')}}" alt="icon">{{ __('main.site') }}<img class="ms-1"
                                src="{{asset('website/assets/img/icon/titleIcon.svg')}}" alt="icon">
                        </div>
                        <h2 class="title wow fadeInUp" data-wow-delay="0.7s">
                           {{ __('main.related products') }}
                        </h2>
                    </div>


                    <div class="food-menu-tab">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                           @foreach ($categories as $index => $category)


                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index == 0 ? 'active' : '' }}" id="pills-{{ $category->id }}-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-{{ $category->id }}" type="button" role="tab"
                                    aria-controls="pills-{{ $category->id }}" aria-selected="true"><img
                                        src="{{asset($category->image )}}" alt="icon">{{ $category->title }}</button>
                            </li>
                             @endforeach


                        </ul>
                   <div class="tab-content" id="pills-tabContent">
                        @foreach ($categories as $index => $category)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                            id="pills-{{ $category->id }}" role="tabpanel">

                            <div class="row gx-60">

                                @foreach ($category->products as $product)
                                    <div class="col-lg-6">
                                        <div class="single-menu-items">
                                            <div class="details">
                                                <div class="menu-item-thumb">
                                                    <img src="{{ asset($product->image) }}" alt="thumb">
                                                </div>

                                                <div class="menu-content">

                                                    @php
                                                    $colors = $product->variants->pluck('color.name')->unique()->implode(',');
                                                    $sizes = $product->variants->pluck('size')->unique()->implode(',');
                                                    @endphp

                                                    <a href="#">

                                                    <h3 class="active">{{ $product->name }}</h3>
                                                    </a>


                                                    <p>{{ Helper::limit(trim(html_entity_decode($product->description)), 200) }}
</p>
                                                </div>
                                            </div>

                                       <h6>
                                            <span class="price">
                                                {{ __('main.currency') }} {{ $product->oneVariant?->price }} -
                                            </span>

                                            <del class="old_price" style="{{ $product->oneVariant?->old_price ? '' : 'display:none' }};font-size:14px">
                                                {{ __('main.currency') }} {{ $product->oneVariant?->old_price }}
                                            </del>


                                        </h6>
                                             <select class="sizeSelect styled-select">
                                                <option    value=""
                                                        data-price=""
                                                        data-old-price=""
                                                   >الحجم</option>
                                                @foreach ($product->variants as $variant)
                                                    <option
                                                        value="{{ $variant->size }}"
                                                        data-price="{{ $variant->price }}"
                                                        data-old-price="{{ $variant->old_price }}"
                                                    >
                                                        {{ $variant->size }}
                                                    </option>
                                                @endforeach
                                            </select>


                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        @endforeach
                        </div>


                    </div>

                </div>
            </div>
        </div>

    </section>





@endsection
@section('js')
<script>
const slider = document.querySelector('.food-menu-tab .nav-pills');
let isDown = false;
let startX;
let scrollLeft;

slider.addEventListener('mousedown', (e) => {
  isDown = true;
  slider.classList.add('active');
  startX = e.pageX - slider.offsetLeft;
  scrollLeft = slider.scrollLeft;
});

slider.addEventListener('mouseleave', () => {
  isDown = false;
});

slider.addEventListener('mouseup', () => {
  isDown = false;
});

slider.addEventListener('mousemove', (e) => {
  if (!isDown) return;
  e.preventDefault();
  const x = e.pageX - slider.offsetLeft;
  const walk = (x - startX) * 1.5;
  slider.scrollLeft = scrollLeft - walk;
});
</script>


<script>
    $('.sizeSelect').on('change', function () {
        let selectedOption = $(this).find(':selected');
        let price = selectedOption.data('price');
        let oldPrice = selectedOption.data('old-price');

        // اختر العناصر الخاصة بالـ parent (single-menu-items)
        let parent = $(this).closest('.single-menu-items');
            if (price) {
                parent.find('.price').text("{{ __('main.currency') }} " + price +" - ");

            }else{
            parent.find('.price').text("برجاء اختيار الحجم" );
            }



        if (oldPrice) {
            parent.find('.old_price').text("{{ __('main.currency') }} " + oldPrice).show();
        } else {
            parent.find('.old_price').hide();
        }
    });
</script>


@endsection
