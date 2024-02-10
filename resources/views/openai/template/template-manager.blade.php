@extends('layouts.auth')
@section('title',__('Template Manager'))
@section('page-header-title',__('Template Manager'))
@section('page-header-details',__('Manage your templates and template groups'))
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card card-rounded mb-4 p-lg-2">
                    <div class="card-body card-rounded">
                        <div class="d-sm-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title card-title-dash">{{__('Templates')}}</h4>
                                <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your templates")}}</p>
                            </div>
                            <div class="btn-wrapper mb-2">
                                @if($is_admin)
                                    <a href="" id="new_template_field" class="btn btn-otline-dark btn-sm mb-0 me-0 makeDefault"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                                @endif
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class='table table-sm table-select' id="mytable10" >
                                <thead>
                                <tr class="table-light">
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>{{__("Thumbnail") }}</th>
                                    <th>{{__("Template Name") }}</th>
                                    <th>{{__("Intro Text") }}</th>
                                    <th>{{__("Group") }}</th>
                                    <th>{{__("Status") }}</th>
                                    <th>{{ __("Actions") }}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card card-rounded mb-4 p-lg-2">
                    <div class="card-body card-rounded">
                        <div class="d-sm-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title card-title-dash">{{__('Template Groups')}}</h4>
                                <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Manage your template groups")}}</p>
                            </div>
                            <div class="btn-wrapper mb-2">
                                @if($is_admin)
                                    <a href="" id="new_group_field" class="btn btn-otline-dark btn-sm mb-0 me-0 makeDefault"><i class="fas fa-plus-circle"></i> {{__('Create')}}</a>
                                @endif
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class='table table-sm table-select' id="mytable11" >
                                <thead>
                                <tr class="table-light">
                                    <th>#</th>
                                    <th>{{__("ID") }}</th>
                                    <th>{{__("Serial") }}</th>
                                    <th>{{__("Thumbnail") }}</th>
                                    <th>{{__("Name") }}</th>
                                    <th>{{__("Status") }}</th>
                                    <th>{{ __("Actions") }}</th>
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
@include('openai.template.template-modal')
@endsection

@push('styles-header')
    <link rel="stylesheet" href="{{ asset('assets/vendors/dropzone/dist/dropzone.css') }}">
@endpush
@push('scripts-footer')
    <script src="{{ asset('assets/vendors/dropzone/dist/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.repeater/jquery.repeater.min.js')}}"></script>
    <script src="{{ asset('assets/js/pages/openai/template.template-manager.js') }}"></script>
@endprepend
