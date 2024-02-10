<?php
$lang_display = $has_team_access ? __('Packages & Roles') : __('Roles');
$title_display = $is_admin ? $lang_display : __('Team Roles');
?>
@extends('layouts.auth')
@section('title',$title_display)
@section('page-header-title',$title_display)
@section('page-header-details',__('All your user access in one place'))
@section('content')
<div class="content-wrapper">
    @if (session('save_package_status')=='1')
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Successful')}}</h4>
            <p> {{ __('Package/role has been saved successfully.') }}</p>
        </div>
    @elseif (session('save_package_status')=='0')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Failed')}}</h4>
            <p> {{ __('Something went wrong. Failed to save package/role.') }}</p>
        </div>
    @endif

    <div class="card card-rounded mb-4 p-lg-2">
        <div class="card-body card-rounded">
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h4 class="card-title card-title-dash">{{$lang_display}}</h4>
                    <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your users access")}}</p>
                </div>
                <div class="btn-wrapper mb-2">
                    @if($has_team_access && $is_admin)
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown d-inline">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-otline-dark btn-sm makeDefault dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-plus-circle"></i> {{__('Create')}}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <li><a class="dropdown-item" href="{{route('create-package')}}">{{__('New Subscription Package')}}</a></li>
                                    <li class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{route('create-package')}}?type=team">{{__('New Team Role')}}</a></li>
                                </ul>
                            </div>
                        </div>
                    @elseif($has_team_access) <a href="{{route('create-package')}}?type=team" class="btn btn-otline-dark btn-sm mb-0 me-0 makeDefault"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                    @else <a href="{{route('create-package')}}" class="btn btn-otline-dark btn-sm mb-0 me-0 makeDefault"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-group mb-3" id="searchbox">
                        <?php $two_block=false;?>
                        @if($has_team_access)
                            <?php $two_block=true;?>
                            <div class="input-group-prepend">
                                <select class="form-control form-control-lg select2" id="search_package_type">
                                    <option value="">{{__("Any Type")}}</option>
                                    <option value="subscription">{{__("Subscription")}}</option>
                                    <option value="Team">{{__("Team")}}</option>
                                </select>
                            </div>
                        @endif
                        <div class="input-group-prepend">
                            <input type="text" class="form-control form-control-lg" autofocus id="search_value" name="search_value" placeholder="{{__("Search...")}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class='table table-sm table-select' id="mytable" >
                    <thead>
                    <tr class="table-light">
                        <th>#</th>
                        <th>{{__("Package ID") }}</th>
                        <th>{{__("Package/Role") }}</th>
                        <th>{{__("Type") }}</th>
                        <th>{{__("Price") }} - <?php echo isset($payment_config->currency) ? $payment_config->currency : 'USD';?></th>
                        <th>{{__("Validity") }} - {{__("days") }}</th>
                        <th>{{__("Default") }}</th>
                        <th>{{__("Actions") }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles-header')
<link rel="stylesheet" href="{{ asset('assets/css/pages/subscription/package.list-package.css') }}">
@endpush

@push('scripts-footer')
<script src="{{ asset('assets/js/pages/subscription/package.list-package.js') }}"></script>
@endpush
