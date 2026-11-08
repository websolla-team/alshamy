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
                <h4 class="content-title mb-0 my-auto">Pages </h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0"> / Pages List </span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <a class="btn d-block btn-outline-primary mb-3" href="{{ route('pages.create') }}">Add Page</a>
    <!-- row opened -->
    <div class="row">
        @foreach ($pages as $page)
            <div class="col-md-6 col-lg-6 col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="pro-img-box mb-0">
                            <img class="w-100" src="{{ asset($page->image) }}" style="height: 200px; object-fit: cover"
                                alt="product-image">
                        </div>
                        <div class="text-center">
                            <h3 class="h6 mb-2 mt-4 font-weight-bold text-uppercase">
                                <p>{{ $page->title }}</p>
                            </h3>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-primary employee-edit" style="padding: 5px 10px"
                                href="{{ route('pages.edit', $page->id) }}">
                                <i class="fa fa-edit"></i>
                            </a>
                            @if ($page->id != 1)
                                <a class="modal-effect btn btn-danger employee-delete" style="padding: 5px 10px"
                                    data-effect="effect-slide-in-bottom" data-toggle="modal" href="#delete"
                                    data-id="{{ $page->id }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endif
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
                        <h6 class="modal-title">Delete Page</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">×</span></button>
                    </div>
                    <form action="{{ route('pages.destroy', 'delete') }}" method="post">
                        <div class="modal-body">
                            <h6>Are you sure ?</h6>
                            <input class="id" type="hidden" name="id" value="">
                            @csrf
                            @method('delete')
                        </div>
                        <div class="modal-footer">
                            <button class="btn ripple btn-primary" type="submit">Delete</button>
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

    {{-- edit modal --}}
    <script>
        $('.employee-edit').on('click', function() {
            id = $(this).data('id');
            name = $(this).data('title');
            $('#modaldemo8').find('input.id').val(id);
            $('#modaldemo8').find('input.title').val(name);
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
