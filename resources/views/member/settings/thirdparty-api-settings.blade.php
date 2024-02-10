<div class="modal fade" id="thirdparty_api_settings_modal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('AI Profile')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="thirdparty-update-id" value="0">
            
                <div class="row">
                    <div class="col-12">
                        <div class="tab-content border-0" id="v-pills-tabContent">
                            <div class="tab-pane active show" id="openai-block" role="tabpanel" aria-labelledby="">
                                <form id="openai-block-form">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="">{{ __("Profile Name") }} *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-circle"></i></span>
                                                    <input name="profile_name" value=""  class="form-control form-control-lg" type="text" placeholder="{{__('Any name (store name) to identify it later')}}">
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
                                                    <input name="api-key" value=""  class="form-control form-control-lg" type="text">
                                                </div>
                                                @if ($errors->has('api-key'))
                                                    <span class="text-danger"> {{ $errors->first('api-key') }} </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="system_prompt" class="col-form-label">{{__("Default custom prompt for AI Chat")}}</label> 
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                    <input name="system_prompt" id="system_prompt" value="" class="form-control form-control-lg" type="text">
                                                </div>
                                                @if ($errors->has('system_prompt'))
                                                    <span class="text-danger"> {{ $errors->first('system_prompt') }} </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                @php $model_name= get_openai_endpoint_list(); 
                                                    $model_name =$model_name['Chat']['chat/completions']['models'];
                                                    array_unshift($model_name, __('Select Chat Model'));
                                                @endphp
                                                <label for="chat_model">{{ __("Default Chat Model for AI Chat") }} *</label>
                                                <?php  echo Form::select('chat_model',$model_name,'',['class'=>'form-control form-control-lg','id'=>'chat-model' ]);?>
                                                @if ($errors->has('chat_model'))
                                                    <span class="text-danger"> {{ $errors->first('chat_model') }} </span>
                                                @endif
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
                <button type="button" class="btn btn-primary float-start btn-sm" id="save_thirdparty_api_settings"><i class="fas fa-save"></i> {{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
