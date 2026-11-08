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
                <h4 class="content-title mb-0 my-auto">Orders </h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0"> / Orders List </span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">#</th>
                                    <th class="wd-15p border-bottom-0">Order Number</th>
                                    <th class="wd-15p border-bottom-0">Payment  </th>
                                    <th class="wd-15p border-bottom-0">Stutus</th>
                                    <th class="wd-15p border-bottom-0">Customer Name</th>
                                    <th class="wd-25p border-bottom-0">Shipping</th>
                                    <th class="wd-25p border-bottom-0">Total</th>
                                    <th class="wd-25p border-bottom-0">Delivery</th>
                                    <th class="wd-25p border-bottom-0">Read</th>
                                    <th class="wd-25p border-bottom-0">Date</th>
                                    <th class="wd-25p border-bottom-0">Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $index => $order)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>#{{ $order->code }}</td>
                                        @if( $order->pay == "card")
                                        <td><span class="badge badge-success text-white"> Vise - Wallet</span></td>
                                       @else
                                         <td><span class="badge badge-primary text-white">Cash On Delivery</span></td>
                                       @endif
                                        <td>
                                            @if ($order->status == 'pending')
                                                <span class="badge badge-warning text-white">Pending</span>
                                            @elseif ($order->status == 'processing')
                                                <span class="badge badge-info text-white">Processing</span>
                                            @elseif ($order->status == 'completed')
                                                <span class="badge badge-success text-white">Completed</span>
                                            @elseif ($order->status == 'returned')
                                                <span class="badge badge-secondary text-white">Cancelled</span>
                                            @elseif ($order->status == 'cancelled')
                                                <span class="badge badge-danger text-white">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->user_name }}</td>
                                        <td>LE {{ $order->shipping }}</td>
                                        <td>LE {{ $order->total }}</td>
                                        <td>{{ $order->delivery }}</td>
                                        <td>
                                            @if ($order->readed)
                                                <span class="badge badge-success text-white">Yes</span>
                                            @else
                                                <span class="badge badge-warning text-white">No</span>
                                            @endif
                                        </td>
                                        <td style="text-wrap: nowrap;">{{ $order->created_at }}</td>
                                        <td>
                                            <div class="row" style="gap: 10px; width: 100px">
                                                <a class="btn btn-warning" style="padding: 3px 10px"
                                                    href="{{ route('orders.show', $order->id) }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="modal-effect btn btn-danger employee-delete"
                                                    style="padding: 3px 10px" data-effect="effect-slide-in-bottom"
                                                    data-toggle="modal" href="#delete" data-id="{{ $order->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->

        {{-- delete modal --}}
        <div class="modal" id="delete" style="display: none;" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">Delete Product</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">×</span></button>
                    </div>
                    <form action="{{ route('orders.destroy', 'delete') }}" method="post">
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
