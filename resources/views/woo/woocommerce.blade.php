@extends('layouts.adminLayout.admin_design')

@section('content')
    <div id="content">
        <div id="content-header">
            <div id="breadcrumb">
                <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
                <a href="#">Form elements</a> <a href="#" class="current">Woocomerce API</a>
            </div>
            <h1>Woocomerce API</h1>
        </div>
        <div class="container-fluid"><hr>

            @include('/admin/session')

            <div class="row-fluid">
                {{--danh sach store woocommerce su dung api--}}
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
                            <h5>List woocommerce store</h5>
                            <p>
                                <a class="btn btn-info" style="" href="{{ url('admin/add-new-woo-store') }}">Add New Store</a>
                            </p>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Store Name</th>
                                    <th>Link</th>
                                    <th>Status</th>
                                    <th>Event</th>
                                    <th>View</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($stores && !empty($stores))
                                    @foreach($stores as $key => $store)
                                        <tr>
                                            <?php $check = 0; ?>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $store->woo_name }}</td>
                                            <td>{{ $store->woo_link }}</td>
                                            <td class="center">
                                                @if($store->status == 1)
                                                    <span class="label label-success">Active</span>
                                                    <?php $check = 1; ?>
                                                @elseif( $store->status == 0)
                                                    <span class="label label-warning">Waiting</span>
                                                @else
                                                    <span class="label">Not Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-mini">Edit</button>
                                                <button class="btn btn-info btn-mini">Delete</button>
                                            </td>
                                            <td>
                                                @if ($check)
                                                    <a class="btn btn-mini btn-primary"
                                                       href="{{ url('/woo/dashboard-store/'.$store->id) }}">See Detail
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6">Empty store</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <a href="{{ url('woo/test-function') }}"><button>Test Function</button></a>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

