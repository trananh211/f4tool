@extends('layouts.adminLayout.admin_design')

@section('content')
    <div id="content">
        <div id="content-header">
            <div id="breadcrumb">
                <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
                <a href="#">Form elements</a> <a href="#" class="current">Woocomerce API</a>
            </div>
            <h1>Woocomerce API - Production</h1>
        </div>
        <div class="container-fluid">
            <hr>
            @include('/admin/session')
            @include('/woo/action_box')

            <div class="row-fluid">
                {{--hien thi form upload file excel--}}
                <div class="span3">
                    <div class="widget-box">
                        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                            <h5>Form Elements</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <form action="{{ route('excel.upload.post') }}" method="post" class="form-horizontal"
                                  enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="control-group">
                                    File upload input
                                    <input type="file" class="css-upload-file" name="excel_file[]" multiple/>
                                </div>
                                <div class="control-group">
                                    Tittle:
                                    <input type="text" class="span11" name="excel_title">
                                </div>
                                <div class="control-group">
                                    Note
                                    <textarea class="span11" name="excel_note"></textarea>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{--End hien thi form upload file excel--}}

                {{--Hien thi danh sach up load file gan day nhat--}}
                <div class="span9">
                    <div class="widget-box">
                        <div class="widget-title"><span class="icon"> <i class="icon-th"></i> </span>
                            <h5>Action tracking number - Last 30 days ago</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Note</th>
                                    <th>Date Upload</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ($files && !empty($files))
                                    @foreach($files as $key => $file)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td title="{{ $file->name }}">{{ str_limit($file->name,16,'....') }}</td>
                                            <td>{{ $file->title }}</td>
                                            <td title="{{ $file->note }}">{{ str_limit($file->note,20,'....') }}</td>
                                            <td class="center">{{ $file->created_at }}</td>
                                            <td class="center">
                                                @switch($file->status)
                                                    @case (1)
                                                        <span class="label label-success">Success</span>
                                                        @break
                                                    @case (2)
                                                        <span class="label label-important">Error File</span>
                                                        @break
                                                    @case (3)
                                                        <span class="label label-important">Wrong Title Excel</span>
                                                        @break
                                                    @default
                                                        <span class="label">Uploaded</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">
                                            Khong co hanh dong nao trong 30 ngay gan nhat
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{--End Hien thi danh sach up load file gan day nhat--}}

            </div>

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

<script src="{{ asset('source/js/jquery.uniform.js') }}"></script>
<script src="{{ asset('source/js/matrix.form_common.js') }}"></script>

