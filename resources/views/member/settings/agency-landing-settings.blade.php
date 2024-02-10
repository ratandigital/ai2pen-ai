@section('content')
@extends('layouts.auth')
@section('title',__('Settings'))
@section('page-header-title',__('Landing Page Editor'))
@section('page-header-details',__('Setup lnading page elements'))
@section('content')
<div class="content-wrapper">
    @if(session('status'))
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Saved')}}</h4>
            <p> {{ session('status') }}</p>
        </div>
    @endif
    <form class="form form-vertical" enctype="multipart/form-data" method="POST" action="{{ route('agency-landing-editor-action') }}">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card card-rounded mb-4 p-lg-2">
                    <div class="card-body card-rounded">
                        <div class="d-sm-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title card-title-dash mb-4">{{__('Landing Page')}}</h4>
                            </div>
                            <div class="btn-wrapper mb-2">
                                <a href="{{ route('agency-landing-editor-reset') }}" data-bs-toggle="tooltip" title="{{__('Reset to default configuration')}}" id="date_range_picker" class="btn btn-outline-dark border btn-sm mb-0 me-0"><i class="fas fa-cog"></i> {{__('Reset')}}</a>
                                <button type="submit" class="btn btn-sm rounded btn-success text-white"><i class="fas fa-check-circle"></i>{{__('Save Changes')}}</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="input-group mb-2">
                                    <span class="input-group-text pt-2 w-100">
                                        <div class="form-check form-switch mt-3 ms-2">
                                            <input class="form-check-input" id="disable_landing_page" name="disable_landing_page" type="checkbox" value="1" <?php echo (old('disable_landing_page',$xdata->disable_landing_page??0)=='0') ? '' : 'checked'; ?>>
                                            <label class="form-check-label" for="disable_landing_page"><h5 class="m-0 text-danger">{{__("Disable Landing Page")}}</h5></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="input-group mb-2">
                                    <span class="input-group-text pt-2 w-100">
                                        <div class="form-check form-switch mt-3 ms-2">
                                            <input class="form-check-input" id="disable_review_section" name="disable_review_section" type="checkbox" value="1" <?php echo (old('disable_review_section',$xdata->disable_review_section??0)=='0') ? '' : 'checked'; ?>>
                                            <label class="form-check-label" for="disable_review_section"><h5 class="m-0 text-primary">{{__("Disable Review Section")}}</h5></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="input-group mb-2">
                                    <span class="input-group-text pt-2 w-100">
                                        <div class="form-check form-switch mt-3 ms-2">
                                            <input class="form-check-input" id="enable_dark_mode" name="enable_dark_mode" type="checkbox" value="1" <?php echo (old('enable_dark_mode',$xdata->enable_dark_mode??0)=='0') ? '' : 'checked'; ?>>
                                            <label class="form-check-label" for="enable_dark_mode"><h5 class="m-0 text-primary">{{__("Enable Dark Mode")}}</h5></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-rounded mb-4 p-lg-2">
            <div class="card-body card-rounded">
                <div class="row">
                    <div class="col-12 col-md-3 order-1 order-md-2 custom-nav-pills">
                        <div class="nav flex-column nav-pills border-0 mt-md-4 mx-0" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" id="v-pills-company-tab" data-bs-toggle="pill" data-bs-target="#v-pills-company" type="button" role="tab" aria-controls="v-pills-company" aria-selected="false">{{ __("Company") }}</button>
                            <button class="nav-link" id="v-pills-detailedFeature-tab" data-bs-toggle="pill" data-bs-target="#v-pills-detailedFeature" type="button" role="tab" aria-controls="v-pills-detailedFeature" aria-selected="true">{{ __("Media") }}</button>
                            <button class="nav-link" id="v-pills-reviews-tab" data-bs-toggle="pill" data-bs-target="#v-pills-reviews" type="button" role="tab" aria-controls="v-pills-reviews" aria-selected="false">{{ __("Reviews") }}</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-9 order-2 order-md-1">

                        <div class="tab-content border-0 p-0" id="v-pills-tabContent">

                            <div class="tab-pane fade" id="v-pills-detailedFeature" role="tabpanel" aria-labelledby="v-pills-detailedFeature-tab">
                                <div class="row">
                                    <?php $i=0;?>
                                    @foreach($settings_data['details_features'] as $key => $form)
                                    <?php $i++;?>
                                        @foreach($form as $value)

                                            <?php  $name = $value['name'] ?? '';
                                                    $placeholder = $value['placeholder'] ?? '';
                                                    $type = $value['type'] ?? '';
                                                    $label = $value['label'] ?? '';
                                                    $upload = $value['upload'] ?? false;
                                                    if($upload) $label = $label.'<a href="#" class="badge bg-light float-end no-radius upload-file text-decoration-none">'.__('Upload').'</a>';
                                                    $form_value = isset($xdata->$name) ? $xdata->$name : $value['value'];
                                            ?>
                                            @switch($value['field'])
                                                @case("textarea")
                                                    <div class="col-12">
                                                        <div class="form-group mb-3">
                                                            <label class="fw-bold w-100">{!! $label !!}</label>
                                                            <textarea class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}">{{ $form_value }}</textarea>
                                                        </div>
                                                    </div>
                                                @break

                                                @case("input")
                                                        <div class="col-12">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold w-100">{!! $label !!}</label>
                                                                <input class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $form_value }}">
                                                            </div>
                                                        </div>
                                                @break
                                            @endswitch
                                        @endforeach


                                    @endforeach
                                </div>
                            </div>

                            <div class="tab-pane fade show active" id="v-pills-company" role="tabpanel" aria-labelledby="v-pills-company-tab">
                                <div class="row">
                                    @php($i=0)
                                    @foreach($settings_data['company_elements'] as $key => $form)
                                        <?php  $name = $form['name'] ?? '';
                                        $placeholder = $form['placeholder'] ?? '';
                                        $type = $form['type'] ?? '';
                                        $label = $form['label'] ?? '';
                                        $upload = $form['upload'] ?? false;
                                        if($upload) $label = $label.'<a href="#" class="badge bg-light float-end no-radius upload-file text-decoration-none">'.__('Upload').'</a>';
                                        $form_value = isset($xdata->$name) ? $xdata->$name : $form['value'];
                                        ?>
                                        @switch($form['field'])
                                            @case("textarea")
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="fw-bold w-100">{!! $label !!}</label>
                                                    <textarea class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}">{{ $form_value }}</textarea>
                                                </div>
                                            </div>
                                            @break

                                            @case("input")
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="fw-bold w-100">{!! $label !!}</label>
                                                    <input class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $form_value }}">
                                                </div>
                                            </div>
                                            @break
                                        @endswitch
                                        @php($i++)
                                    @endforeach
                                </div>
                            </div>

                            <div class="tab-pane fade pt-4" id="v-pills-reviews" role="tabpanel" aria-labelledby="v-pills-reviews-tab">
                                <?php $i=0;?>
                                @foreach($settings_data['customer_reviews'] as $key => $form)
                                    <?php $i++;?>
                                    <div class="card card-rounded mb-4 p-lg-2">
                                        <div class="card-body card-rounded border">
                                            <h4 class="card-title card-title-dash mb-4">{{ __('Review') }} {{$i}}</h4>
                                            <div class="row">
                                                @foreach($form as $value)

                                                    <?php  $name = $value['name'] ?? '';
                                                            $placeholder = $value['placeholder'] ?? '';
                                                            $type = $value['type'] ?? '';
                                                            $label = $value['label'] ?? '';
                                                            $upload = $value['upload'] ?? false;
                                                            if($upload) $label = $label.'<a href="#" class="badge bg-light float-end no-radius upload-file text-decoration-none">'.__('Upload').'</a>';
                                                            $form_value = isset($xdata->$name) ? $xdata->$name : $value['value'];
                                                    ?>

                                                    @switch($value['field'])

                                                        @case("input")
                                                        <div class="col-12 col-md-4">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold w-100">{!! $label !!}</label>
                                                                <input class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $form_value }}">
                                                            </div>
                                                        </div>
                                                        @break

                                                        @case("textarea")
                                                        <div class="col-12">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold w-100">{!! $label !!}</label>
                                                                <textarea class="form-control form-control-lg" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}">{{ $form_value }}</textarea>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @endswitch
                                                @endforeach
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
    </form>
</div>


<div class="modal fade" id="upload_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">{{__('Upload')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="current-item-name">
                <div id="agency-dropzone" class="dropzone mb-1">
                    <div class="dz-default dz-message">
                        <input class="form-control form-control-lg" name="thumbnail" id="uploaded-file" type="hidden">
                        <span><i class="fas fa-cloud-upload-alt"></i> {{ __("Upload") }}</span>
                    </div>
                </div>

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
    @include('member.settings.agency-landing-settings-js')
@endprepend
