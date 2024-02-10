@extends('layouts.auth')
@section('title',__('Content History'))
@section('page-header-title',__('Content History'))
@section('page-header-details',__('All your generated contents'))
<?php
$template_list_drop_down = $template_list;
$template_list_drop_down[''] = __('Select Template');
?>
@section('content')
    <div class="content-wrapper">
        <div class="card card-rounded mb-4 p-lg-2">
            <div class="card-body card-rounded">
                <div class="d-sm-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title card-title-dash">{{__('Content History')}}</h4>
                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your generated contents")}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3" id="searchbox">
                            <div class="input-group-prepend">
                                <select class="form-control form-control-lg select2" id="search_ai_template_id">
                                    <option value="">{{__('Select Template')}}</option>
                                    @foreach($ai_sidebar_group_by_id as $menu_group_id=>$menu_items)
                                      <?php
                                      if(empty($menu_items)) continue;
                                      $group_name = $template_list[$menu_group_id] ?? '';
                                      ?>
                                      <optgroup label="{{__($group_name)}}">
                                        @foreach($menu_items as $menu_item)
                                          <option value="{{__($menu_item['id'])}}">{{__($menu_item['template_name'])}}</option>
                                        @endforeach
                                      </optgroup>
                                    @endforeach
                                </select>

                            </div>
                            <div class="input-group-prepend">
                                <input type="text" class="form-control form-control-lg" autofocus id="search_value" name="search_value" placeholder="{{__("Search...")}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class='table table-lg table-select' id="mytable" >
                        <thead>
                        <tr class="table-light">
                            <th>#</th>
                            <th>ID</th>
                            <th>{{__("Document") }}</th>
                            <th>{{__("Used") }}</th>
                            <th>{{__("Language") }}</th>
                            <th>{{__("Actions") }}</th>
                            <th>{{__("Group") }}</th>
                            <th>{{__("Type") }}</th>
                            <th>{{__("Template") }}</th>
                            <th>{{__("Model") }}</th>
                            <th>{{__("Generated at") }}</th>
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
<script src="{{ asset('assets/js/pages/openai/tools.search-history.js') }}"></script>
@endpush