@extends('layouts.auth')
@section('title',__('Settings'))
@section('page-header-title',__('AI Chat Settings'))
@section('page-header-details',__('Different Custom Prompt for different chat'))
@section('content')


<div class="content-wrapper">
    <form  class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('ai-chat-settings-action') }}">
        @csrf
        <div  id="ai-chat-api">
            <div class="row">
                {{-- <div class="col-12 col-md-4">
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
                </div> --}}

                <div class="col-12 col-md-12">
                    <div class="card card-rounded mb-4 p-lg-2">
                        <div class="card-body card-rounded">
                            <div class="d-sm-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="card-title card-title-dash">{{__('AI Chat Profile')}}</h4>
                                    <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your AI Chat profile")}}</p>
                                </div>
                                @if(has_module_action_access($module_id_settings,1,$team_access,$is_manager))
                                <div class="btn-wrapper mb-2">
                                    <a id="new-ai-chat-profile" href="#" class="btn btn-otline-dark btn-sm mb-0 me-0"><i class="mdi mdi-plus-circle"></i> {{__('New')}}</a>
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
                                        <th>{{__("Name") }}</th>
                                        <th>{{__("Custom Prompt") }}</th>
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
    </form>
</div>

<div class="modal fade" id="ai_settings_modal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('AI Chat Profile')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ai-chat-update-id" value="0">
            
                <div class="row">
                    <div class="col-12">
                        <div class="tab-content border-0" id="v-pills-tabContent">
                            <div class="tab-pane active show" id="openai-block" role="tabpanel" aria-labelledby="">
                                <form id="openai-block-form" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="">{{ __("Profile Name") }} *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                    <input name="profile_name" id="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name (store name) to identify it later')}}">
                                                </div>
                                                @if ($errors->has('profile_name'))
                                                    <span class="text-danger"> {{ $errors->first('profile_name') }} </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="custom_prompt" class="col-form-label">{{__("Custom prompt for AI Chat")}} *</label> 
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                    <input name="custom_prompt" id="custom_prompt" value="" class="form-control form-control-lg" type="text">
                                                </div>
                                                @if ($errors->has('custom_prompt'))
                                                    <span class="text-danger"> {{ $errors->first('custom_prompt') }} </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                @php $model_name= get_openai_endpoint_list();
                                                $model_name =$model_name['Chat']['chat/completions']['models'];
                                                array_unshift($model_name, __('Select Chat Model'));
                                                @endphp
                                                <label for="chat_model">{{ __("Chat Model") }} *</label>
                                                <?php  echo Form::select('chat_model',$model_name,'',['class'=>'form-control form-control-lg','id'=>'chat-model' ]);?>
                                                @if ($errors->has('chat_model'))
                                                    <span class="text-danger"> {{ $errors->first('chat_model') }} </span>
                                                @endif
                                            </div>
                                        </div>

                                        <input type="file" id="logo" not-required class="form-control d-none" name="logo" >
                                        <div class="col-3">
                                            <div class="form-group">
                                                <?php 
                                                    $logo  =  asset('assets/images/logo.png');
                                                    ?>
                                                <label for="">{{ __("Custom Prompt Image") }} <small><small>{180x50}</small></small></label>
                                                <div class="position-relative img-edit">
                                                    <img src="{{$logo}}" id="profile_img" class="rounded border cursor-pointer w-100" height="80px" onclick="document.getElementById('logo').click()">
                                                    <a href="#" class="position-absolute top-0 end-0">
                                                        <i class="fa-solid fa-square-pen text-muted" onclick="document.getElementById('logo').click()"></i>
                                                    </a>
                                                    @if ($errors->has('logo'))
                                                        <span class="text-danger"> {{ $errors->first('logo') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>                                       

                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="col-12 d-none">
                        <div class="nav d-block nav-pills thirdparty-api-block" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="d-block nav-link active" data-bs-toggle="pill" href="#openai-block" id="openai-block-link" role="tab" aria-controls="" aria-selected="true">{{__('openai')}}</a>
                        </div>
                    </div>
                </div>
            

            </div>

            <div class="modal-footer d-block">
                <button type="button" class="btn btn-primary float-start btn-sm" id="save_ai_chat_api_settings"><i class="fas fa-save"></i> {{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>



@endsection


@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/member/settings.ai-chat-settings.js') }}"></script>
@endpush