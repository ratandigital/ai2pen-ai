@extends('layouts.guest')
@section('title',__('Sign Up'))
@section('content')
    <h4>{{__("New here?")}}</h4>
    <h6 class="fw-light">{{__('Signing up only takes a minute')}}</h6>   
    <form class="pt-3" method="POST" id="register-form">
      <div class="form-group">
        <input autofocus type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name') }}" placeholder="{{__('Full Name')}} *">
      </div>  
      <div class="form-group">
        <input type="text" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" placeholder="{{__('Email')}} *">
      </div>
      <div class="form-group">
        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="{{__('Password')}} *"> 
      </div>
      <div class="form-group">
        <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" placeholder="{{__('Confirm Password')}} *"> 
      </div>
      
      <div class="mb-4">
        <div class="form-check">
          <label class="text-muted">
            <input type="checkbox" class="" name="terms" id="terms" value="1">
            {!!__('I agree to all the :terms',['terms'=>'<a class="text-decoration-none" href="'.route('policy-terms').'">'.__('Terms & Conditions').'</a>'])!!}
          </label>
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn text-uppercase" id="form-submit-button">{{__('Sign Up')}}</button>
      </div>
      <div class="mt-4 fw-light">
        {{__('Already have an account?')}} <a href="{{route('login')}}" class="text-primary text-decoration-none">{{__('Sign In')}}</a>
      </div>
    </form>
@endsection

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/auth/auth.register.js') }}"></script>
@endpush
