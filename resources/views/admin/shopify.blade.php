@extends('layouts.adminLayout.admin_design')

@section('content')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> 
      <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
      <a href="#">Form elements</a> <a href="#" class="current">Spy Shopify</a> 
    </div>
    <h1>Spy Shopify</h1>
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
        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
          <h5>Personal-info</h5>
        </div>
        <div class="widget-content nopadding">
          <form action="#" class="form-horizontal">
            <div class="control-group">
              <label class="control-label">Domain Spy :</label>
              <div class="controls">
                <input type="text" name="domain" class="span11 tip-top" id="js-domain" title="Example: abc.xyx => Do not fill http or https" placeholder="abc.xyz" value="{{ isset($data['domain']) ? $data['domain'] : '' }}">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Page :</label>
              <div class="controls">
                <input type="number" name="page" id="js-page" value='{{ isset($data['page']) ? $data['page'] : '1' }}' class="span3" placeholder="Only Number">
              </div>
            </div>
            
            <div class="form-actions">
              <button class="btn btn-success js-checkShopify" >Check</button>
            </div>
          </form>
        </div>
      </div>
      </div>
    </div>
    
  </div>
</div>
@endsection

