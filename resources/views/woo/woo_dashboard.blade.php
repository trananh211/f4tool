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
        <div class="container-fluid">
            <hr>
            @include('/admin/session')
            @include('/woo/action_box')

            <div class="row-fluid">
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="icon-signal"></i> </span>
                            <h5>Line chart</h5>
                        </div>
                        <div class="widget-content">
                            <div class="bars"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

