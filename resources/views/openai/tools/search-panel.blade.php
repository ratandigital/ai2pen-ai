@extends('layouts.auth')
@section('title',$template_data->template_name)
@section('page-header-title',$template_group_data->group_name)
{{-- @section('page-header-details',"$template_data->about_text") --}}
@section('content')
    <div class="content-wrapper pt-1">
        <div class="row d-none d-xl-flex">

            @if(config('app.is_demo')=='1' && config('app.is_restricted')=='1' && $is_admin)
                <div class="alert alert-warning alert-dismissible fade show p-4"  role="alert">
                    <h5 class="alert-heading mb-1 pt-2 text-dark">
                        Demo Restrictions : <small class="lh-lg">Due to high traffic, content generation has been temporarily disabled for this demo admin account. However, you can still access previously generated content through the History menu. Alternatively, you may sign up as a member user to generate your desired content and explore the features of your own account without any limitations.</small>
                    </h5>
                    <p class=""><<a href="{{ route('tools-search-history') }}" class="text-success fw-bold text-decoration-none">Check previously generated content</a></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </p>
                </div>
            @endif

            @if(isset($ai_sidebar_group_by_id[$template_group_data->id]) && count($ai_sidebar_group_by_id[$template_group_data->id])>1)

                <?php
                $template_thumb = $template_data->template_thumb;
                $template_thumb = str_replace(['text-info','text-primary','text-success','text-warning','text-danger','text-dark','text-light'],'',$template_thumb);
                $action_url = route('tools',['template_slug'=>$template_data->template_slug,'group_slug'=>$template_group_data->group_slug])
                ?>
                <div class="col-xl-3 ps-xl-0 mb-3 grid-margin stretch-card cursor-pointer" onclick="return window.location.href='<?php echo $action_url?>'">
                    <div class="card bg-primary text-white border">
                        <div class="card-body p-0">
                            <div class="d-sm-flex flex-row flex-wrap text-start align-items-center">
                                <div class="py-2 text-center w-80px"><i class="icon-xl text-white {{$template_thumb}}"></i></div>
                                <div class="ms-sm-2 ms-md-0 ms-xl-2 mt-2 mt-sm-0 mt-md-2 mt-xl-0">
                                    <h6 class="mb-1">{{$template_data->template_name??''}}</h6>
                                    <p class="mb-0 text-secondary">{{$template_group_data->group_name??''}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $related_count = 1;?>
                @foreach($ai_sidebar_group_by_id[$template_group_data->id] as $related_item)
                    <?php
                        if($related_item['id']==$template_data->id) continue;
                        $template_thumb = $related_item['template_thumb'];
                        $action_url = route('tools',['template_slug'=>$related_item['template_slug'],'group_slug'=>$related_item['group_slug']])
                    ?>
                    <div class="col-xl-3 <?php if($related_count%3==0) echo ' pe-xl-0 ';?> ps-xl-0 mb-3 grid-margin stretch-card cursor-pointer" onclick="return window.location.href='<?php echo $action_url?>'">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="d-sm-flex flex-row flex-wrap text-start align-items-center">
                                    <div class="py-2 text-center w-80px"><i class="icon-xl {{$template_thumb}}"></i></div>
                                    <div class="ms-sm-2 ms-md-0 ms-xl-2 mt-2 mt-sm-0 mt-md-2 mt-xl-0">
                                        <h6 class="mb-1">{{$related_item['template_name']??''}}</h6>
                                        <p class="mb-0 text-muted">{{$related_item['group_name']??''}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                $related_count++;
                ?>
                @endforeach
            @endif
        </div>
        <div class="email-wrapper wrapper">
          <div class="row align-items-stretch">
            <div class="col-lg-4 pt-4 pt-md-2 pb-4 bg-white">
                <div class="menu-bar p-0 p-xl-2">
                    <form method="POST" action="" class="mt-2" id="generate-form">
                        @csrf
                        <input type="hidden" id="hidden-id" value="{{$search_data->id??''}}">
                        <input type="hidden" id="ai-template-id" value="{{$template_data->id??''}}">
                        <input type="hidden" id="group-slug" value="{{htmlspecialchars($template_group_data->group_slug??'')}}">
                        <input type="hidden" id="template-slug" value="{{htmlspecialchars($template_data->template_slug??'')}}">
                        <div class="col-12">
                            <div class="form-group">
                                <label><?php echo __('Document Title'); ?></label>
                                <input type="text" name="document_name" autofocus placeholder="Untitled Document" id="document_name" value="{{$search_data->document_name??''}}" class="form-control form-control-lg">
                                <span id="document_name_err" class="text-danger"></span>
                            </div>
                        </div>
                        @if($template_data->api_type=='edits')
                            <div class="col-12">
                                <div class="form-group repeater">
                                    <label>{{__('Input Text')}} *</label>
                                    <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                        <textarea name="input_text" id="input_text" required class="form-control form-control-lg">{{$search_data->input_text??''}}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <?php
                        $prompt_fields = json_decode($template_data->prompt_fields??'[]');
                        $prompt_fields_data = json_decode($search_data->prompt_fields_data??'[]',true);
                        $paramType_drop_down_values = json_decode($template_data->paramType_drop_down_values??'[]',true);
                        {{-- dd($paramType_drop_down_values); --}}
                        ?>
                        @if(!empty($prompt_fields))
                        @foreach($prompt_fields as $prompt_key=>$prompt_val)
                            <div class="col-12">
                                <div class="form-group repeater">
                                    <label>{{ucwords($prompt_key)}} *</label>
                                    <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                        <input type="hidden" value="{{$prompt_key}}" name="paramName[]" class="form-control form-control-lg paramName">
                                        @if($prompt_val=='textarea')
                                            <textarea name="paramValue[]" required class="form-control form-control-lg paramValue">{{$prompt_fields_data[$prompt_key]??''}}</textarea>
                                        @elseif($prompt_val == 'dropdown')
                                            <select name="paramValue[]" required class="form-control form-control-lg paramValue" >
                                                <option class="form-group" value="">{{ __('Select ').'  '.$prompt_key }}</option>
                                                @foreach ( $paramType_drop_down_values[$prompt_key] as $dropdown_value)
                                                        <option class="form-group" @if(isset($prompt_fields_data[$prompt_key]) && $prompt_fields_data[$prompt_key]== $dropdown_value ) selected @endif value="{{ $dropdown_value }}">{{ $dropdown_value }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="{{$prompt_val}}" value="{{$prompt_fields_data[$prompt_key]??''}}" required name="paramValue[]" class="form-control form-control-lg paramValue">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @endif
                        @if(($template_data->api_group=='image' && $template_data->api_type!='images/generations') || $template_data->api_group=='audio')
                        <div class="col-12">
                            <div class="form-group">
                                <?php $upload_details = $template_data->api_group=='image' ? "png, <4MB, ".__('Square')  : ".mp3,.mp4,.mpeg,.mpga,.m4a,.wav,.webm"?>
                                <label><?php echo __('Upload Media'); ?>* <span class="text-small text-muted">({{$upload_details}})</span></label>
                                <input type="hidden" required id="media_url" value="">
                                <input type="hidden" id="media_duration" value='0'>
                                <div id="media-url-dropzone" class="dropzone mb-1">
                                    <div class="dz-default dz-message">
                                        <input class="form-control form-control-lg" name="media_url_dropzone" id="media-uploaded-file" type="hidden">
                                        <span><i class="fas fa-cloud-upload-alt"></i> {{ __("Upload") }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($template_data->api_group!='image' && $template_data->api_group!='audio')
                        <div class="row">
                            <div class="col-12 col-xl-6  pe-xl-1">
                                <div class="form-group">
                                    <label><?php echo __('Language'); ?> <i class="fas fa-info-circle text-dark" data-bs-toggle="tooltip" title="{{__('Specify the desired language for generating your content.')}}"></i></label>
                                    <?php
                                    $language_list = get_language_list();
                                    $language_list_named = [];
                                    foreach ($language_list as $lang) {
                                        $language_list_named[$lang] = $lang;
                                    }
                                    ?>
                                    <?php  echo Form::select('language',$language_list_named,$search_data->language??'English',['class'=>'form-control form-control-lg select2 w-100','id'=>'language']);?>
                                    <span id="language_err" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 ps-xl-1">
                                <div class="form-group">
                                    <label><?php echo __('Creativity Level'); ?> <i class="fas fa-info-circle text-dark" data-bs-toggle="tooltip" title="{{__('Creativity refers to the degree of novelty and uniqueness in the generated text. Higher levels of creativity can lead to more original and unexpected results, while lower levels may result in more predictable and conservative text.')}}"></i></label>
                                    <?php  echo Form::select('temperature',get_openai_temperature_list(),$search_data->temperature??'1',['class'=>'form-control form-control-lg select2 w-100','id'=>'temperature']);?>
                                    <span id="temperature_err" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($template_data->api_group=='text' || $template_data->api_group=='chat')
                            <div class="row">
                                <div class="col-12 col-xl-6 pe-xl-1">
                                    <div class="form-group">
                                        <label><?php echo __('Phrase Repetition Level'); ?> <i class="fas fa-info-circle text-dark" data-bs-toggle="tooltip" title="{{__('Phrase repetition level refers to the frequency of repeating phrases in the generated text. Setting a high penalty for phrase repetition can reduce the occurrence of repetitive phrases in the output, while setting a lower penalty can result in more repetitions.')}}"></i></label>
                                        <?php  echo Form::select('frequency_penalty',get_openai_uniqueness_list(),$search_data->frequency_penalty??'0',['class'=>'form-control form-control-lg select2 w-100','id'=>'frequency_penalty']);?>
                                        <span id="frequency_penalty_err" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6 ps-xl-1">
                                    <div class="form-group">
                                        <label><?php echo __('Input Presence Level'); ?> <i class="fas fa-info-circle text-dark" data-bs-toggle="tooltip" title="{{__('Input presence level refers to how much the generated text is influenced by the input text provided. A high input presence level ensures that the generated text is closely related to the input, while a lower input presence level allows for more divergence from the input text.')}}"></i></label>
                                        <?php  echo Form::select('presence_penalty',get_openai_input_repeatness_list(),$search_data->frequency_penalty??'0',['class'=>'form-control form-control-lg select2 w-100','id'=>'presence_penalty']);?>
                                        <span id="presence_penalty_err" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            @if($template_data->api_group=='image')
                                <div class="col-xl-6 pe-xl-1">
                                    <div class="form-group">
                                        <label><?php echo __('Output Size'); ?> (px)</label>
                                        <?php echo Form::select('output_size',['1024x1024'=>__("Large").' ( 1024x1024)','512x512'=>__("Medium").' (512x512)','256x256'=>__("Small").' (256x256)'],$search_data->temperature??'512x512',['class'=>'form-control form-control-lg select2 w-100','id'=>'output_size']);?>
                                        <span id="output_size_err" class="text-danger"></span>
                                    </div>
                                </div>
                            @endif
                            @if($template_data->api_group=='text' || $template_data->api_group=='chat')
                            <div class="col-12 col-xl-8 pe-xl-1">
                                <div class="form-group">
                                    <label><?php echo __('Max Tokens'); ?> <i class="fas fa-info-circle text-dark" data-bs-toggle="tooltip" title="{{__('You can think of tokens as pieces of words used for natural language processing. For English text, 1 token is approximately 4 characters or 0.75 words. As a point of reference, the collected works of Shakespeare are about 900,000 words or 1.2M tokens.')}}"></i></label>
                                    <input type="number" value="{{$search_data->max_tokens??$template_data->default_tokens}}" min="16" max="32768" name="max_tokens" id="max_tokens" class="form-control form-control-lg">
                                    <span id="max_tokens_err" class="text-danger"></span>
                                </div>
                            </div>
                            @endif
                            <?php
                                $variation_class = ($template_data->api_group=='text' ||  $template_data->api_group=='chat') ? 'col-xl-4 ps-xl-1' : 'col-xl-6 ps-xl-1';
                                if($template_data->api_group=='audio') $variation_class = '';
                            ?>
                            <div class="col-12 {{$variation_class}}">
                                <div class="form-group">
                                    <label><?php echo __('Variation'); ?> <i class="fas fa-info-circle text-dark" data-bs-toggle="tooltip" title="{{__('It determines the number of completions to generate for each submit. However, it is important to note that this can consume a significant amount of your token quota due to the high number of variations it generates. Therefore, use it carefully and make sure to have appropriate settings for max tokens and stop.')}}"></i></label>
                                    <input type="number" value="{{$search_data->variation??'1'}}" min="1" max="10" name="variation" id="variation" class="form-control form-control-lg">
                                    <span id="variation_err" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-lg btn-primary fw-bol btn-block mt-2 w-100" id="generate" type="submit"><i class="fas fa-pen-nib"></i>&nbsp;&nbsp;{{__('Generate')}}</button>
                    </form>
                </div>
            </div>
            <?php $response = isset($search_data->response) && !empty($search_data->response) ? json_decode($search_data->response) : null;?>
              <div class="mail-view col-lg-8 bg-white border-left-lg p-0">
                @if(isset($search_data) && !empty($search_data) && !isset($response->error))
                  <div class="message-body">
                    <div class="sender-details p-3 p-lg-4 pb-0 pb-lg-0">
                      <div class="details">
                        <p class="msg-subject">
                          {{$template_data->template_name}}
                        </p>
                        <p class="sender-email">
                          {{isset($search_data->searched_at) ? convert_datetime_to_timezone($search_data->searched_at,'','','jS M y H:i A') : ''}}
                        </p>
                      </div>
                    </div>

                    @if($template_data->api_group=='text' ||  $template_data->api_group=='chat')
                        <?php
                          $choices = $response->choices ?? [];
                          $choice_count = 0;
                        ?>
                        @foreach($choices as $choice)
                            <form action="{{route('tools-download-text')}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-toolbar pt-3 px-3 px-lg-4 pt-4 pb-0 d-flex justify-content-between">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm fw-bolder px-0">{{$choice_count>0 ? __('Variation').'-'.$choice_count : __('Output')}}</button>
                                            </div>
                                            <div class="btn-group">
                                                <button type="button" onclick="copyToClipboard('choice-{{$choice_count}}')" class="btn btn-sm px-0"><i class="ti-layers text-primary me-1"></i>{{__('Copy')}}</button>
                                                <input type="hidden" name="filename" value="{{$template_data->template_slug.'.'.$template_group_data->group_slug.'.search-'.$search_data->id.'.output-'.$choice_count+1}}">
                                                <button type="submit" class="btn btn-sm px-0 ps-4"><i class="ti-download text-primary me-1"></i>{{__('Download')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group px-3 px-lg-4 pb-0">
                                    @if($template_data->output_display=='code')
                                        <textarea name="download_text"  id="choice-{{$choice_count}}" onblur="auto_grow(this)" class="search-result d-none">{!! $choice->text !!}</textarea>
                                        <textarea name=""  id="editor-{{$choice_count}}" class="search-result code-editor">{!! $choice->text !!}</textarea>
                                    @else
                                        <textarea name="download_text"  id="choice-{{$choice_count}}" onblur="auto_grow(this)" class="search-result {{$template_data->output_display=='code' ? ' code-editor' : ''}}">{!! $choice->text !!}</textarea>
                                    @endif
                                </div>
                            </form>
                            <?php $choice_count++;?>
                        @endforeach

                    @elseif($template_data->api_group=='image')
                        <?php
                        $choices = $response->data ?? [];
                        $choice_count = 0;
                        ?>
                        @foreach($choices as $choice)
                            <form action="{{route('tools-download-file')}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-toolbar pt-3 px-3 px-lg-4 pt-4 pb-0 d-flex justify-content-between">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm fw-bolder px-0">{{$choice_count>0 ? __('Variation').'-'.$choice_count : __('Output')}}</button>
                                            </div>
                                            <div class="btn-group">
                                                <input type="hidden" name="file_url" value="{{$choice->url}}">
                                                <input type="hidden" name="filename" value="{{$template_data->template_slug.'.'.$template_group_data->group_slug.'.search-'.$search_data->id.'.output-'.$choice_count+1}}">
                                                <button type="submit" class="btn btn-sm px-0 ps-4"><i class="ti-download text-primary me-1"></i>{{__('Download')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(@getimagesize($choice->url))
                                <div class="mx-3 mb-5 mx-lg-4">
                                    <img src="{{$choice->url}}" alt="{{__('Resource has been moved or removed.')}}" class="border img-thumbnail img-fluid">
                                </div>
                                @else
                                <div class="mx-3 my-3 mx-lg-4">
                                    <div class="alert alert-warning">
                                        <h4 class="mt-2">{{__("The resource's validity has expired and it has been removed. You can re-generate content using same dataset.")}}</h4>
                                    </div>
                                </div>
                                @endif
                            </form>
                            <?php $choice_count++;?>
                        @endforeach

                    @elseif($template_data->api_group=='audio')
                      <?php $choice = $response->text ?? '';?>
                      <form action="{{route('tools-download-text')}}" method="post">
                          @csrf
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="btn-toolbar pt-3 px-3 px-lg-4 pt-4 pb-0 d-flex justify-content-between">
                                      <div class="btn-group">
                                          <button type="button" class="btn btn-sm fw-bolder px-0">{{__('Output')}}</button>
                                      </div>
                                      <div class="btn-group">
                                          <button type="button"  onclick="copyToClipboard('choice')" class="btn btn-sm px-0"><i class="ti-layers text-primary me-1"></i>{{__('Copy')}}</button>
                                          <input type="hidden" name="filename" value="{{$template_data->template_slug.'.'.$template_group_data->group_slug.'.search-'.$search_data->id.'.output'}}">
                                          <button type="submit" class="btn btn-sm px-0 ps-4"><i class="ti-download text-primary me-1"></i>{{__('Download')}}</button>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="form-group px-3 px-lg-4 pb-0">
                              <textarea name="download_text"  id="choice-0" onblur="auto_grow(this)" class="search-result">{!! $choice !!}</textarea>
                          </div>
                      </form>
                    @endif
                  </div>

                @elseif(isset($response->error))
                    <div class="row">
                        <div class="col-12 text-center">
                            <img src="{{asset('assets/images/content-error.jpg')}}" alt="" class="mt-5 pt-5 content-error">
                            <h4 class="my-4 pb-5 text-danger px-4">{{$response->error->message ?? __('Something went wrong.')}}</h4>
                        </div>
                    </div>
                @else
                    <div class="row d-none d-md-flex">
                        <div class="col-12 text-center">
                            <img src="{{asset('assets/images/content-pending.jpg')}}" alt="" class="mt-5 pt-5 content-pending">
                            <h4 class="my-4 pb-5 text-primary px-4">{{__('When done, your desired content will be displayed here.')}}</h4>
                        </div>
                    </div>
                @endif
            </div>
          </div>
        </div>
    </div>
@endsection

@push('styles-header')
    <link rel="stylesheet" href="{{ asset('assets/vendors/dropzone/dist/dropzone.css') }}">
@endpush
@push('scripts-footer')
    <script src="{{ asset('assets/vendors/dropzone/dist/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/openai/tools.search-panel.js') }}"></script>
@endprepend
