<?php
  $has_team_access = has_module_access($module_id_team_member,$user_module_ids,$is_admin,$is_manager);
  $has_settings_access = has_module_access($module_id_settings,$user_module_ids,$is_admin,$is_manager);
  $has_template_manager_access = has_module_access($module_id_template_manager,$user_module_ids,$is_admin,$is_manager);
  $package_language_display = $is_admin ? __('Packages & Roles') : __('Team Roles');
  $user_language_display = $is_admin ? __('Users & Teams') : __('Team Members');
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <?php
      $thisRoute = 'dashboard';
      $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
    ?>
    <li class="nav-item {{$get_selected_sidebar_class}}">
      <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
        <i class="mdi mdi-monitor-dashboard menu-icon"></i>
        <span class="menu-title">{{__('Dashboard')}}</span>
      </a>
    </li>

    @if(!empty($ai_sidebar_group_by_id))
    <li class="nav-item nav-category">{{__('Tools')}}</li>

      @if(has_module_access($ai_livechat,$user_module_ids,$is_admin,$is_manager) )
      <?php
      $thisRoute = 'livechat.load';
      $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      <li class="nav-item {{$get_selected_sidebar_class}}">
        <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}} " target="_blank">
            <i class="mdi mdi-chat menu-icon text-primary"></i>
            <span class="menu-title">{{__('AI Chat')}}</span>
        </a>
      </li>
      @endif

      <?php $menu_number = 0;?>
      @foreach($ai_sidebar_group_by_id as $menu_group_id=>$menu_items)
      <?php
      if(empty($menu_items)) continue;
      $menu_number++;
      $group_name = $template_list[$menu_group_id] ?? '';
      $group_icon =  $template_group_icon_list[$menu_group_id] ?? '';
      $ai_sidebar_menu_group_id = "aitool-sidebar-".$menu_number;
      $has_access = $menu_items[0]['has_access'] ?? true;
      $upgrade_text = !$has_access ? '<span class="badge badge-opacity-danger p-1 ms-1"><small>'.__("Pro").'</small></span>' : '';
      if(!$has_access) $group_icon = 'fa-solid fa-award text-danger';
      ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#{{$ai_sidebar_menu_group_id}}" aria-expanded="false" aria-controls="{{$ai_sidebar_menu_group_id}}">
          <i class="{{$group_icon}} menu-icon"></i>
          <span class="menu-title">{{__($group_name)}} {!! $upgrade_text !!}</span>
        </a>
        <div class="collapse" id="{{$ai_sidebar_menu_group_id}}">
          <ul class="nav flex-column sub-menu">
            @foreach($menu_items as $menu_item)
              <li class="nav-item"> <a class="nav-link" href="{{route('tools',['group_slug'=>$menu_item['group_slug']??'','template_slug'=>$menu_item['template_slug']??''])}}">{{__($menu_item['template_name'])}}</a></li>
            @endforeach
          </ul>
        </div>
      </li>
      @endforeach

      
      <?php
        $thisRoute = 'tools-search-history';
        $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      <li class="nav-item nav-category">{{__('Content')}}</li>
      <li class="nav-item {{$get_selected_sidebar_class}}">
        <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
            <i class="mdi mdi-history menu-icon"></i>
            <span class="menu-title">{{__('History')}}</span>
        </a>
      </li>
    @endif

    @if($route_name!= 'livechat.load')

      @if($is_admin || $has_team_access)<li class="nav-item nav-category">{{$is_admin ? __('Administration') : __('Management')}}</li>@endif
      <?php
        $thisRoute = 'template-manager';
        $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      @if(($is_admin && !$is_manager) || $has_template_manager_access)
      <li class="nav-item {{$get_selected_sidebar_class}}">
        <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
          <i class="mdi mdi-table-cog menu-icon"></i>
          <span class="menu-title">{{__('Template Manager')}}</span>
        </a>
      </li>
      @endif
      <?php
        $thisRoute = 'ai-chat-settings';
        $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      @if(($is_admin && !$is_manager) || $has_template_manager_access)
      <li class="nav-item {{$get_selected_sidebar_class}}">
        <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
          <i class="mdi mdi-cog menu-icon"></i>
          <span class="menu-title">{{__('Ai Chat Settings')}}</span>
        </a>
      </li>
      @endif

      @if($has_team_access && !$is_manager)
      <?php
      $thisRoute = 'list-user';
      $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      <li class="nav-item {{$get_selected_sidebar_class}}">
          <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
              <i class="mdi mdi-account-plus-outline menu-icon"></i>
              <span class="menu-title">{{$user_language_display}}</span>
          </a>
      </li>
      <?php
        $thisRoute = 'list-package';
        $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      <li class="nav-item {{$get_selected_sidebar_class}}">
        <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
          <i class="mdi mdi-package-variant menu-icon"></i>
          <span class="menu-title">{{$package_language_display}}</span>
        </a>
      </li>
      @endif

    <?php
      $thisRoute = 'general-settings';
      $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      @if($is_admin || $has_settings_access)
          <li class="nav-item {{$get_selected_sidebar_class}}">
              <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
                  <i class="mdi mdi-cog menu-icon"></i>
                  <span class="menu-title">{{__('Settings')}}</span>
              </a>
          </li>
      @endif

      @if(!$is_manager)
      <?php
        $thisRoute = 'transaction-log';
        $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      <li class="nav-item {{$get_selected_sidebar_class}}">
        <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
          <i class="mdi mdi-currency-usd menu-icon"></i>
          <span class="menu-title">{{$is_member ? __('Transactions') : __('Earnings')}}</span>
        </a>
      </li>
      @endif

      <?php
      $thisRoute = 'update-list';
      $get_selected_sidebar_class = $get_selected_sidebar==$thisRoute ? 'active' : '';
      ?>
      @if($is_admin)
          <li class="nav-item {{$get_selected_sidebar_class}}">
              <a class="nav-link {{$get_selected_sidebar_class}}" href="{{route($thisRoute)}}">
                  <i class="mdi mdi-leaf menu-icon"></i>
                  <span class="menu-title">{{__('Check Update')}}</span>
              </a>
          </li>
      @endif
    @endif

  </ul>
</nav>
