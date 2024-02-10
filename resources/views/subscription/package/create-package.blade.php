<?php
$action_type = request()->type;
if(empty($action_type)) $action_type = 'subscription';
$title_display = $action_type=='team' ? __('Create Team Role') : __('Create Subscription Package');
$title_display_des = $action_type=='team' ? __('Create a new team role') : __('Create a subscription package');
$paypal_data = isset($payment_config->paypal) ? json_decode($payment_config->paypal) : null;
$paypal_access = isset($paypal_data->paypal_status) && $paypal_data->paypal_status=='1' ? true : false;    
$integration_access = $paypal_access ? true : false;
$access_array = ["1"=>__("Create"),"2"=>__("Update"),"3"=>__("Delete"),"4"=>__("Special")];
?>
@extends('layouts.auth')
@section('title',$title_display)
@section('page-header-title',$title_display)
@section('page-header-details',__('Create a new access level'))
@section('content')
<div class="content-wrapper">
    <form class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('create-package-action') }}">
        @csrf
        <input type="hidden" name="package_type" id="package_type" value="{{$action_type}}">
        <div class="row">
            <div class='col-12 col-md-7'>

                <div class="card card-rounded mb-4 p-lg-2">
                    <div class="card-body card-rounded">
                        <h4 class="card-title card-title-dash mb-4">{{__('Define Access Level')}}</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name"> {{ $action_type=='team' ? __("Team Role Name") : __("Subscription Package Name") }}*</label>
                                    <input name="package_name" class="form-control form-control-lg form-control form-control-lg-lg" type="text" value="{{old('package_name')}}">
                                    @if ($errors->has('package_name'))
                                        <span class="text-danger"> {{ $errors->first('package_name') }} </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($action_type!='team')
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="">{{__('Price')}} *</label>
                                    <div class="input-group">
                                        <input name="price"  class="form-control form-control-lg" type="text" value="{{old('price')}}">
                                        <span class="input-group-text"><?php echo isset($payment_config->currency) ? $payment_config->currency : 'USD'; ?></span>
                                    </div>
                                    @if ($errors->has('price'))
                                        <span class="text-danger"> {{ $errors->first('price') }} </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="">{{__('Validity')}} *</label>
                                    <div class="input-group">
                                        <input type="text" name="validity" class="form-control form-control-lg" value="{{old('validity')}}">
                                        <?php echo Form::select('validity_type',$validity_types,old('validity_type'),array('class'=>'form-control form-control-lg')); ?>
                                    </div>
                                    @if ($errors->has('validity'))
                                        <span class="text-danger"> {{ $errors->first('validity') }} </span>
                                    @endif
                                    @if ($errors->has('validity_type'))
                                        <span class="text-danger"> {{ $errors->first('validity_type') }} </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text pt-4 w-100">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="visible" name="visible" type="checkbox" value="1" <?php echo (old('visible')=='0') ? '' : 'checked'; ?>>
                                            <label class="form-check-label" for="visible">{{__("Public")}}</label>
                                        </div>
                                    </span>
                                </div>
                                @if ($errors->has('visible'))
                                    <span class="text-danger"> {{ $errors->first('visible') }} </span>
                                @endif
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text pt-4 w-100">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="highlight" value="1" name="highlight" type="checkbox" <?php if(old('highlight')=='1') echo 'checked'; ?>>
                                            <label class="form-check-label" for="highlight">{{__("Highlight")}}</label>
                                        </div>
                                    </span>
                                </div>
                                @if ($errors->has('highlight'))
                                    <span class="text-danger"> {{ $errors->first('highlight') }} </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="row <?php echo $action_type!='team' ? 'mt-5' : 'mt-2';?>">
                            <div class="form-group">
                                <?php $mandatory_modules = array(1); $SL=0; $access_sl=0;?>
                                <div class="table-responsive ps-1">
                                    <table class="table table-borderless table-sm w-100">
                                        @foreach($modules as $module)
                                            <?php if(!$is_admin && is_array($user_module_ids) && !in_array($module->id,$user_module_ids) && $module->id!=14) continue; ?>
                                            <?php $SL++;?>
                                            <tr class="border-0">
                                                <td class='text-left <?php if($action_type!='team') echo 'w-min-150px';?> h-60px p-0'>
                                                    <div class="form-check form-switch pt-2">
                                                        <input  name="modules[]" id="box{{$SL}}" class="modules form-check-input" <?php if(in_array($module->id, $mandatory_modules)) echo 'checked onclick="return false"';?>  type="checkbox" value="{{$module->id}}"/>
                                                        <label class="form-check-label" for="box{{$SL}}">{{__($module->module_name)}}</label>
                                                    </div>
                                                </td>

                                                <?php
                                                if($module->limit_enabled=='0')
                                                {
                                                    $disabled=" readonly";
                                                    $limit= __("Inapplicable");
                                                    $styleClass=' bg-light-alt';
                                                    $input_group_class = 'd-none';
                                                }
                                                else
                                                {
                                                    $disabled="";
                                                    $limit = $module->extra_text=='' ? __('Fixed') : __('Monthly');
                                                    $styleClass='';
                                                    $input_group_class = '';
                                                }

                                                $min_limit = $default_limit = 0;
                                                $max_limit = "";
                                                if($module->id==$module_id_team_member){
                                                    $min_limit = $is_admin ? 0 : 1;
                                                    $max_limit = $is_admin ? "" : 10;
                                                    $default_limit = $is_admin ? 5 : 1;
                                                }
                                                ?>
                                                <td class='text-center w-min-250px {{$action_type=="team" ? "d-none" : ""}}'>
                                                    <div class="input-group {{$input_group_class}}">
                                                        <span class="input-group-text">{{__('Limit')}}</span>
                                                         <input type='number' {{$disabled}} class='form-control form-control-lg {{$styleClass}}' value='{{$default_limit}}' min='{{$min_limit}}' max="{{$max_limit}}" name='monthly_{{$module->id}}'>
                                                        <span class="input-group-text">{{$limit}}</span>
                                                    </div>
                                                </td>
                                                <?php
                                                if($module->bulk_limit_enabled=="0")
                                                {
                                                    $disabled=" readonly";
                                                    $limit= __("None");
                                                    $styleClass='bg-light-alt';
                                                    $input_group_class = 'd-none';
                                                }
                                                else
                                                {
                                                    $disabled="";
                                                    $limit=__("Calls");
                                                    $styleClass='';
                                                    $input_group_class = '';
                                                } ?>

                                                <td class='text-center w-min-250px {{$action_type=="team" ? "d-none" : ""}}'>
                                                    <div class="input-group {{$input_group_class}}">
                                                        <span class="input-group-text">{{__('Bulk Limit')}}</span>
                                                         <input type='number' {{$disabled}} class='form-control form-control-lg {{$styleClass}}' value='0' min='0' name='bulk_{{$module->id}}'>
                                                    </div>
                                                </td>

                                                <td class='text-center {{$action_type!="team" || $module->id==$ai_livechat ? "d-none" : ""}}'>                                                                    
                                                    @foreach($access_array as $access_key=>$access_value)
                                                        <?php
                                                            $access_sl++;
                                                            $checked = in_array($module->id,$mandatory_modules) && in_array($access_key,[1,2]) ? 'checked' : '';
                                                        ?>
                                                        <input  name="team_access[{{$module->id}}][]" id="team_access{{$access_sl}}" class="team_access module_access{{$module->id}} form-check-input" type="checkbox" value="{{$access_key}}" {{$checked}}/>
                                                        <label class="form-check-label me-3 ms-0" for="team_access{{$access_sl}}">{{$access_value}}</label>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                @if ($errors->has('modules'))
                                    <span class="text-danger"> {{ $errors->first('modules') }} </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <div class="btn-wrapper mt-4">
                                        <button type="submit" class="btn btn-sm rounded btn-success text-white"><i class="fas fa-check-circle"></i>{{__('Save Changes')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 col-md-5  <?php if($action_type=='team') echo 'd-none';?>">

                <div class="card card-rounded mb-4 p-lg-2">
                    <div class="card-body card-rounded">
                        <h4 class="card-title  card-title-dash mb-4">{{$integration_access ? __('Discount & Integration') : __('Discount')}}</h4>
                        <div class="row">
                            <div class="<?php echo !$integration_access ? 'col-12' : 'col-8';?>">
                                <div class="tab-content p-0 border-0" id="v-pills-tabContent">

                                    <div class="tab-pane fade active show" id="discount-block" role="tabpanel" aria-labelledby="">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="name"> {{ __("Discount") }} %</label>
                                                    <div class="input-group">
                                                        <input type="number" name="discount_percent" class="form-control form-control-lg" value="{{old('discount_percent')}}">
                                                        <span class="input-group-text">%</span>
                                                        @if ($errors->has('discount_percent'))
                                                            <span class="text-danger"> {{ $errors->first('discount_percent') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="name"> {{ __("Discount Terms") }} </label>
                                                    <div class="input-group">
                                                        <input type="text" name="discount_terms" class="form-control form-control-lg" value="{{old('discount_terms')}}">
                                                        @if ($errors->has('discount_terms'))
                                                            <span class="text-danger"> {{ $errors->first('discount_terms') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="name"> {{ __("Discount Start") }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                                        <input type="text" name="discount_start_date" class="form-control form-control-lg datetimepicker" value="{{old('discount_start_date')}}">
                                                        @if ($errors->has('discount_start_date'))
                                                            <span class="text-danger"> {{ $errors->first('discount_start_date') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="name"> {{ __("Discount End") }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                                        <input type="text" name="discount_end_date" class="form-control form-control-lg datetimepicker" value="{{old('discount_end_date')}}">
                                                        @if ($errors->has('discount_end_date'))
                                                            <span class="text-danger"> {{ $errors->first('discount_end_date') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Discount Timezone') }}</label>
                                                    @php
                                                        $select_timezone = config('app.userTimezone');
                                                        $selected = old('timezone', $select_timezone);
                                                        if($selected=='UTC') $selected='Europe/Dublin';
                                                        $timezone_list = get_timezone_list();
                                                        echo Form::select('discount_timezone',$timezone_list,$selected,array('class'=>'form-control form-control-lg select2'));
                                                    @endphp
                                                    @if ($errors->has('discount_timezone'))
                                                        <span class="text-danger">
                                                        {{ $errors->first('discount_timezone') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="discount_status" >{{ __('Discount Status') }}</label>
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-text pt-4 w-100 bg-white">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" id="discount_status" name="discount_status" type="checkbox" value="1" <?php echo old('discount_status')=='1' ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="discount_status">{{__("Active")}}</label>
                                                                </div>
                                                            </span>
                                                        </div>
                                                        @if ($errors->has('discount_status'))
                                                            <span class="text-danger"> {{ $errors->first('discount_status') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="discount_apply_all" >{{ __('Apply to all Packages') }}</label>
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-text pt-4 w-100 bg-white">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" id="discount_apply_all" name="discount_apply_all" type="checkbox" value="1" <?php echo old('discount_apply_all')=='1' ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="discount_apply_all">{{__("Active")}}</label>
                                                                </div>
                                                            </span>
                                                        </div>
                                                        @if ($errors->has('discount_apply_all'))
                                                            <span class="text-danger"> {{ $errors->first('discount_apply_all') }} </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="paypal-block" role="tabpanel" aria-labelledby="">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="name"> {{ __("Paypal Plan ID") }}</label>
                                                    <input type="text" name="paypal_plan_id" class="form-control form-control-lg" value="{{old('paypal_plan_id')}}">
                                                    @if ($errors->has('paypal_plan_id'))
                                                        <span class="text-danger"> {{ $errors->first('paypal_plan_id') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-4 custom-nav-pills <?php echo !$integration_access ? 'd-none' : '';?>">
                                <div class="nav d-block nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#discount-block" role="tab" aria-controls="" aria-selected="true">{{__('Discount')}}</a>                                            
                                    <a class="nav-link" data-bs-toggle="pill" href="#paypal-block" role="tab" aria-controls="" aria-selected="true">Paypal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>     
</div>

@endsection

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/subscription/package.create-package.js') }}"></script>
@endpush
