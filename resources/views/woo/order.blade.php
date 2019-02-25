@extends('layouts.adminLayout.admin_design')

@section('content')
    <div id="content">
        <div id="content-header">
            <div id="breadcrumb">
                <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
                <a href="#">Form elements</a> <a href="#" class="current">Woocomerce API</a>
            </div>
            <h1>Woocomerce API - Order</h1>
        </div>
        <div class="container-fluid">
            <hr>
            @include('/admin/session')
            @include('/woo/action_box')
            {{--hien thi data order table--}}
            <div class="row-fluid">
                <div class="widget-box">
                    <div class="widget-title"><span class="icon"><i class="icon-th"></i></span>
                        <h5>List order</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered data-table">
                            <thead>
                            <tr>
                                <th>Order number</th>
                                <th>Email</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Transection ID</th>
                                <th>Tracking Number</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($orders) && is_array($orders))
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->number }}</td>
                                        <td>{{ $order->billing->email }}</td>
                                        <td>{{ $order->shipping->first_name }}</td>
                                        <td>{{ $order->shipping->last_name }}</td>

                                        <td>
                                            @switch($order->status)
                                                @case('processing')
                                                <span class="label label-success">Processing</span>
                                                @break

                                                @case('on-hold')
                                                <span class="label label-warning">On hold</span>
                                                @break

                                                @case('production')
                                                <span class="label">Production</span>
                                                @break

                                                @case('shipping')
                                                <span class="label label-success">Shipping</span>
                                                @break

                                                @default
                                                <span class="label label-inverse">Completed</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($order->payment_method)
                                                @case('Paypal')
                                                Paypal
                                                @break

                                                @default
                                                Stripe
                                            @endswitch
                                        </td>
                                        <td>{{ $order->date_created }}</td>
                                        <td>{{ $order->transaction_id }}</td>
                                        <td class="center">
                                            @if(sizeof($order->meta_data) > 2)
                                                @foreach($order->meta_data as $shipping)
                                                    @if ( $shipping->key == 'ywot_tracking_code')
                                                        {{ $shipping->value }}
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="gradeX">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="center">-</td>
                                </tr>
                            @endif
                            <?php
                            //                            echo "<pre>";
                            //                            print_r($orders);
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{--End hien thi data order table--}}
        </div>
    </div>
@endsection


<script src="{{ asset('source/js/jquery.min.js') }}"></script>
<script src="{{ asset('source/js/jquery.ui.custom.js') }}"></script>
<script src="{{ asset('source/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('source/js/jquery.uniform.js') }}"></script>
<script src="{{ asset('source/js/select2.min.js') }}"></script>
<script src="{{ asset('source/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('source/js/matrix.js') }}"></script>
<script src="{{ asset('source/js/matrix.tables.js') }}"></script>

