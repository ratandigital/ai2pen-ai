@extends('layouts.guest')
@section('title',__('Reset Password'))
@section('content')
    <h4>{{__("Forgot your password?")}}</h4>
    <h6 class="fw-light">{{__('Just let us know your email and we will send a password reset link.')}}</h6>
    <h5 class="text-success mt-3">{{session('status')}}</h5>
    <form class="pt-3" method="POST" id="" action="{{route('password.email')}}">
        @csrf
        <div class="form-group">
          <input autofocus type="text" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" placeholder="{{__('Account Email')}} *">
          @if ($errors->has('email'))
              <span class="text-danger">
                  {{ $errors->first('email') }}
              </span>
          @endif
        </div>
        <div class="mt-3">
          <button id="" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn text-uppercase">{{ __('Request Password Reset') }}</button>
        </div>   
       
        <div class="mt-4 fw-light">
          {{__("Remembered your password?")}} <a href="{{route('login')}}" class="text-primary text-decoration-none">{{__('Sign In')}}</a>
        </div>
    </form>
@endsection
