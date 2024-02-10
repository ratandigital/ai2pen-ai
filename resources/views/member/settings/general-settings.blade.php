@extends('layouts.auth')
@section('title',__('Settings'))
@section('page-header-title',__('Settings'))
@section('page-header-details',__('All your settings and integrations in one place'))
@section('content')
    <div class="content-wrapper">
        @if (session('save_agency_account_status')=='1')
            <div class="alert alert-success">
                <h4 class="alert-heading">{{__('Successful')}}</h4>
                <p> {{ __('Settings have been saved successfully.') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{__('Something Missing')}}</h4>
                <p> {{ __('Something is missing. Please check the the required inputs.') }}</p>
            </div>
        @endif
        @if (session('save_agency_account_minimun_one_required')=='1')
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{__('No Data')}}</h4>
                <p> {{ __('You must enable at least one email account.') }}</p>
            </div>
        @endif

        <?php
        $xapp_name = $xdata->app_name ?? '';
        $email_settings = isset($xdata->email_settings) ? json_decode($xdata->email_settings) : [];
        $default_email = $email_settings->default ?? '';
        $sender_name = $email_settings->sender_name ??  $xapp_name;
        if(empty($sender_name)) $sender_name = config('app.name');

        $sender_email = $email_settings->sender_email ?? '';
        if(empty($sender_email)) $sender_email = 'no-reply@'.get_domain_only(url('/'));

        $nav_items = [];
        array_push($nav_items, ['tab'=>true,'id'=>'general-tab','href'=>'#general','title'=>__('General'),'subtitle'=>__('Settings'),'icon'=>'fas fa-cog']);

        array_push($nav_items, ['tab'=>true,'id'=>'thirdparty-api-tab','href'=>'#thirdparty-api','title'=>__('AI API'),'subtitle'=>__('Integration'),'icon'=>'fas fa-robot']);

        array_push($nav_items, ['tab'=>true,'id'=>'email-tab','href'=>'#email','title'=>__('Email'),'subtitle'=>__('Integration'),'icon'=>'far fa-envelope']);

        array_push($nav_items, ['tab'=>true,'id'=>'emailauto-tab','href'=>'#emailauto','title'=>__('Responder'),'subtitle'=>__('Integration'),'icon'=>'fas fa-sync']);

        array_push($nav_items, ['tab'=>true,'id'=>'analytics-tab','href'=>'#analytics','title'=>__('Script'),'subtitle'=>__('Analytics'),'icon'=>'fas fa-code']);

        array_push($nav_items, ['tab'=>true,'id'=>'cron-tab','href'=>'#cron','title'=>__('Cron Job'),'subtitle'=>__('Commands'),'icon'=>'fas fa-tasks']);

        array_push($nav_items, ['tab'=>false,'href'=>route('payment-settings',0),'title'=>__('Payment'),'subtitle'=>__('Integration'),'icon'=>'fas fa-credit-card']);

        array_push($nav_items, ['tab'=>false,'href'=>route('agency-landing-editor'),'title'=>__('Landing'),'subtitle'=>__('Page Setup'),'icon'=>'fas fa-home']);

        array_push($nav_items, ['tab'=>false,'href'=>route('languages.index'),'title'=>__('Language'),'subtitle'=>__('Editor'),'icon'=>'fas fa-language','target'=>'_BLANK']);
        ?>
        <form  class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('general-settings-action') }}">
            @csrf
            <div class="home-tab myTab" id="myTab">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach($nav_items as $index=>$nav)
                            <li class="nav-item">
                                <a class="nav-link" href="{{$nav['href']??''}}" id="{{$nav['id']??''}}"  <?php if($nav['tab']) echo 'data-bs-toggle="tab" aria-selected="true" role="tab"'; if(isset($nav['target'])) echo 'target="'.$nav['target'].'"';?>>{{$nav['title']}}<small class="text-muted d-block"><i class="{{$nav['icon']}}"></i> {{$nav['subtitle']}}</small></a>
                            </li>
                        @endforeach
                    </ul>
                    <div>
                        <div class="btn-wrapper">
                            <button type="submit" class="btn btn-success text-white align-items-center"><i class="fas fa-check-circle"></i>{{__('Save Changes')}}</button>
                        </div>
                    </div>
                </div>

                <div class="tab-content tab-content-basic" id="myTabContent">

                    <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row">
                            <div class="col-12 col-lg-4">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <h4 class="card-title  card-title-dash">{{__('Preference')}}</h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Set default system preferences")}}</p>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Timezone') }}</label>
                                                    @php
                                                        $selected = old('timezone', $xdata->timezone ?? '');
                                                        if(empty($selected)) $selected = config('app.timezone');
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
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __("Locale") }} </label>
                                                    <?php echo Form::select('language',$language_list,old('language', $xdata->language ?? 'en'),array('class'=>'form-control form-control-lg select2'));?>
                                                    @if ($errors->has('language'))
                                                        <span class="text-danger"> {{ $errors->first('language') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <?php
                                            $force_email_verify = old('force_email_verify', $xdata->force_email_verify ?? '1');
                                            ?>
                                            <div class="col-12 mt-2">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" id="force_email_verify" name="force_email_verify" type="checkbox" value="1" <?php if($force_email_verify=='1') echo 'checked';?>>
                                                    <label class="form-check-label" for="force_email_verify">{{__("Force Email Verification")}}</label>
                                                </div>
                                                @if ($errors->has('force_email_verify'))
                                                    <span class="text-danger"> {{ $errors->first('force_email_verify') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-8">
                                <div class="card card-rounded mb-4 p-lg-2 mb-4">
                                    <div class="card-body card-rounded">
                                        <h4 class="card-title  card-title-dash">{{__('Brand')}}</h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("White label your app using your branding")}}</p>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Brand name") }} </label>
                                                    <div class="input-group">
                                                        @php
                                                            $app_name = old('app_name', $xapp_name);
                                                            if(empty($app_name)) $app_name = config('app.name');
                                                        @endphp
                                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                        <input name="app_name" value="{{old('app_name',$app_name)}}"  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('app_name'))
                                                        <span class="text-danger"> {{ $errors->first('app_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>


                                            <input type="file" id="logo" class="form-control d-none" name="logo" >
                                            <input type="file" id="logo_alt" class="form-control d-none" name="logo_alt" >
                                            <input type="file" id="favicon" class="form-control d-none" name="favicon" >
                                            <input type="file" id="ai_chat_icon" class="form-control d-none" name="ai_chat_icon" >

                                            <div class="col-12 col-md-6 col-xl-5">
                                                <div class="form-group">
                                                    <?php $logo  = !empty($xdata->logo) ? $xdata->logo : asset('assets/images/logo.png');?>
                                                    <label for="">{{ __("Logo") }} <small><small>500x100</small></small></label>
                                                    <div class="position-relative img-edit">
                                                        <img src="{{$logo}}" class="rounded border cursor-pointer w-100" height="80px" onclick="document.getElementById('logo').click()">
                                                        <a href="#" class="position-absolute top-0 end-0">
                                                            <i class="fa-solid fa-square-pen text-muted" onclick="document.getElementById('logo').click()"></i>
                                                        </a>
                                                        @if ($errors->has('logo'))
                                                            <span class="text-danger"> {{ $errors->first('logo') }} </span>
                                                        @endif
                                                       </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 col-xl-5">
                                                <div class="form-group">
                                                    <?php $logo_alt  = !empty($xdata->logo_alt) ? $xdata->logo_alt : asset('assets/images/logo-white.png');?>
                                                    <label for="">{{ __("White Logo") }} <small><small>500x100</small></small></label>
                                                    <div class="position-relative img-edit">
                                                        <img src="{{$logo_alt}}" class="rounded cursor-pointer bg-primary w-100" height="80px" onclick="document.getElementById('logo_alt').click()">
                                                        <a href="#" class="position-absolute top-0 end-0">
                                                            <i class="fa-solid fa-square-pen text-white" onclick="document.getElementById('logo_alt').click()"></i>
                                                        </a>
                                                        @if ($errors->has('logo_alt'))
                                                            <span class="text-danger"> {{ $errors->first('logo_alt') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-6 col-xl-2">
                                                <div class="form-group">
                                                    <?php $favicon  = !empty($xdata->favicon) ? $xdata->favicon : asset('assets/images/favicon.png'); ?>
                                                    <label for="">{{ __("Favicon") }} <small><small>100x100</small></small></label>
                                                    <div class="position-relative img-edit w-80px">
                                                        <img src="{{$favicon}}" class="rounded border cursor-pointer" height="80px" width="80px" onclick="document.getElementById('favicon').click()">
                                                        <a href="#" class="position-absolute top-0 end-0">
                                                            <i class="fa-solid fa-square-pen text-muted" onclick="document.getElementById('favicon').click()"></i>
                                                        </a>
                                                        @if ($errors->has('favicon'))
                                                            <span class="text-danger"> {{ $errors->first('favicon') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6 col-md-6 col-xl-2">
                                                <div class="form-group">
                                                    <?php $ai_chat_icon  = !empty($xdata->ai_chat_icon) ? $xdata->ai_chat_icon : asset('assets/images/ai_chat_icon.png'); ?>
                                                    <label for="">{{ __("Ai Chat Icon") }} <small><small>22x22</small></small></label>
                                                    <div class="position-relative img-edit w-80px">
                                                        <img src="{{$ai_chat_icon}}" class="rounded border cursor-pointer" height="80px" width="80px" onclick="document.getElementById('ai_chat_icon').click()">
                                                        <a href="#" class="position-absolute top-0 end-0">
                                                            <i class="fa-solid fa-square-pen text-muted" onclick="document.getElementById('ai_chat_icon').click()"></i>
                                                        </a>
                                                        @if ($errors->has('ai_chat_icon'))
                                                            <span class="text-danger"> {{ $errors->first('ai_chat_icon') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="cron" role="tabpanel" aria-labelledby="cron-tab">

                        <div class="card card-rounded mb-4 p-lg-2">
                            <div class="card-body card-rounded">
                                <h4 class="card-title card-title-dash">{{__('Cron Commands')}}</h4>
                                <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Set task scheduler in your server")}}</p>
                                @if(check_build_version()=='double')
                                <div class="list align-items-center border-bottom py-2">
                                    <div class="wrapper w-100">
                                        <p class="mb-2 font-weight-medium">
                                            {{__('PayPal Subscription (every 5 minutes)')}}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <p class="mb-0 text-small text-muted">
                                                   curl {{route('get-paypal-subscriber-transaction')}} >/dev/null 2>&1
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="list align-items-center border-bottom py-2">
                                    <div class="wrapper w-100">
                                        <p class="mb-2 font-weight-medium">
                                            {{__('Clean Junk Data (every day)')}}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <p class="mb-0 text-small text-muted">
                                                    curl {{route('cron-clean-junk-data')}} >/dev/null 2>&1
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">

                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <h4 class="card-title card-title-dash">{{__('Default Profile')}}</h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Set default email profile to send mails")}}</p>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="default_email" >{{ __('Default Profile') }} *</label>
                                                    <div class="form-group">
                                                        <div class="input-group" id="default-main-container">
                                                        </div>
                                                        @if ($errors->has('default_email'))
                                                            <span class="text-danger"> {{ $errors->first('default_email') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Default Sender Email") }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                                        <input name="sender_email" value="{{$sender_email}}"  class="form-control form-control-lg" type="email">
                                                    </div>
                                                    @if ($errors->has('sender_email'))
                                                        <span class="text-danger"> {{ $errors->first('sender_email') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Default Sender Name") }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                                        <input name="sender_name" value="{{$sender_name}}"  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('sender_name'))
                                                        <span class="text-danger"> {{ $errors->first('sender_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <div class="d-sm-flex justify-content-between align-items-start">
                                            <div>
                                                <h4 class="card-title card-title-dash">{{__('Email Profile')}}</h4>
                                                <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your email profile")}}</p>
                                            </div>
                                            @if(has_module_action_access($module_id_settings,1,$team_access,$is_manager))
                                            <div class="btn-wrapper mb-2">
                                                <a id="new-profile" href="#" class="btn btn-otline-dark btn-sm mb-0 me-0"><i class="mdi mdi-plus-circle"></i> {{__('New')}}</a>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-select' id="mytable" >
                                                <thead>
                                                <tr class="table-light">
                                                    <th>#</th>
                                                    <th>
                                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                                    </th>
                                                    <th>{{__("Profile Name") }}</th>
                                                    <th>{{__("API Name") }}</th>
                                                    <th>{{__("Updated at") }}</th>
                                                    <th>{{__("Actions") }}</th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="thirdparty-api" role="tabpanel" aria-labelledby="thirdparty-api-tab">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <h4 class="card-title card-title-dash">{{__('Default Profile')}}</h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Select default AI profile to use")}}</p>
                                        <div class="form-group">
                                            <label for="default_ai_api" >{{ __('Default Profile') }} </label>
                                            <div class="form-group">
                                                <div class="input-group" id="default-main-container2">
                                                </div>
                                                @if ($errors->has('default_ai_api'))
                                                    <span class="text-danger"> {{ $errors->first('default_ai_api') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <div class="d-sm-flex justify-content-between align-items-start">
                                            <div>
                                                <h4 class="card-title card-title-dash">{{__('AI API Profile')}}</h4>
                                                <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your AI API profile")}}</p>
                                            </div>
                                            @if(has_module_action_access($module_id_settings,1,$team_access,$is_manager))
                                            <div class="btn-wrapper mb-2">
                                                <a id="new-thirdparty-api-profile" href="#" class="btn btn-otline-dark btn-sm mb-0 me-0"><i class="mdi mdi-plus-circle"></i> {{__('New')}}</a>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-select' id="mytable4" >
                                                <thead>
                                                <tr class="table-light">
                                                    <th>#</th>
                                                    <th>
                                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                                    </th>
                                                    <th>{{__("Profile Name") }}</th>
                                                    <th>{{__("API Name") }}</th>
                                                    <th>{{__("Updated at") }}</th>
                                                    <th>{{__("Actions") }}</th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="emailauto" role="tabpanel" aria-labelledby="emailauto-tab">

                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <h4 class="card-title card-title-dash">{{__('Signup Integration')}}</h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Sync signed up user list to auto responders")}}</p>
                                        @foreach ($autoresponser_dropdown_values as $key_root => $value_root)
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="default_email" >{{ __(ucfirst($key_root)) }} {{__('List')}}</label>
                                                    <div class="form-group">
                                                        <select class="form-control form-control-lg select2" id="" name="auto_responder_signup_settings[]" multiple>
                                                            <?php
                                                            foreach ($value_root as $key => $value)
                                                            {
                                                                if(!isset($value['data'])) continue;
                                                                echo '<optgroup label="'.addslashes($value['api_name']).' : '.addslashes($value['profile_name']).'">';

                                                                foreach ($value['data'] as $key2 => $value2)
                                                                {
                                                                    $selected = '';
                                                                    if(isset($value_root['selected']) && in_array($value2['table_id'], $value_root['selected'])) $selected = 'selected';
                                                                    echo "<option value='".$value2['table_id']."-".$key_root."' ".$selected.">".$value2['list_name']."</option>";
                                                                }
                                                                echo '</optgroup>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <div class="d-sm-flex justify-content-between align-items-start">
                                            <div>
                                                <h4 class="card-title card-title-dash">{{__('Auto Responder Profile')}}</h4>
                                                <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your auto responder profile")}}</p>
                                            </div>
                                            @if(has_module_action_access($module_id_settings,1,$team_access,$is_manager))
                                            <div class="btn-wrapper mb-2">
                                                <a id="new-auto-profile" href="#" class="btn btn-otline-dark btn-sm mb-0 me-0"><i class="mdi mdi-plus-circle"></i> {{__('New')}}</a>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-select' id="mytable2" >
                                                <thead>
                                                <tr class="table-light">
                                                    <th>#</th>
                                                    <th>
                                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                                    </th>
                                                    <th>{{__("Profile Name") }}</th>
                                                    <th>{{__("API Name") }}</th>
                                                    <th>{{__("Updated at") }}</th>
                                                    <th>{{__("Actions") }}</th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card card-rounded mb-4 p-lg-2">
                                    <div class="card-body card-rounded">
                                        <h4 class="card-title card-title-dash">{{__('Script and Analytics')}}</h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Add analytics script to track visitors")}}</p>
                                        <?php
                                        $analytics_data = isset($xdata->analytics_code) ? json_decode($xdata->analytics_code,true):[];
                                        $fb_pixel_id = isset($analytics_data['fb_pixel_id']) ? $analytics_data['fb_pixel_id']:"";
                                        $google_analytics_id = isset($analytics_data['google_analytics_id']) ? $analytics_data['google_analytics_id']:"";
                                        $tme_widget_id = isset($analytics_data['tme_widget_id']) ? $analytics_data['tme_widget_id']:"";
                                        $whatsapp_widget_id = isset($analytics_data['whatsapp_widget_id']) ? $analytics_data['whatsapp_widget_id']:"";
                                        ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Facebook Pixel Id") }}</label>
                                                    <input type="text" class="form-control form-control-lg" id="fb_pixel_id" name="fb_pixel_id" value="{{ old('fb_pixel_id', $fb_pixel_id) }}" placeholder="{{ __('Ex: 342869686661279') }}">

                                                    @if ($errors->has('fb_pixel_id'))
                                                        <span class="text-danger"> {{ $errors->first('fb_pixel_id') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Google Analytics Id") }}</label>
                                                    <input type="text" class="form-control form-control-lg" id="google_analytics_id" name="google_analytics_id" value="{{ old('google_analytics_id', $google_analytics_id) }}" placeholder="{{ __('Ex: G-Z2TZKBFV49') }}">

                                                    @if ($errors->has('google_analytics_id'))
                                                        <span class="text-danger"> {{ $errors->first('google_analytics_id') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>


    <div class="modal fade" id="email_settings_modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Email Profile')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <input type="hidden" id="update-id" value="0">

                    <div class="row">
                        <div class="col-7 col-md-9">
                            <div class="tab-content border-0" id="v-pills-tabContent">
                                <div class="tab-pane active show" id="smtp-block" role="tabpanel" aria-labelledby="">
                                    <form id="smtp-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Host") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-server"></i></span>
                                                        <input name="host" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('host'))
                                                        <span class="text-danger"> {{ $errors->first('host') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ __("Username") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                        <input name="username" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('username'))
                                                        <span class="text-danger"> {{ $errors->first('username') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ __("Password") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="password" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('password'))
                                                        <span class="text-danger"> {{ $errors->first('password') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ __("Port") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-plug"></i></span>
                                                        <input name="port" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('port'))
                                                        <span class="text-danger"> {{ $errors->first('port') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ __("Encryption") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                                        <?php echo Form::select('encryption',array(''=>'Default','tls'=>"TLS",'ssl'=>"SSL"),'',array('class'=>'form-control form-control-lg','not-required'=>'true')); ?>
                                                    </div>
                                                    @if ($errors->has('encryption'))
                                                        <span class="text-danger"> {{ $errors->first('encryption') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="mailgun-block" role="tabpanel" aria-labelledby="">
                                    <form id="mailgun-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Domain") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-server"></i></span>
                                                        <input name="domain" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('domain'))
                                                        <span class="text-danger"> {{ $errors->first('domain') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Secret") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="secret" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('secret'))
                                                        <span class="text-danger"> {{ $errors->first('secret') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Endpoint") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-genderless"></i></span>
                                                        <input name="endpoint" value="api.eu.mailgun.net"  class="form-control form-control-lg" type="text" reset="false">
                                                    </div>
                                                    @if ($errors->has('endpoint'))
                                                        <span class="text-danger"> {{ $errors->first('endpoint') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="postmark-block" role="tabpanel" aria-labelledby="">
                                    <form id="postmark-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Token") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="token" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('token'))
                                                        <span class="text-danger"> {{ $errors->first('token') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="ses-block" role="tabpanel" aria-labelledby="">
                                    <form id="ses-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Key") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                        <input name="key" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('key'))
                                                        <span class="text-danger"> {{ $errors->first('key') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Secret") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="secret" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('secret'))
                                                        <span class="text-danger"> {{ $errors->first('secret') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Region") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-genderless"></i></span>
                                                        <input name="region" value="us-east-1"  class="form-control form-control-lg" type="text" reset="false">
                                                    </div>
                                                    @if ($errors->has('region'))
                                                        <span class="text-danger"> {{ $errors->first('region') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="mandrill-block" role="tabpanel" aria-labelledby="">
                                    <form id="mandrill-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Key") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="secret" value=""  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('secret'))
                                                        <span class="text-danger"> {{ $errors->first('secret') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="col-5 col-md-3">
                            <div class="nav d-block nav-pills email-block" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="d-block nav-link active" data-bs-toggle="pill" href="#smtp-block" id="smtp-block-link" role="tab" aria-controls="" aria-selected="true">{{__('SMTP')}}</a>
                                <a class="nav-link" data-bs-toggle="pill"  href="#mailgun-block" id="mailgun-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mailgun')}}</a>
                                <a class="nav-link" data-bs-toggle="pill"  href="#postmark-block"  id="postmark-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Postmark')}}</a>
                                <a class="nav-link" data-bs-toggle="pill"  href="#ses-block" id="ses-block-link" role="tab" aria-controls="" aria-selected="true">{{__('SES')}}</a>
                                <a class="nav-link" data-bs-toggle="pill"  href="#mandrill-block" id="mandrill-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mandril')}}</a>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="modal-footer d-block">
                    <button type="button" class="btn btn-success float-start btn-sm" id="save_email_settings"><i class="fas fa-check-circle"></i> {{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sms_settings_modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('SMS Profile')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                       <input type="hidden" id="sms-update-id" value="0">

                        <div class="row">
                            <div class="col-7 col-md-9">
                                <div class="tab-content border-0" id="v-pills-tabContent">
                                    <div class="tab-pane active show" id="plivo-block" role="tabpanel" aria-labelledby="">
                                        <form id="plivo-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Auth ID") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                            <input name="auth_id" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('auth_id'))
                                                            <span class="text-danger"> {{ $errors->first('auth_id') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Auth Token") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="auth_token" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('auth_token'))
                                                            <span class="text-danger"> {{ $errors->first('auth_token') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Sender/From") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                            <input name="sender" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('sender'))
                                                            <span class="text-danger"> {{ $errors->first('sender') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="twilio-block" role="tabpanel" aria-labelledby="">
                                        <form id="twilio-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Auth SID") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                            <input name="auth_sid" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('auth_sid'))
                                                            <span class="text-danger"> {{ $errors->first('auth_sid') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Auth Token") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="auth_token" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('auth_token'))
                                                            <span class="text-danger"> {{ $errors->first('auth_token') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Sender/From") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                            <input name="sender" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('sender'))
                                                            <span class="text-danger"> {{ $errors->first('sender') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="nexmo-block" role="tabpanel" aria-labelledby="">
                                        <form id="nexmo-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                            <input name="api_key" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('api_key'))
                                                            <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API Secret") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input name="api_secret" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('api_secret'))
                                                            <span class="text-danger"> {{ $errors->first('api_secret') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Sender/From") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                            <input name="sender" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('sender'))
                                                            <span class="text-danger"> {{ $errors->first('sender') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="clickatell-block" role="tabpanel" aria-labelledby="">
                                        <form id="clickatell-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API ID") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                            <input name="api_id" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('api_id'))
                                                            <span class="text-danger"> {{ $errors->first('api_id') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="africastalking-block" role="tabpanel" aria-labelledby="">
                                        <form id="africastalking-block-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Profile Name") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                            <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                        </div>
                                                        @if ($errors->has('profile_name'))
                                                            <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("API Key") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fab fa-keycdn"></i></span>
                                                            <input name="api_key" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('api_key'))
                                                            <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __("Sender (Username)") }} *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                            <input name="sender" value=""  class="form-control form-control-lg" type="text">
                                                        </div>
                                                        @if ($errors->has('sender'))
                                                            <span class="text-danger"> {{ $errors->first('sender') }} </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <div class="col-5 col-md-3">
                                <div class="nav d-block nav-pills sms-block" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="d-block nav-link active" data-bs-toggle="pill" href="#plivo-block" id="plivo-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Plivo')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#twilio-block" id="twilio-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Twilio')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#nexmo-block"  id="nexmo-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Nexmo/Vonage')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#clickatell-block" id="clickatell-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Clickatell')}}</a>
                                    <a class="nav-link" data-bs-toggle="pill"  href="#africastalking-block" id="africastalking-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Africastalking')}}</a>
                                </div>
                            </div>
                        </div>


                    </div>

                <div class="modal-footer d-block">
                    <button type="button" class="btn btn-success float-start btn-sm" id="save_sms_settings"><i class="fas fa-check-circle"></i> {{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="email_auto_settings_modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Auto Responder Profile')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="auto-update-id" value="0">
                    <div class="row">
                        <div class="col-7 col-md-9">
                            <div class="tab-content border-0" id="v-pills-tabContent">
                                <div class="tab-pane active show" id="mailchimp-block" role="tabpanel" aria-labelledby="">
                                    <form id="mailchimp-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("API Key") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="api_key" value="" non-editable="true"  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('api_key'))
                                                        <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="sendinblue-block" role="tabpanel" aria-labelledby="">
                                    <form id="sendinblue-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("API Key") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="api_key" value="" non-editable="true"  class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('api_key'))
                                                        <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="activecampaign-block" role="tabpanel" aria-labelledby="">
                                    <form id="activecampaign-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("API Key") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="api_key" value="" non-editable="true" class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('api_key'))
                                                        <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("API URL") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                        <input name="api_url" value="" non-editable="true" class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('api_url'))
                                                        <span class="text-danger"> {{ $errors->first('api_url') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="mautic-block" role="tabpanel" aria-labelledby="">
                                    <form id="mautic-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ __("Username") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fab fa-user"></i></span>
                                                        <input name="username" value="" non-editable="true" class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('username'))
                                                        <span class="text-danger"> {{ $errors->first('username') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ __("Password") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="password" value="" non-editable="true" class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('password'))
                                                        <span class="text-danger"> {{ $errors->first('password') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Base URL") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                        <input name="base_url" value="" non-editable="true" class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('base_url'))
                                                        <span class="text-danger"> {{ $errors->first('base_url') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="acelle-block" role="tabpanel" aria-labelledby="">
                                    <form id="acelle-block-form">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("Profile Name") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                        <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name to identify it later')}}">
                                                    </div>
                                                    @if ($errors->has('profile_name'))
                                                        <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("API Key") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                        <input name="api_key" value="" non-editable="true" class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('api_key'))
                                                        <span class="text-danger"> {{ $errors->first('api_key') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{ __("API URL") }} *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                        <input name="api_url" value="" non-editable="true" class="form-control form-control-lg" type="text">
                                                    </div>
                                                    @if ($errors->has('api_url'))
                                                        <span class="text-danger"> {{ $errors->first('api_url') }} </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="col-5 col-md-3">
                            <div class="nav d-block nav-pills email-auto-block" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="d-block nav-link active" data-bs-toggle="pill" href="#mailchimp-block" id="mailchimp-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mailchimp')}}</a>
                                <a class="nav-link" data-bs-toggle="pill"  href="#sendinblue-block" id="sendinblue-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Sendinblue')}}</a>
                                <a class="nav-link" data-bs-toggle="pill"  href="#activecampaign-block"  id="activecampaign-block-link" role="tab" aria-controls="" aria-selected="true">{{__('ActiveCampaign')}}</a>
                                <a class="nav-link" data-bs-toggle="pill"  href="#mautic-block" id="mautic-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Mautic')}}</a>
                                {{--<a class="nav-link" data-bs-toggle="pill"  href="#acelle-block" id="acelle-block-link" role="tab" aria-controls="" aria-selected="true">{{__('Acelle')}}</a>--}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-block">
                    <button type="button" class="btn btn-success float-start btn-sm" id="save_email_auto_settings"><i class="fas fa-check-circle"></i> {{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>
    @include('member.settings.thirdparty-api-settings')
@endsection

@push('scripts-footer')
    @include('member.settings.general-settings-js')
    <script src="{{ asset('assets/js/pages/member/settings.general-settings.js') }}"></script>
    <script src="{{ asset('assets/js/pages/member/settings.thirdparty-api-settings.js') }}"></script>
@endpush
