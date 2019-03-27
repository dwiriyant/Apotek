<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin {{ ucwords(getToko('nama')) }}</title>

    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png" >

    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/animate/animate.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/css-hamburgers/hamburgers.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/select2/select2.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth.css') }}">

</head>
<body>
    
    <div class="limiter">
        <div class="container-login100">
            @yield('content')
        </div>
    </div>
    
    

    
<!--===============================================================================================-->  
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ asset('bower_components/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
<!--===============================================================================================-->
    <script src="{{ asset('plugins/tilt/tilt.jquery.min.js') }}"></script>
    <script >
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
<!--===============================================================================================-->
    <script src="{{ asset('js/auth.js') }}"></script>

</body>
</html>