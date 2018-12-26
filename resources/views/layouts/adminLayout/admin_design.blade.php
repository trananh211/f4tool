<!DOCTYPE html>
<html lang="en">
<head>
    <title>Matrix Admin</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="{{ asset('source/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('source/css/bootstrap-responsive.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('source/css/fullcalendar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('source/css/matrix-style.css') }}"/>
    <link rel="stylesheet" href="{{ asset('source/css/matrix-media.css') }}"/>
    <link rel="stylesheet" href="{{ asset('source/font-awesome/css/font-awesome.css') }}"/>
    <link rel="stylesheet" href="{{ asset('source/css/jquery.gritter.css') }}"/>
    <link rel="stylesheet" href="{{ asset('source/css/my-custom.css') }}"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
@include('layouts.adminLayout.admin_header')

@include('layouts.adminLayout.admin_sidebar')

@yield('content')
<!--end-main-container-part-->
@include('layouts.adminLayout.admin_footer')
{{--@include('layouts.adminLayout.admin_script')--}}

</body>
</html>
