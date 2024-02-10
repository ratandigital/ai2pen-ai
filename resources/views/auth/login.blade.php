@extends('layouts.guest')
@section('title',__('Login'))
@section('content')
    <h4>{{__("Let's keep going")}}</h4>
    <h6 class="fw-light">{{__('Sign in to continue')}}</h6>
    <form class="pt-3" method="POST" id="" action="{{route('login')}}">
        @csrf
        <div class="form-group">
          <input autofocus type="text" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" placeholder="{{__('Email')}}">
          @if ($errors->has('email'))
              <span class="text-danger">
                  {{ $errors->first('email') }}
              </span>
          @endif
        </div>
        <div class="form-group">
          <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="{{__('Password')}}">      
          @if ($errors->has('password'))
              <span class="text-danger">
                  {{ $errors->first('password') }}
              </span>
          @endif
        </div>
        <div class="mt-3">
          <button id="" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn text-uppercase">{{ __('Sign In') }}</button>
        </div>   
       
        <div class="mt-4 fw-light">
          {{__("Don't have an account?")}} <a href="{{route('register')}}" class="text-primary text-decoration-none">{{__('Create')}}</a>      
          @if (Route::has('password.request'))
              <a href="{{route('password.request')}}" class="auth-link text-black float-lg-end d-block d-lg-inline mt-2 mt-lg-0 text-decoration-none">{{__('Reset Password') }}</a>
          @endif
        </div>
    </form>
@endsection
