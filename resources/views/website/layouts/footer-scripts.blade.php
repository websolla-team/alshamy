

    <!--<< All JS Plugins >>-->
    <script src="{{asset('website/assets/js/jquery-3.7.1.min.js')}}"></script>
    <!--<< Bootstrap Js >>-->
    <script src="{{asset('website/assets/js/bootstrap.bundle.min.js')}}"></script>
    <!--<< Waypoints Js >>-->
    <script src="{{asset('website/assets/js/jquery.waypoints.js')}}"></script>
    <!--<< Counterup Js >>-->
    <script src="{{asset('website/assets/js/jquery.counterup.min.js')}}"></script>
    <!--<< Viewport Js >>-->
    <script src="{{asset('website/assets/js/viewport.jquery.js')}}"></script>
    <!--<< Magnific popup Js >>-->
    <script src="{{asset('website/assets/js/magnific-popup.min.js')}}"></script>
    <!--<< Tilt Js >>-->
    <script src="{{asset('website/assets/js/tilt.min.js')}}"></script>
    <!--<< Swiper Slider Js >>-->
    <script src="{{asset('website/assets/js/swiper-bundle.min.js')}}"></script>
    <!--<< MeanMenu Js >>-->
    <script src="{{asset('website/assets/js/jquery.meanmenu.min.js')}}"></script>
    <!--<< Wow Animation Js >>-->
    <script src="{{asset('website/assets/js/wow.min.js')}}"></script>
    <!--<< Nice Select Js >>-->
    <script src="{{asset('website/assets/js/nice-select.min.js')}}"></script>
    <!--<< Main.js')}} >>-->
    <script src="{{asset('website/assets/js/main.js')}}"></script>

	<!--Google map api -->






	<!-- Site Scripts -->


<!-- jQuery -->


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- <script src="https://cdn.js')}}delivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js')}}"></script> --}}




<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('exampleModal');

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // بيانات المنتج
        const id = button.getAttribute('data-id');
        const variant = button.getAttribute('data-variant');
        const name = button.getAttribute('data-name');
        const price = button.getAttribute('data-price');
        const oldPrice = button.getAttribute('data-old-price');
        const text = button.getAttribute('data-text');
        const image = button.getAttribute('data-image');
        const sizes = button.getAttribute('data-size')?.split(',') || [];
        const extras = button.getAttribute('data-color')?.split(',') || [];

        // تعديل النصوص والصورة
        const modalAddCartBtn = document.getElementById('modalProductId');
        modalAddCartBtn.setAttribute('data-id', id);
        modalAddCartBtn.setAttribute('data-variant', variant);

        document.getElementById('modalProductName').innerText = name;
        document.getElementById('modalProductPrice').innerText = "EGP " + price;
        document.getElementById('modalProductText').innerText = text;
        document.getElementById('modalProductImage').src = image;

        // السعر القديم
        const oldPriceEl = document.getElementById('modalOldPrice');
        if (oldPrice) {
            oldPriceEl.innerText = "EGP " + oldPrice;
            oldPriceEl.classList.remove('d-none');
        } else {
            oldPriceEl.classList.add('d-none');
        }

        // ملأ الأحجام ديناميكي
        const modalSizesEl = document.getElementById('modalSizes');
        modalSizesEl.innerHTML = '';
        sizes.forEach((size, index) => {
            const inputId = 'size_' + index;
            modalSizesEl.innerHTML += `
                <div>
                    <input type="radio" id="${inputId}" name="size" value="${size}" ${index===0?'checked':''}>
                    <label for="${inputId}">${size}</label>
                </div>
            `;
        });

        // ملأ الإضافات ديناميكي
        const modalExtrasEl = document.getElementById('modalExtras');
        modalExtrasEl.innerHTML = '';
        extras.forEach((extra, index) => {
            const inputId = 'extra_' + index;
            modalExtrasEl.innerHTML += `
                <div>
                    <input type="radio" id="${inputId}" name="color"  value="${extra}" ${index===0?'checked':''}>
                    <label for="${inputId}">${extra}</label>
                </div>
            `;
        });
    });


});




// دالة الإضافة للسلة مع color و size
function addToCart(productId, variant, qty = 1, color = null, size = null) {
    $.ajax({
        url: '{{ route('cart.add') }}',
        method: 'POST',
        data: {
            id: productId,
            variant: variant,
            qty: qty,
            color: color,
            size: size,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: '{{ __('main.success') }}',
                text: response.success,
                showCancelButton: true,
                confirmButtonText: '{{ __('main.shopping cart') }}',
                cancelButtonText: '{{ __('main.continue shopping') }}',
                preConfirm: () => {
                    window.location.href = '{{ route('cart') }}';
                }
            });

            // تحديث بيانات العربة
            $.ajax({
                method: 'GET',
                url: '{{ route('contacts') }}',
                success: function(response) {
                    let $response = $('<div>').html(response);
                    let cartCount = $response.find('header .cart-count').html();
                    let cartContent = $response.find('#shoppingCart').html();
                    let cartContents = $response.find('#shoppingCarts').html();
                    $('header .cart-count').html(cartCount);
                    $('#shoppingCart').html(cartContent);
                    $('#shoppingCarts').html(cartContents);
                },
                error: function() {
                    console.log('Error fetching cart data.');
                }
            });
        },
        error: function(response) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: response.responseJSON.error,
            });
        }
    });
}


