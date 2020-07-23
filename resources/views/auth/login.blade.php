@extends('layouts.auth')

@section('content')
<div class="wrap-login100">
    <div class="login100-pic js-tilt" data-tilt>
        <img src="{{ asset('img/logo.png') }}" alt="IMG">
    </div>

    <form method="POST" action="{{ route('login') }}" class="login100-form validate-form">
        @csrf
        
        <span class="login100-form-title">
            Member Login
        </span>

        @if (count($errors) > 0)
        <div class="alert alert-danger">
            
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            
        </div>
        @endif

        <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
            <input id="email" type="text" class="input100{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Email / Username" name="username" value="{{ old('email') }}" required autofocus>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>

            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="wrap-input100 validate-input" data-validate = "Password is required">
            <input id="password" type="password" class="input100{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
            </span>

            @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif

        </div>
        
        <div class="container-login100-form-btn">
            <button class="login100-form-btn">
                Login
            </button>
        </div>
        <div class="text-center p-t-136">
            <a class="txt2" target="_blank" href="https://colorlib.com">
                Login Page by Colorlib
            </a>
        </div>
    </form>
</div>

@endsection
