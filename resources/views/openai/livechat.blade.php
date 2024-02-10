@extends('layouts.full')
@section('title',__('Livechat'))
@section('page-header-title',__('Livechat'))
@section('page-header-details',__('Livechat'))
@section('content')
<div class="light-mode" id="frame">
    <div id="sidepanel" class="d-none d-sm-block">
        <div class="row">
            <div class="col">
                <div class="rounded-1" id="profile">
                    <div class="">
                        <a  href="{{route('dashboard')}}">
                            <img src="{{config('app.logo_alt')}}" id="brand_logo" alt="logo" />
                        </a>
                    </div>
                </div>
            </div>
            <div class="col" id="new_chat_id" >
                    <span  id="new_chat" >
                      <i class="fas fa-comment-dots"> </i>   {{__('New Chat')}}
                    </span>
            </div>
        </div>

        <div id="contacts" class="side_content">

            @foreach ($info as $key=>$value)
                <ul class="list-group list-group-flush" id="chat_{{ $value->id ?? '' }}">
                    <li class="contact">
                        <div class="row">                           
                            <div class="col-9" id="side_chat_content">
                                <div class="wrap">
                                    <img src="{{asset('assets/images/livechat/chat-bubble.png')}}" alt="" />
                                    <input class="edit-hidden-input" type="hidden" id="chat_id_input_{{ $value->id ?? '' }}"  value="{{ $value->id ?? '' }}">    
                                    <div class="meta">
                                        <div class="name" id="side_chat_{{ $value->id ?? '' }}">{{ ucfirst($value->conversation_start_content) ?? '' }}</div>
                                        <div class="preview">{{$ai_first_reply[$key]->content ?? ''}}</div>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-3" id="edit_btn_div" >                            
                                <span class="" id="edit_btn"><img class="img_custom" src="{{asset('assets/images/livechat/edit.png')}}"/></span>
                                <span  id="delete_btn"><img class="img_custom" src="{{asset('assets/images/livechat/delete.png')}}"/></span>                                                     
                            </div>
                        </div>
                    </li>
                </ul>
            @endforeach
            
        </div>
        <div class="d-none d-sm-block" id="bottom-bar">
            <button id="ai_traning_button" title="{{ __('Custom prompt to train AI')}}" data-bs-toggle="modal" data-bs-target="#AItraningModal"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>{{__('Settings')}}</span></button>
            <button id="dark_button" title="{{ __('Dark Mood')}}"><i class="" aria-hidden="true"><img src="{{asset('assets/images/livechat/sun.png')}}" alt="" id="dark_mode_icon"></i> <span></span></button>
        </div>
    </div>
    <div class="content">

        <div class="text-end float-right me-2">
            <div class="social-media">
                <input type="hidden" id="chat_download_id" name="chat_download_id" value="0">
                <i class="fa fa-download " id="chat_download" aria-hidden="true"></i>                   
            </div>
        </div>
        <div class="messages" id="conversation_body">
                <input type="hidden" id="conversation_id" name="conversation_id" value="0"> 
            <ul id="conversation_modal_body">

            </ul>
            <div class="jumbotron p-3 text-center pb-0" id="custom_staating_msg">
                <h1 class="display-4 mb-3">{{__('A Better UI for ChatGPT')}}</h1>
                <p class="lead">{{__('Default AI Chat Assistant')}}</p>
                <div class="row mt-5">
                    <div class="col-12 col-lg-4">
                        <div class="card rounded-3 mb-2">
                            <div class="card-body">
                              <h5 class="card-title">{{__("Remember")}}</h5>
                              <h6 class="card-subtitle mb-2 lh-base">{{__("Remembers what user said earlier in the conversation")}}</h6>
                            </div>
                          </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card rounded-3 mb-2">
                            <div class="card-body">
                              <h5 class="card-title">{{__("Follow-up")}}</h5>
                              <h6 class="card-subtitle mb-2 lh-base">{{__("Allows user to provide follow-up corrections")}}</h6>
                            </div>
                          </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card rounded-3 mb-2">
                            <div class="card-body">
                              <h5 class="card-title">{{__("Filter")}}</h5>
                              <h6 class="card-subtitle mb-2 lh-base">{{__("Trained to decline inappropriate requests")}}</h6>
                            </div>
                          </div>
                    </div>

                    @if(config('app.is_demo')=='1' && config('app.is_restricted')=='1' && $is_admin)
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible fade show p-4"  role="alert">
                            <h5 class="alert-heading mb-1 pt-2 text-dark">
                                Demo Restrictions : <small class="lh-lg">Due to high traffic, content generation has been temporarily disabled for this demo admin account. You may sign up as a member user to generate your desired content and explore the features of your own account without any limitations.</small>
                            </h5>
                            <p class=""><<a href="{{ route('tools-search-history') }}" class="text-success fw-bold text-decoration-none">Check previously generated content</a></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
                <hr>
                <p class="lead text-center pt-3 pb-4" id="More_Specialized_AI_Chat">{{__('More Specialized AI Chat')}}</p>
                <?php $scrollHeight = count($custom_prompts)>4 ? 280 : 120; ?>
                <div style="height:{{$scrollHeight}}px;overflow-y:auto;overflow-x:hidden" class="makescroll-prompt">
                <div class="row" id="">
                    <input class="custom-prompt-id" type="hidden" id="custom_prompt_id"  value="">
                    @foreach($custom_prompts as $key => $value)                    
                        <div class="col-6 col-md-3 col-lg-2 mb-4">
                            <div class="card" id="card_{{ $value->id ?? '' }}" style="background: #32465a;">
                                <div class="row">
                                    <div class="card-img">
                                        <a href="{{route('livechat.loadcustom',$value->id)}}"><img src="{{!empty($value->custom_prompt_img) ? $value->custom_prompt_img : config('app.logo')}}" style="height:80px;" class="w-100 img-fluid rounded-0"></a>
                                    </div>
                                </div>
                                {{-- <div class="row mx-auto my-auto overflow-hidden">
                                    <div class="preview text-white" style="font-size:12px" >{{$value->profile_name ?? ''}}</div>
                                </div> --}}
                                <a type="button" href="{{route('livechat.loadcustom',$value->id)}}" class="btn btn-primary btn-sm rounded-0 btn-block" id="chat_now">{{$value->profile_name ?? ''}}</a>
                                    
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            </div>
        </div>
        <div class="input-group mx-3" id="sendblock">
            <label for="" class="mb-2"><small>{{ __('Start typing your mind...')}}</small></label>
            <textarea type="text" class="form-control border-1" id="send_message" placeholder=""></textarea>  
            <div class="input-group-append cursor-pointer border-0" id="final_send_button">
                <span class="input-group-text border-1" id="final_send_span"><i class="fa fa-paper-plane"></i></span>
            </div>                                                 
        </div>
        
    </div>