$(document).on('click', '.add-to-cart-details', function(e) {
    e.preventDefault();
    let qty = $('.quantity-input').val() || 1;
    let productId = $(this).attr('data-id');
    let variant = $(this).attr('data-variant');


    let color = $('#modalExtras input[name="color"]:checked').val();
    let size = $('#modalSizes input[name="size"]:checked').val();

    addToCart(productId, variant, qty, color, size);
});
</script>




{{-- update quantity in cart --}}
<script>
    $(".dec-btn").on("click", function(e) {
        e.preventDefault();
        var $this = $(this);
        var $input = $this.closest("div").find("input");
        var value = parseInt($input.val(), 10);

        if (value > 1) {
            value = value - 1;
        }
        $input.val(value).trigger("change");
    });

    $(".inc-btn").on("click", function(e) {
        e.preventDefault();
        var $this = $(this);
        var $input = $this.closest("div").find("input");
        var value = parseInt($input.val(), 10);

        if (value > -1) {
            value = value + 1;
        }
        $input.val(value).trigger("change");
    });
    $(document).on('change', '.qty-update', function() {

        let $input = $(this);
        let productId = $(this).data('id');
        let quantity = $(this).val();
        if (quantity < 1) {
            $input.val(1);
            return;
        }
        $.ajax({
            url: '{{ route('cart.update') }}',
            method: 'POST',
            data: {
                id: productId,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(response) {
                $input.val(quantity - 1);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.responseJSON.error,
                });
            }
        });
    });
</script>

{{-- remove item from cart --}}
<script>
    $(document).on('click', '.remove-item', function(e) {
        e.preventDefault();
        let productId = $(this).data('id');
        $.ajax({
            url: '{{ route('cart.remove') }}',
            method: 'POST',
            data: {
                id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                });
            }
        });
    });
</script>

{{-- quick view --}}
<script>
    $(document).ready(function() {
        $('.view-product').on('click', function(e) {
            e.preventDefault();
            $('.modal-dialog').empty();
            let id = $(this).data('id');
            let variant = $(this).data('variant');
            $.ajax({
                url: '{{ route('product.quickView', '') }}/' + variant,
                method: 'GET',
                success: function(response) {
                    $('.modal-dialog').html(response);
                },
                error: function() {
                    $('.modal-dialog').click();
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "{{ __('main.no products') }}",
                    });
                }
            });
        });
    });
</script>
{{-- quick view actions --}}
<script>
    $(document).ready(function() {
        $(document).on('click', '.image-view-url', function(e) {
            let img = $(this).attr('src');
            $('.image-view').attr('src', img);
        });
        $(document).on('change', '.variants-list', function() {
            let selectedOption = $(this).find('option:selected');
            let price = selectedOption.data('price');
            let oldPrice = selectedOption.data('old_price');
            $('.size-price').text('LE ' + price);
            if (oldPrice) {
                if ($('.size-old-price').length) {
                    $('.size-old-price').text('LE ' + oldPrice);
                } else {
                    $('.quickview-wrapper .price').append(
                        '<del class="amount size-old-price" style="color: red; font-size: 15px;">LE ' +
                        oldPrice + '</del>');
                }
            } else {
                $('.size-old-price').remove();
            }
        });
        $(document).on('click', '.plus', function() {
            let $input = $(this).siblings('.qty');
            let currentVal = parseInt($input.val());
            if (!isNaN(currentVal)) {
                $input.val(currentVal + 1);
            }
        });
        $(document).on('click', '.minus', function() {
            let $input = $(this).siblings('.qty');
            let currentVal = parseInt($input.val());
            if (!isNaN(currentVal) && currentVal > 1) {
                $input.val(currentVal - 1);
            }
        });
    });
</script>

{{-- subscribtion --}}
<script>
    $(document).ready(function() {
        $('#subscribtion').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route('newsletter.message') }}',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        title: "Good job!",
                        text: response,
                        icon: "success"
                    });
                    $('#subscribtion')[0].reset();
                },
                error: function(error) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong!",
                    });
                }
            });
        });
    });
</script>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ __('main.success') }}',
            text: '{{ session('success') }}',
        });
    </script>
@endif

@yield('js')
