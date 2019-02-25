@if (Session::has('error'))
    <div class="widget-content">
        <div class="alert alert-error alert-block"><a class="close" data-dismiss="alert" href="#">×</a>
            <h4 class="alert-heading">Error!</h4>
            {{ Session('error') }}</div>
    </div>
@endif

@if (Session::has('success'))
    <div class="widget-content">
        <div class="alert alert-success alert-block"><a class="close" data-dismiss="alert" href="#">×</a>
            <h4 class="alert-heading">Success!!</h4>
            {{ Session('success') }}</div>
    </div>
@endif
