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
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $mainProduct->name }} </h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0"> / Product Images </span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
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
    <a class="btn modal-effect d-block btn-outline-primary mb-3" data-effect="effect-slide-in-bottom" data-toggle="modal"
        href="#add">Add Images</a>

    <!-- row opened -->
    <div class="row row-sm">
        @foreach ($products as $product)
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <img src="{{ asset($product->image) }}" style="height: 150px;">
                            </div>
                            <div class="col-6">
                                <form action="{{ route('product.images.update') }}" method="post"
                                    enctype="multipart/form-data">
                                    <label for="">Change Image</label>
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <input type="file" name="image" class="form-control">
                                    <select name="color_id" class="form-control mt-2">
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ $color->id == $product->color_id ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="d-flex mt-2" style="gap: 10px">
                                        <button class="btn d-block btn-primary" style="flex: 1"
                                            type="submit">Update</button>
                                        <a class="modal-effect btn btn-danger employee-delete" style="padding: 8px 10px"
                                            data-effect="effect-slide-in-bottom" data-toggle="modal" href="#delete"
                                            data-id="{{ $product->id }}">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <!--/div-->

        {{-- delete modal --}}
        <div class="modal" id="delete" style="display: none;" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">Delete Image</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">×</span></button>
                    </div>
                    <form action="{{ route('product.images.delete') }}" method="post">
                        <div class="modal-body">
                            <h6>Are you sure ?</h6>
                            <input class="id" type="hidden" name="id" value="">
                            @csrf
                        </div>
                        <div class="modal-footer">
                            <button class="btn ripple btn-primary" type="submit">Delete</button>
                            <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Cancle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- add modal --}}
        <div class="modal" id="add" style="display: none;" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">Add Images</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">×</span></button>
                    </div>
                    <form action="{{ route('product.images.store') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <h6>One or Multiable Images</h6>
                            @csrf
                            <input type="hidden" name="id" value="{{ $id }}">
                            <input type="file" class="form-control" name="imgs[]" required multiple>

                            <div class="form-group mt-3">
                                <label for="color">
                                    Color <span class="font-weight-bold text-danger">*</span>
                                </label>
                                <select name="color_id" id="color" class="form-control">
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn ripple btn-primary" type="submit">Add</button>
                            <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Cancle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    {{-- delete --}}
    <script>
        $('.employee-delete').on('click', function() {
            id = $(this).data('id');
            if (id != null) {
                $('#delete').find('input.id').val(id);
            }
        })
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
@endsection
