@extends('layouts.master')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        td {
            vertical-align: middle !important;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- Container opened -->
    <div class="container-fluid mt-4">
        @if (session('success'))
            <div class="alert alert-success">
                <strong>{{ session('success') }}</strong>
            </div>
        @endif
        <!-- Order Information -->
        <div class="row">
            <div class="col-6">
                <div class="card mb-4">
                    <div class="card-header mb-0 pb-0">
                        <h5 class="card-title text-primary mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Customer Name:</div>
                            <div class="col-8">{{ $order->user_name }}
                                @if ($order->user)
                                    <span class="badge badge-info text-white ml-4">User</span>
                                @else
                                    <span class="badge badge-success text-white ml-4">Guest</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Phone:</div>
                            <div class="col-8">{{ $order->phone }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Delivery Area:</div>
                            <div class="col-8">{{ $order->delivery }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">City:</div>
                            <div class="col-8">{{ $order->city }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Address:</div>
                            <div class="col-8">{{ $order->address }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card mb-4">
                    <div class="card-header mb-0 pb-0">
                        <h5 class="card-title text-primary mb-0">Order Summery</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Order Number:</div>
                            <div class="col-8">#{{ $order->code }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Date:</div>
                            <div class="col-8">{{ $order->created_at->format('Y M d') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Products Price:</div>
                            <div class="col-8">LE {{ $order->products->sum('total_price') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Shipping:</div>
                            <div class="col-8">LE {{ $order->shipping }}</div>
                        </div>
                        {{-- payment --}}
                         <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Payment:</div>


                            <div class="col-8"><span class="badge badge-success text-white ml-4">{{ $order->payment_method }}</span>
                                    </div>


                        </div>

                        @if ($order->coupon & $order->discount)
                            <div class="row mb-2">
                                <div class="col-4 font-weight-bold">Discount:</div>
                                <div class="col-8">
                                    {{ $order->discount }}
                                    <span class="badge badge-info ml-4">{{ $order->coupon }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col-4 font-weight-bold">Total Price:</div>
                            <div class="col-8">LE {{ $order->total }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Order actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card mb-4" style="height: 100%">
                    <div class="card-header mb-0 pb-0">
                        <h5 class="card-title text-primary mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        @if ($order->note)
                            <span class="mb-2 d-block">Note</span>
                            <p>{{ $order->note }}</p>
                        @endif
                        <form action="{{ route('orders.status', $order->id) }}" method="post">
                            @csrf
                            <label for="status">Change Order Status</label>
                            <select name="status" id="status" class="form-control" onchange="submit()">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                    Processing
                                </option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                                <option value="returned" {{ $order->status == 'returned' ? 'selected' : '' }}>
                                    Returned
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Order Details (Products) -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header mb-0 pb-0">
                        <h5 class="card-title text-primary mb-0">Order Products</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Extra</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->products as $product)
                                    <tr>
                                        <td>
                                            <img src="{{ asset($product->product?->image) }}" alt="product image"
                                                style="height: 100px">
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->color ?? 'N/A' }}</td>
                                        <td>{{ $product->size ?? 'N/A' }}</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td>LE {{ $product->price }}</td>
                                        <td>LE {{ $product->total_price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Container closed -->
@endsection
@section('js')
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

    {{-- delete --}}
    <script>
        $('.employee-delete').on('click', function() {
            id = $(this).data('id');
            if (id != null) {
                $('#delete').find('input.id').val(id);
            }
        })
    </script>
@endsection
