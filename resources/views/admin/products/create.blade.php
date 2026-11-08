@extends('layouts.master')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!---Internal Owl Carousel css-->
    <link href="{{ URL::asset('assets/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <!---Internal  Multislider css-->
    <link href="{{ URL::asset('assets/plugins/multislider/multislider.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
    <!---Internal Fancy uploader css-->
    <link href="{{ URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
    <!--Internal Sumoselect css-->
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/sumoselect/sumoselect-rtl.css') }}">
    <!--Internal  TelephoneInput css-->
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/telephoneinput/telephoneinput-rtl.css') }}">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Create Products</h4>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="card">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <li>{{ session('error') }}</li>
                    </ul>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <li>املأ جميع الحقول بشكل صحيح</li>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <p><strong>Images</strong> must be equal or less than <strong>1MB</strong></p>
                <div class="form-group">
                    <label for="">Image <span class="font-weight-bold text-danger">*</span></label>
                    <input type="file" class="dropify" data-height="200" name="image" required />
                </div>
                <div class="row">
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <div class="col-6">
                            <div class="form-group">
                                <label for="overview">Name {{ $localeCode }} <span
                                        class="font-weight-bold text-danger">*</span></label>
                                <input class="form-control" required placeholder="enter name"
                                    name="name[{{ $localeCode }}]">
                            </div>
                        </div>
                    @endforeach
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <div class="col-12">
                            <div class="form-group">
                                <label for="overview">Description {{ $localeCode }} <span
                                        class="font-weight-bold text-danger">*</span></label>
                                <textarea class="form-control" placeholder="enter description" rows="5" name="description[{{ $localeCode }}]"
                                    id="description{{ $localeCode }}"></textarea>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-6">
                        <div class="form-group">
                            <label for="overview">Category </label>
                            <select name="category_id" class="form-control select2" required>
                                @foreach ($categories as $category)
                                    <option value="">choose</option>
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="overview">Defualt Price</label>
                            <input type="number" class="form-control" placeholder="enter price" id="defualt-price">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input type="checkbox" id="featured" value="1" style="display: inline-block"
                                name="is_featured" />
                            <label for="featured">Top Seller (show in Top Seller products in home page)</label>
                        </div>
                    </div>

                    {{-- variants --}}
                    <div class="col-12">
                        <button class="btn btn-secondary add-variant d-block m-auto" type="button">
                            Add Product Variant
                        </button>
                        <div class="variants p-3 border border-1">
                            <div class="item row position-relative">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="overview">Size <span
                                                class="font-weight-bold text-danger">*</span></label>
                                              <select name="size[]" class="form-control" required>
                                            <option value="">Choose Size</option>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size->name }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label for="overview">Quantity <span
                                                class="font-weight-bold text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control" name="quantity[]"
                                            required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="overview">Price <span
                                                class="font-weight-bold text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control variant-price"
                                            name="price[]" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="overview">Old Price</label>
                                        <input type="number" min="0" class="form-control" name="old_price[]">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn d-block btn-outline-primary w-100 mt-4">Submit</button>
            </form>
        </div>
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    {{-- defualt price --}}
    <script>
        $(document).ready(function() {
            $('#defualt-price').on('keyup change', function() {
                let defaultPrice = $(this).val();
                $('.variant-price').val(defaultPrice);
            });
        });
    </script>

    {{-- variants --}}
    <script>
        $(document).ready(function() {
            const variantsContainer = $('.variants');
            const addVariantButton = $('.add-variant');
            let variantIndex = 1; // Start index for new variants

            addVariantButton.on('click', function() {
                const originalItem = variantsContainer.find('.item').first();
                const newItem = originalItem.clone();
                const defaultPrice = $('#defualt-price').val();

                // Clear inputs and reset selects
                newItem.find('input').val('');
                newItem.find('select').prop('selectedIndex', 0);

                // Set default price if it's not empty
                const variantPriceInput = newItem.find('.variant-price');
                variantPriceInput.val(defaultPrice);

                // Update the name attribute for variant_images input
                newItem.find('input[name^="variant_images"]').attr('name',
                    `variant_images[${variantIndex}][]`);

                // Add remove button
                const removeButton = $('<button>', {
                    type: 'button',
                    class: 'remove-variant btn btn-danger btn-sm',
                    html: '×',
                    css: {
                        position: 'absolute',
                        top: '55%',
                        right: '-15px',
                        transform: 'translateY(-50%)',
                        padding: '2px 7px'
                    }
                });

                removeButton.on('click', function() {
                    newItem.remove();
                });

                newItem.append(removeButton);
                variantsContainer.append(newItem);
                variantIndex++; // Increment index for the next variant
            });
        });
    </script>


    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>

    <!--Internal Fileuploads js-->
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>
    <!--Internal Fancy uploader js-->
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
    <!--Internal  Form-elements js-->
    <script src="{{ URL::asset('assets/js/advanced-form-elements.js') }}"></script>

    <script src="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
    <!--Internal  jquery.maskedinput js -->
    <script src="{{ URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>
    <!--Internal  spectrum-colorpicker js -->
    <script src="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js') }}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!--Internal Ion.rangeSlider.min js -->
    <script src="{{ URL::asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <!--Internal  jquery-simple-datetimepicker js -->
    <script src="{{ URL::asset('assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}"></script>
    <!-- Ionicons js -->
    <script src="{{ URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js') }}"></script>
    <!--Internal  pickerjs js -->
    <script src="{{ URL::asset('assets/plugins/pickerjs/picker.min.js') }}"></script>
    <!-- Internal form-elements js -->
    <script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>

    <script>
        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            ClassicEditor
                .create(document.querySelector('#description{{ $localeCode }}'))
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#small_description{{ $localeCode }}'))
                .catch(error => {
                    console.error(error);
                });
        @endforeach
    </script>
@endsection
