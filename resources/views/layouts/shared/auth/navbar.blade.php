<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
    <div>
      <a class="navbar-brand brand-logo" href="{{url('/')}}">
        <img src="{{config('app.logo')}}" alt="logo" />
      </a>
      <a class="navbar-brand brand-logo-mini" href="{{url('/')}}">
        <img src="{{config('app.favicon')}}" alt="logo" />
      </a>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-top  <?php if(in_array($route_name,$full_width_page_routes)) echo ' ps-5 ';?>">
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h1 class="welcome-text">@yield('page-header-title')</h1>
        <h3 class="welcome-sub-text">@yield('page-header-details')</h3>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown d-none">
        <a class="nav-link dropdown-bordered dropdown-toggle dropdown-toggle-split" id="messageDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">English </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="messageDropdown">
          <a class="dropdown-item py-3" >
            <p class="mb-0 font-weight-medium float-left">{{__('Select Language')}}</p>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item">
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">English </p>
              <p class="fw-light small-text mb-0"></p>
            </div>
          </a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <?php $count_notification = isset($notifications) && count($notifications) ? count($notifications) : 0?>
        <a class="nav-link <?php if($count_notification>0) echo 'count-indicator';?>" id="notificationDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="icon-bell"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list h-max-500px overflow-y pb-0" aria-labelledby="notificationDropdown">
          <a class="dropdown-item py-3 border-bottom">
            <p class="mb-0 font-weight-medium float-left">{{__('You have :count new notifications',['count'=>$count_notification])}} </p>
          </a>
          @if(isset($notifications) && count($notifications)>0)
            @foreach($notifications as $row)
              <?php $not_link = $row->linkable=='1' && $row->custom_link!='' ? $row->custom_link : '';?>
              <a class="dropdown-item preview-item py-3 notification-mark-see" data-id="{{$row->id}}" href="{{ $not_link }}">
                <div class="preview-thumbnail">
                 <i class="{{ $row->icon }} {{str_replace('bg-', 'text-', $row->color_class)}}"></i>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject fw-normal text-dark mb-1">{{ $row->title }}</h6>
                  <p class="fw-light small-text mb-0">
                    <?php
                    echo $row->description;
                    if(date('Y-m-d',strtotime($row->created_at))==date('Y-m-d',strtotime(date('Y-m-d'))))
                    $converted = convert_datetime_to_timezone($row->created_at,'',false,'h:i A');
                    else $converted = convert_datetime_to_phrase($row->created_at,true,date('Y-m-d H:i:s'),false);
                    echo ' ('.$converted.')';
                    ?>
                  </p>
                </div>
              </a>
            @endforeach
          @endif
        </div>
      </li>

      <li class="nav-item dropdown user-dropdown">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="{{$profile_pic}}" alt="Profile image"> </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-md rounded-circle" src="{{$profile_pic}}" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold">{{Auth::user()->name}}</p>
            <p class="fw-light text-muted mb-0">{{Auth::user()->email}}</p>
          </div>
          <a class="dropdown-item" href="{{route('account')}}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>{{__('My Profile')}}</a>
          @if($is_member)
            <a class="dropdown-item" href="{{route('pricing-plan')}}"><i class="dropdown-item-icon mdi mdi-credit-card-check-outline text-primary me-2"></i> {{ Auth::user()->package_id==1 ?__('Upgrade to Pro') : __('Renew / Upgrade') }}</a>
          @endif

          @if(check_build_version()=='double' && !$is_manager)
            @if(has_module_access($module_id_affiliate_system,$user_module_ids,$is_admin,$is_manager) && !$is_admin)
              <a class="dropdown-item" href="{{route('affiliate-program')}}"><i class="dropdown-item-icon mdi mdi-account-switch text-primary me-2"></i> {{ __('Affiliate Program') }}</a>
            @endif

            @if($is_admin)
             <a class="dropdown-item" href="{{route('affiliate-settings')}}"><i class="dropdown-item-icon mdi mdi-account-switch text-primary me-2"></i> {{ __('Affiliate System') }}</a>
            @endif
          @endif

          <a class="dropdown-item" href="{{ route('logout') }}"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>{{__('Sign Out')}}</a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>


