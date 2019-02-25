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

                {{--them moi store su dung api--}}
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title"><span class="icon"> <i class="icon-align-justify"></i> </span>
                            <h5>Add new store woocomerce</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <form action="{{url('woo/form-add-new-store')}}" method="post" class="form-horizontal">
                                {{ csrf_field()}}
                                <div class="control-group">
                                    <label class="control-label">Store Name :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="storename" placeholder="Store name"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Link Store :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="linkstore" placeholder="Link store"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Consumer Key</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="consumer_key"
                                               placeholder="Consumer Key"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Consumer Secret :</label>
                                    <div class="controls">
                                        <input type="text" class="span11" name="consumer_secret"
                                               placeholder="Consumer Secret"/>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

