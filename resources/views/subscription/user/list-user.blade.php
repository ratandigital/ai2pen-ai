<?php
$lang_display = $has_team_access ? __('Users & Team Members') : __('Teams');
$title_display = $is_admin ? $lang_display : __('Team Members');
?>
@extends('layouts.auth')
@section('title',$title_display)
@section('page-header-title',$title_display)
@section('page-header-details',__('All your user in one place'))
@section('content')
<div class="content-wrapper">
    @if (session('save_user_limit_error')=='1')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Limit Exceeded')}}</h4>
            <p> {{ __('User creation limit exceeded. You cannot create more user.') }}</p>
        </div>
    @endif

    @if (session('save_team_limit_error')=='1')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Limit Exceeded')}}</h4>
            <p> {{ __('Team member creation limit exceeded. You cannot create more team member.') }}</p>
        </div>
    @endif

    @if (session('save_user_status')=='1')
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Successful')}}</h4>
            <p> {{ __('User has been saved successfully.') }}</p>
        </div>
    @elseif (session('save_user_status')=='0')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Failed')}}</h4>
            <p>
                {{ __('Something went wrong. Failed to save user.') }}&nbsp;{{session('save_user_status_error')}}
            </p>
        </div>
    @endif

    <div class="card card-rounded mb-4 p-lg-2">
        <div class="card-body card-rounded">
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h4 class="card-title card-title-dash">{{$lang_display}}</h4>
                    <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your users")}}</p>
                </div>
                <div class="btn-wrapper mb-2">
                    @if($has_team_access && $is_admin)
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown d-inline">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-otline-dark btn-sm makeDefault  dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-plus-circle"></i> {{__('Create')}}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <li><a class="dropdown-item" href="{{route('create-user')}}">{{__('New User')}}</a></li>
                                    <li class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{route('create-user')}}?type=team">{{__('New Team Member')}}</a></li>
                                </ul>
                            </div>
                        </div>
                        @elseif($has_team_access) <a href="{{route('create-user')}}?type=team" class="btn btn-otline-dark btn-sm mb-0 me-0 makeDefault"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                        @else <a href="{{route('create-user')}}" class="btn btn-otline-dark btn-sm mb-0 me-0 makeDefault"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-group mb-3" id="searchbox">
                        <div class="input-group-prepend">
                            <?php echo Form::select('search_package_id',$packages,'',['class' => 'form-control form-control-lg select2','id'=>'search_package_id','autocomplete'=>'off']);?>
                        </div>
                        <?php $four_block=false;?>
                        @if($is_admin)
                        <?php $four_block=true;?>
                            <div class="input-group-prepend">
                                <select class="form-control form-control-lg select2" id="search_user_type">
                                    <option value="">{{__("Any User Type")}}</option>
                                    <option value="Member">{{__("Member")}}</option>
                                    @if($has_team_access)<option value="Manager">{{__("Team")}}</option>@endif
                                </select>
                            </div>
                        @endif
                        <div class="input-group-prepend">
                            <input type="text" class="form-control form-control-lg no-radius" autofocus id="search_value" name="search_value" placeholder="{{__("Search...")}}">
                        </div>
                        <div class="input-group-prepend">
                           <a class="btn btn-outline-dark btn-sm send_email_ui float-end" href="#"><i class="far fa-paper-plane"></i> {{__('Email')}}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class='table table-sm table-select' id="mytable" >
                    <thead>
                     <tr class="table-light">
                            <th>#</th>
                            <th>
                                <div class="form-check form-switch d-flex justify-content-center pt-2"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                            </th>
                            <th>{{__("Avatar") }}</th>
                            <th>{{__("Name") }}</th>
                            <th>{{__("Email") }}</th>
                            <th>{{__("Package/Role") }}</th>
                            <th>{{__("Status") }}</th>
                            <th>{{__("Role") }}</th>
                            <th>{{__("Actions") }}</th>
                            <th>{{__("Expiry date") }}</th>
                            <th>{{__("Created") }}</th>
                            <th>{{__("Last login") }}</th>
                            <th>{{__("Last IP") }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_send_sms_email" tabindex="-1" aria-labelledby="modal_send_sms_email_label"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{{ __("Send Email") }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <div id="show_message" class="text-center"></div>

                <div class="form-group">
                    <label for="subject">{{ __("Subject") }} *</label><br/>
                    <input type="text" id="subject" class="form-control form-control-lg"/>
                    <div class="invalid-feedback">{{ __("Subject is required") }}</div>
                </div>

                <div class="form-group">
                    <label for="message">{{ __("Message") }} *</label><br/>
                    <textarea name="message" class="h-min-200px form-control form-control-lg" id="message"></textarea>
                    <div class="invalid-feedback">{{ __("Message is required") }}</div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="send_sms_email" class="btn btn-success btn-sm float-start" > <i class="fas fa-paper-plane"></i>  {{ __("Send") }}</button>
            </div>
        </div>
    </div>
</div>
@include('subscription.user.list-user-css')
@endsection



@push('scripts-footer')
<script src="{{ asset('assets/js/pages/subscription/user.list-user.js') }}"></script>
@endpush


