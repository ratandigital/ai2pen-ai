@extends('layouts.guest')
@section('title',__('Email Verification'))
@section('content')
    <h4>{{__("Verify your email")}} : {{Auth::user()->email}}</h4>
    <h6 class="fw-light">{{__('We will send an verification link to your registered email')}}</h6>
    @if (session('status') == 'verification-link-sent')
    <div class="mt-4 alert alert-success">
        {{ __("An email verification link has been sent to your email. Please verify your email address by clicking on the link we just emailed to you. We will gladly send you another email if you didn't receive the first one, but please first check your spam folder.") }}
    </div>
    @endif
    <form class="pt-3" method="POST" action="{{route('verification.send')}}">
      @csrf
      <div class="">
        <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn text-uppercase w-100" id="">{{__('Start Verification')}}</button>
      </div>

    </form>
@endsection
