@extends('layouts.guest')
@section('title',__('Reset Password'))
@section('content')
    <h4>{{__("Set a new password")}}</h4>
    <h6 class="fw-light">{{__('Regain access to your account')}}</h6>   
    <form class="pt-3" method="POST" action="{{route('password.update')}}">
      @csrf

      <input type="hidden" id="token" name="token" value="{{ $request->route('token') }}">
      <div class="form-group">
        <input type="text" class="form-control form-control-lg" id="email" name="email" value="{{ old('email', $request->email) }}" placeholder="{{__('Email')}} *">
        @if ($errors->has('email'))
            <span class="text-danger">
                {{ $errors->first('email') }}
            </span>
        @endif
      </div>
      <div class="form-group">
        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="{{__('Password')}} *">
        @if ($errors->has('password'))
            <span class="text-danger">
                {{ $errors->first('password') }}
            </span>
        @endif 
      </div>
      <div class="form-group">
        <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" placeholder="{{__('Confirm Password')}} *">
        @if ($errors->has('password_confirmation'))
        <span class="text-danger">
            {{ $errors->first('password_confirmation') }}
        </span>
        @endif
      </div>

      <div class="mt-3">
        <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn text-uppercase" id="">{{__('Reset Password')}}</button>
      </div>

    </form>
@endsection

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/auth/auth.register.js') }}"></script>
@endpush
