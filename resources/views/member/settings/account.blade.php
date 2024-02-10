@extends('layouts.auth')
@section('title',__('My Profile'))
@section('page-header-title',__('Hello').', '.Auth()->user()->name)
@section('page-header-details',__('Your account, subscription and usage summary at a glance'))
@section('content')
    <div class="content-wrapper">
        @if (session('save_user_profile')=='1')
            <div class="alert alert-success">
                <h4 class="alert-heading">{{__('Account Updated')}}</h4>
                <p> {{ __('Account has been updated successfully.') }}</p>
            </div>
        @endif

        @php
            $profile_pic  = !empty($data->profile_pic) ? $data->profile_pic : asset('assets/images/avatar/avatar-6.png');
        @endphp

        @if($is_member && !$is_manager)
            @include('member.payment.usage-log.stat')
        @endif

        <div class="row">
            <div class="col-lg-6">
                <div class="card card-rounded mb-4">
                    <div class="card-body card-rounded">
                        <h4 class="card-title  card-title-dash">{{__("Update Profile")}}</h4>
                        <p class="card-subtitle card-subtitle-dash">{{__("Update personal information and password")}}</p>
                        <div class="d-flex align-items-center py-4">
                            <div class="position-relative img-edit">
                                <img src="{{$profile_pic}}" class="rounded img-thumbnail img-lg cursor-pointer" onclick="document.getElementById('profile_pic').click()">
                                <a href="#" class="position-absolute top-0 end-0">
                                    <i class="fa-solid fa-square-pen text-success" onclick="document.getElementById('profile_pic').click()"></i>
                                </a>
                            </div>
                            <div class="mb-3 ms-4 pt-4">
                                <h3>{{Auth::user()->name}}</h3>
                                <h5 class="me-2 text-muted">{{Auth::user()->email}}</h5>
                            </div>
                        </div>

                        <form class="form form-vertical mb-4" enctype="multipart/form-data" method="POST" action="{{ route('account-action') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Name') }}* </label>
                                                    <div class="input-group">
                                                        <div class="input-group-text"><i class="far fa-user"></i></div>
                                                        <input type="text" id="first-name" class="form-control form-control-lg" name="name" value="{{old('name', $data->name)}}">
                                                    </div>
                                                    @if ($errors->has('name'))
                                                        <span class="text-danger"> {{ $errors->first('name') }} </span>
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Email') }}*</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text"><i class="far fa-envelope"></i></div>
                                                        <input type="email" id="email-id" class="form-control form-control-lg" name="email" value="{{old('email', $data->email)}}">
                                                    </div>
                                                    @if ($errors->has('email'))
                                                        <span class="text-danger"> {{ $errors->first('email') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Mobile') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text"><i class="fas fa-mobile-alt"></i></div>
                                                        <input type="text" id="contact-info" class="form-control form-control-lg" name="mobile" value="{{old('mobile', $data->mobile)}}">
                                                    </div>
                                                    @if ($errors->has('mobile'))
                                                        <span class="text-danger"> {{ $errors->first('mobile') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Password') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text"><i class="fas fa-key"></i></div>
                                                        <input type="password" id="password" class="form-control form-control-lg" name="password" placeholder="******">
                                                    </div>
                                                    @if ($errors->has('password'))
                                                        <span class="text-danger"> {{ $errors->first('password') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Confirm Password') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text"><i class="fas fa-key"></i></div>
                                                        <input type="password" id="password_confirmation" class="form-control form-control-lg" name="password_confirmation" placeholder="******">
                                                    </div>
                                                    @if ($errors->has('password_confirmation'))
                                                        <span class="text-danger"> {{ $errors->first('mobile') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Address') }}</label>
                                                    <textarea id="address"  class="form-control form-control-lg" name="address">{{old('address', $data->address)}}</textarea>
                                                    @if ($errors->has('address'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('address') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ __('Timezone') }}</label>
                                                    @php
                                                        $select_timezone = !empty($data->timezone) ? $data->timezone : config('app.userTimezone');
                                                        $selected = old('timezone', $select_timezone);
                                                        if($selected=='UTC') $selected='Europe/Dublin';
                                                        $timezone_list = get_timezone_list();
                                                        echo Form::select('timezone',$timezone_list,$selected,array('class'=>'form-control form-control-lg select2'));
                                                    @endphp
                                                    @if ($errors->has('timezone'))
                                                        <span class="text-danger">
                                                                {{ $errors->first('timezone') }}
                                                            </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ __('Language') }}</label>
                                                    @php
                                                        $selected_language = !empty($data->language) ? $data->language : config('app.locale');
                                                        $selected = old('language', $selected_language);
                                                        echo Form::select('language',$language_list,$selected,array('class'=>'form-control form-control-lg select2'));
                                                    @endphp
                                                    @if ($errors->has('language'))
                                                        <span class="text-danger">
                                                                {{ $errors->first('language') }}
                                                            </span>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group mt-1">
                                                    <label>{{ __('Avatar') }}</label>
                                                    <input type="file" id="profile_pic" class="form-control" name="profile_pic" >
                                                    <div>
                                                        @if ($errors->has('profile_pic'))
                                                            <span class="text-danger"> {{ $errors->first('profile_pic') }} </span>
                                                        @else
                                                            <small class="text-muted">png/jpg/webp, {{__('Square Image')}} </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary me-1 mb-1"><i class="fas fa-edit"></i> {{ __('Update') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                @if($is_member)
                    @include('member.payment.usage-log.list')
                @endif

                <div class="card card-rounded mb-4">
                    <div class="card-body card-rounded">
                        <h4 class="card-title card-title-dash">{{__('Summary')}}</h4>
                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Your account at a glance")}}</p>
                        <?php $last_logged_in = convert_datetime_to_timezone(Auth::user()->last_login_at,'','','jS M y H:i A')?>
                        <?php $created_at = convert_datetime_to_timezone(Auth::user()->created_at,'','','jS M y H:i A')?>
                        <?php $email_verified_at = !empty(Auth::user()->email_verified_at) ? convert_datetime_to_timezone(Auth::user()->email_verified_at,'','','jS M y H:i A') : __('Not verified');?>
                        <?php $details = [
                            1 => ['icon'=>'fas fa-user-circle','value'=>Auth::user()->status=='1' ? __('Active') : __('Inactive'),'index'=> __('Status')],
                            2 => ['icon'=>'fa-regular fa-calendar-check','value'=>$created_at,'index'=>__('Singed up at')],
                            3 => ['icon'=>'fas fa-envelope','value'=>$email_verified_at,'index'=> __('Email verified at')],
                            4 => ['icon'=>'fas fa-sign-in','value'=>$last_logged_in,'index'=>__('Last logged in at')],
                            5 => ['icon'=>'fa-solid fa-network-wired','value'=>Auth::user()->last_login_ip,'index'=>__('Last login IP')],
                        ];
                        if(!$is_admin){
                            $details[] = ['icon'=>'fas fa-credit-card','value'=>!empty(Auth::user()->last_payment_method)?Auth::user()->last_payment_method:__('None'),'index'=>__('Last payment method')];
                            $details[] = ['icon'=>'fas fa-coins','value'=>Auth::user()->subscription_enabled=='1' ? __('Enabled') : __('None'),'index'=>__('Subscription')];
                        }
                        ?>
                        @foreach($details as $detail)
                            <div class="list align-items-center border-bottom py-2">
                                <div class="wrapper w-100">
                                    <p class="mb-2 font-weight-medium">
                                        {{$detail['index']}}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="{{$detail['icon']??'fas fa-circle'}} text-muted me-1"></i>
                                            <p class="mb-0 text-small text-muted">{{$detail['value']}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
