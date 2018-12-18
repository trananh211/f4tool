@extends('layouts.adminLayout.admin_design')

@section('content')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> 
      <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
      <a href="#">Form elements</a> <a href="#" class="current">Setting</a> 
    </div>
    <h1>Settings</h1>
  </div>
  <div class="container-fluid"><hr>
    @if (Session::has('error'))
    <div class="widget-content">
      <div class="alert alert-error alert-block"> <a class="close" data-dismiss="alert" href="#">×</a>
        <h4 class="alert-heading">Error!</h4>
      {{ Session('error') }}</div>
    </div>
    @endif

    @if (Session::has('success'))
    <div class="widget-content">
      <div class="alert alert-success alert-block"> <a class="close" data-dismiss="alert" href="#">×</a>
        <h4 class="alert-heading">Success!!</h4>
      {{ Session('success') }}</div>
    </div>
    @endif  
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Update Password</h5>
          </div>
          <div class="widget-content nopadding">
            <form class="form-horizontal" method="post" action="{{ url('/admin/update-pwd') }}" name="password_validate" id="password_validate" novalidate="novalidate">
              @csrf
              <div class="control-group">
                <label class="control-label">Current Password</label>
                <div class="controls">
                  <input type="password" name="current_pwd" id="current_pwd" />
                  <span id="js-chkPwd"></span>
                </div>

              </div>
              <div class="control-group">
                <label class="control-label">Password</label>
                <div class="controls">
                  <input type="password" name="pwd" id="pwd" />
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Confirm password</label>
                <div class="controls">
                  <input type="password" name="pwd2" id="pwd2" />
                </div>
              </div>
              <div class="form-actions">
                <input type="submit" value="Validate" class="btn btn-success">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
