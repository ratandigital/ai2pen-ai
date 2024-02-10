@extends('layouts.auth')
@section('title',__('Dashboard'))
@section('page-header-title',__('Welcome back').', '.Auth()->user()->name)
@section('page-header-details',__('Performance & activity summary at a glance'))
@section('content')
    <span class="d-none">
        {{__('Text Generation')}}
        {{__('Image Generation')}}
        {{__('Speech to Text')}}
        {{__('Template Manager')}}
        {{__('Settings')}}
        {{__('Subscribers')}}
        {{__('Team Member')}}
        {{__('Ai LiveChat')}}
        {{__('Affiliate System')}}
    </span>
    <div class="content-wrapper">
      <div class="row">
        <div class="col-sm-12">
            @auth
                @if(!auth()->user()->email_verified_at && !$is_manager)
                    <div class="alert alert-warning alert-dismissible fade show p-4"  role="alert">
                        <h5 class="alert-heading mb-1 pt-2 text-dark">
                            <i class="far fa-envelope-open fs-1 float-start mt-1 me-3"></i>
                            {{__('Verify Email')}} : <small>{{__('Email is not verified yet. Please verify your email.')}}</small>
                        </h5>
                        <p class="ps-sm-5 ms-sm-1 ms-lg-2"><span class="fw-bold text-dark">{{ __('Click the link to get started') }}</span> : <a href="{{ route('verification.notice') }}" class="text-success fw-bold text-decoration-none">{{ __('Start Email Verification') }}</a></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </p>
                    </div>
                @endif
            @endauth
          <div class="home-tab">
            <div class="tab-content tab-content-basic pt-0">
              <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                <?php
                    $token_used = $usage_stat["usage_info"][$module_id_token]['usage_count']??0;
                    $token_module_limit = $usage_stat["monthly_limit"][$module_id_token] ?? 0;
                    $token_left = $token_module_limit==0 ? '<i class="fas fa-infinity"></i>' : $token_module_limit - $token_used;
                    $token_usage = $token_used==0 || $token_module_limit==0 ? '0' : ($token_used/$token_module_limit)*100;
                    $image_used = $usage_stat["usage_info"][$module_id_image]['usage_count']??0;
                    $audio_used = $usage_stat["usage_info"][$module_id_audio]['usage_count']??0;
                ?>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="statistics-details d-flex align-items-center justify-content-between">
                      <div class="">
                        <p class="statistics-title text-center">{{__('Token Used')}}</p>
                        <h3 class="rate-percentage text-center">{{$token_used}}</h3>
                      </div>
                      <div class="">
                        <p class="statistics-title text-center">{{__('Token Left')}}</p>
                        <h3 class="rate-percentage text-center">{!! $token_left !!}</h3>
                      </div>
                      <div class="d-none d-md-block">
                        <p class="statistics-title text-center">{{__('Token Usage')}} %</p>
                        <h3 class="rate-percentage text-center">{{number_format($token_usage,2)}}%</h3>
                      </div>
                      <div class="">
                        <p class="statistics-title text-center d-none d-xl-block">{{__('Speech to text')}}</p>
                        <p class="statistics-title text-center d-xl-none">{{__('Speech')}}</p>
                        <h3 class="rate-percentage text-center">{{$audio_used}} {{__('min')}}</h3>
                      </div>
                      <div class="">
                        <p class="statistics-title text-center d-none d-xl-block">{{__('Image Generated')}}</p>
                        <p class="statistics-title text-center d-xl-none">{{__('Image')}}</p>
                        <h3 class="rate-percentage text-center">{{$image_used}}</h3>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-8 d-flex flex-column">
                    <div class="row flex-grow">
                      <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <div class="d-sm-flex justify-content-between align-items-start">
                              <div>
                               <h4 class="card-title card-title-dash">{{__('Text Token Usage')}}</h4>
                               <h5 class="card-subtitle card-subtitle-dash">{{__("Last week vs this week")}}</h5>
                              </div>
                              <div id="performance-line-legend"></div>
                            </div>
                            <div class="chartjs-wrapper mt-4">
                              <canvas id="performaneLine"></canvas>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 d-flex flex-column">
                    <div class="row flex-grow">
                      <div class="col-md-6 col-lg-12 grid-margin stretch-card mb-0">
                        <div class="card bg-primary rounded-0 rounded-top">
                          <div class="card-body pb-0">
                            <h4 class="card-title card-title-dash text-white mb-4">{{__('This week')}}</h4>
                            <div class="row">
                              <div class="col-sm-4">
                                <p class="status-summary-ight-white mb-1">{{__('Token Used')}}</p>
                                <h2 class="text-info">{{$this_week_token_usage_count}}</h2>
                              </div>
                              <div class="col-sm-8">
                                <div class="status-summary-chart-wrapper pb-4">
                                  <canvas id="status-summary"></canvas>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                        <div class="card card-rounded rounded-0 rounded-bottom">
                          <div class="card-body">
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="d-flex justify-content-between align-items-center mb-2 mb-sm-0">
                                  <div>
                                    <p class="text-small mb-2">{{__('Image generated')}}</p>
                                    <h4 class="mb-0 fw-bold">{{$this_week_token_image_usage_count}}</h4>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <p class="text-small mb-2">{{__('Speech to text')}}</p>
                                    <h4 class="mb-0 fw-bold">{{$this_week_token_audio_usage_count}} {{__('min')}}</h4>
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

                @if($is_member && Auth::user()->package_id==1)
                  <div class="row flex-grow">
                      <div class="col-12 grid-margin stretch-card">
                          <div class="card card-rounded table-darkBGImg">
                              <div class="card-body">
                                  <div class="col-sm-8">
                                      <h5 class="text-white upgrade-info mb-4">
                                          {{__('Upgrade to Pro to get full access')}}
                                      </h5>
                                      <a href="{{route('pricing-plan')}}" class="btn btn-sm btn-warning text-dark border-0 m-0">{{__('Upgrade Account')}}</a>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{__("Tools Library")}}</h4>
                                <p class="card-description"></p>
                                <div class="row">
                                    <div class="form-group m-0">
                                        <input type="text" class="fs-6 form-control form-control-xl border-2" autofocus onkeyup="search_library_item(this,'library-item')" placeholder="{{__('Just type what you are looking for')}}...">
                                    </div>
                                    <div class="col-md-12 mx-auto">
                                        <ul class="nav nav-pills nav-pills-custom" id="pills-tab-custom" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link px-3 fw-bold active" id="pills-home-tab-custom" data-bs-toggle="pill" href="#pills-library-all" role="tab" aria-controls="pills-library-all" aria-selected="true">
                                                    <i class="fas fa-check-circle"></i> {{__('All Templates')}}
                                                </a>
                                            </li>
                                            <?php $tab_number = 0; ?>
                                            @foreach($ai_sidebar_group_by_id as $menu_group_id=>$menu_items)
                                            <?php
                                                if(empty($menu_items)) continue;
                                                $tab_number++;
                                                $group_name = $template_list[$menu_group_id] ?? '';
                                                $group_icon =  $template_group_icon_list[$menu_group_id] ?? '';
                                                $group_icon = str_replace(['text-info','text-primary','text-success','text-warning','text-danger','text-dark','text-light'],'',$group_icon);
                                                $tab_library_id = "tab-library-".$tab_number;
                                                $has_access = $menu_items[0]['has_access'] ?? true;
                                                if(!$has_access) $group_icon = 'fa-solid fa-award text-danger';
                                                $upgrade_text = !$has_access ? '<span class="badge badge-opacity-danger p-1 ms-1"><small>'.__("Pro").'</small></span>' : '';
                                            ?>
                                            <li class="nav-item">
                                                <a class="nav-link px-3 fw-bold" id="pills-home-tab-custom" data-bs-toggle="pill" href="#pills-{{$tab_library_id}}" role="tab" aria-controls="pills-{{$tab_library_id}}" aria-selected="true">
                                                   <i class="{{$group_icon}}"></i> {{$group_name}} {!! $upgrade_text !!}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="tab-content tab-content-custom-pill" id="pills-tabContent-custom">
                            <?php
                                $tab_number = 0;
                                $all_templates = [];
                            ?>
                            @foreach($ai_sidebar_group_by_id as $menu_group_id=>$menu_items)
                                <?php
                                if(empty($menu_items)) continue;
                                $tab_number++;
                                $tab_library_id = "tab-library-".$tab_number;
                                ?>
                                <div class="tab-pane fade" id="pills-{{$tab_library_id}}" role="tabpanel" aria-labelledby="pills-home-tab-custom">
                                    <div class="row">
                                        @foreach($menu_items as $menu_item)
                                        <?php
                                            array_push($all_templates,$menu_item);
                                            $action_url = route('tools',['template_slug'=>$menu_item['template_slug'],'group_slug'=>$menu_item['group_slug']]);
                                            $has_access = $menu_items[0]['has_access'] ?? true;
                                            if(!$has_access) $menu_item['template_thumb'] = 'fa-solid fa-award text-danger';
                                            $upgrade_text = !$has_access ? '<span class="badge badge-opacity-danger p-1 ms-1"><small>'.__("Pro").'</small></span>' : '';
                                        ?>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 grid-margin library-item">
                                            <div class="card d-flex align-items-start cursor-pointer" onclick="return window.location.href='<?php echo $action_url?>'">
                                                <div class="card-body">
                                                    <div class="d-flex flex-row align-items-start">
                                                        <i class="{{$menu_item['template_thumb']}} icon-lg"></i>
                                                        <div class="ms-3">
                                                            <h6 class="text-twitter mb-0">{{__($menu_item['template_name'])}} {!! $upgrade_text !!}</h6>
                                                            {{-- <p class="mt-2 text-dark card-text">{{__($menu_item['about_text'])}}</p> --}}
                                                            <p class="text-muted mt-2">{{__($menu_item['template_description'])}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="tab-pane fade show active" id="pills-library-all" role="tabpanel" aria-labelledby="pills-home-tab-custom">
                                <div class="row">
                                    @foreach($all_templates as $menu_item)
                                        <?php
                                            $action_url = route('tools',['template_slug'=>$menu_item['template_slug'],'group_slug'=>$menu_item['group_slug']]);
                                            $has_access = $menu_item['has_access'] ?? true;
                                            if(!$has_access) $menu_item['template_thumb'] = 'fa-solid fa-award text-danger';
                                            $upgrade_text = !$has_access ? '<span class="badge badge-opacity-danger p-1 ms-1"><small>'.__("Pro").'</small></span>' : '';
                                        ?>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 grid-margin library-item">
                                            <div class="card d-flex align-items-start cursor-pointer" onclick="return window.location.href='<?php echo $action_url?>'">
                                                <div class="card-body">
                                                    <div class="d-flex flex-row align-items-start">
                                                        <i class="{{$menu_item['template_thumb']}} icon-lg"></i>
                                                        <div class="ms-3">
                                                            <h6 class="text-twitter mb-0">{{__($menu_item['template_name'])}}</h6>
                                                            {{-- <p class="mt-2 text-dark card-text">{{__($menu_item['about_text'])}}  {!! $upgrade_text !!}</p> --}}
                                                            <p class="text-muted mt-2">{{__($menu_item['template_description'])}}</p>
                                                        </div>
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

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection


@push('scripts-footer')
    @include('include.dashboard-js')
    <script src="{{asset('assets/js/pages/dashboard.js')}}"></script>
@endpush