</div>


<div class="modal fade" id="AItraningModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <i class="fa fa-paper-plane"></i> {{__('Custom prompt to train AI')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="system_prompt_value" name="system_prompt_value" value="">
                <label for="user input" class="col-form-label">{{__("Enter custom prompt :")}}</label>
                <textarea type="text" class="form-control" id="user_choice_system_prompt"> {{$system_prompt_config->system_prompt ?? $system_prompt_info}}</textarea>
                
                <input type="hidden" id="prompt-model" name="prompt-model" value={{$chat_model->chat_model??'gpt-3.5-turbo'}}>
                <input type="hidden" id="system_prompt_model" name="system_prompt_model" value="gpt-3.5-turbo">

                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary <?php if(config('app.is_demo')=='1' && $is_admin) echo 'disabled';?>" id="modal_send_btn">{{__("Save")}}</button>
            </div>
        </div>
    </div>
</div>

@php 
$user_id =Auth::user()->id; 
$user_pic = $profile_pic  = !empty(Auth::user()->profile_pic) ? Auth::user()->profile_pic : asset('assets/images/avatar/avatar-6.png');
$user_name =Auth::user()->name; 
$aichaticon =config('app.ai_chat_icon');
@endphp


@endsection


@push('styles-header')
<link rel="stylesheet" href="{{ asset('assets/css/livechat.css') }}">
<style>
    #delete_btn:hover .img_custom {
    content: url('{{ asset("assets/images/livechat/delete_white.png") }}');
    }
    #edit_btn:hover .img_custom {
    content: url('{{ asset("assets/images/livechat/edit_white.png") }}');
    }
</style>

@endpush 

@push('scripts-footer')
<script>
    "use strict";
    var from_user_id = {{$user_id}};
    var user_name = '{{$user_name}}';
    var lang_success = '{{ __("success") }}';
    var lang_error = '{{ __("error") }}';
    var loading_gif = '{{asset("assets/images/livechat/loading.gif")}}'
    var sun_img = '{{asset("assets/images/livechat/sun.png")}}'
    var moon_img = '{{asset("assets/images/livechat/moon.png")}}'
    var check_icon = '{{asset("assets/images/livechat/check.png")}}'
    var close_icon = '{{asset("assets/images/livechat/close.png")}}'
    var chat_icon = '{{asset("assets/images/livechat/chat-bubble.png")}}'
    var edit_icon = '{{asset("assets/images/livechat/edit.png")}}'
    var delete_icon = '{{asset("assets/images/livechat/delete.png")}}'
    var ai_pic = '{{$aichaticon}}'
    var user_pic = '{{$user_pic}}'
    var data_deleted_success = '{{"Your data has been deleted"}}';
    var livechat_conversation= '{{route('livechat_conversation')}}';
    var livechat_conversation_download= '{{route('livechat_conversation_download')}}';
    var user_choice_system_prompt= '{{route('user_choice_system_prompt')}}';
    var livechat_sidechat_edit= '{{route('livechat_sidechat_edit')}}';
    var livechat_side_conversation= '{{route('livechat_side_conversation')}}';
    var livechat_side_conversation_delete= '{{route('livechat_side_conversation_delete')}}';
    if(areWeUsingScroll){        
        $(".makescroll-prompt").niceScroll({
            cursorcolor:"#eee",
            horizrailenabled:false
        });
    }
</script>

<script src="{{ asset('assets/js/pages/openai/livechat.js') }}"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/dark.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
@endpush














