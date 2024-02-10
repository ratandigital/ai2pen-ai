<?php
$lang_display =  __('Affiliate Settings ');
$title_display = $lang_display;
?>
@extends('layouts.auth')
@section('title',$title_display)
@section('page-header-title',$title_display)
@section('page-header-details',__('All your Affiliate in one place'))
@section('content')

<?php
$general_signup_commission = isset($info['signup_commission']) ? $info['signup_commission']:0;
$general_sign_up_amount = isset($info['sign_up_amount']) ? $info['sign_up_amount']:'';
$general_payment_commission = isset($info['payment_commission']) ? $info['payment_commission']:0;
$general_payment_type = isset($info['payment_commission']) ? $info['payment_type']:'';
$general_payment_percentage = isset($info['percentage']) ? $info['percentage']:'';
$general_payment_fixed_amount = isset($info['fixed_amount']) ? $info['fixed_amount']:'';
$general_is_recurring = isset($info['is_recurring']) ? $info['is_recurring']:0;
?>
<div class="content-wrapper">

    @if(session('status'))
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Saved')}}</h4>
            <p> {{ session('status') }}</p>
        </div>
    @endif

    @if (session('save_user_status')=='1')
        <div class="alert alert-success">
            <h4 class="alert-heading">{{__('Successful')}}</h4>
            <p> {{ __('User has been saved successfully.') }}</p>
        </div>
    @elseif (session('save_user_status')=='0')
        <div class="alert alert-danger">
            <h4 class="alert-heading">{{__('Failed')}}</h4>
            <p>
                {{ __('Something went wrong. Failed to save user.') }}&nbsp;{{session('save_user_status_error')}}
            </p>
        </div>
    @endif


    <div class="card card-rounded mb-4 p-lg-2">
        <div class="card-body card-rounded">
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h4 class="card-title card-title-dash">{{$lang_display}}</h4>
                    <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Customize Affiliate Settings")}}</p>
                </div>
            </div>
            <div>
                <div class="nav nav-pills flex-row justify-content-center" id="v-pills-tab" role="tablist" aria-orientation="vertical" >
                    <button class="mt-2 me-2 rounded-pill nav-link active " id="v-pills-users-tab" data-bs-toggle="pill" data-bs-target="#v-pills-users" type="button" role="tab" aria-controls="v-pills-users" aria-selected="true">{{ __("Users") }}</button>
                    <button class="mt-2 me-2 rounded-pill nav-link" id="v-pills-user-request-tab" data-bs-toggle="pill" data-bs-target="#v-pills-user-request" type="button" role="tab" aria-controls="v-pills-user-request" aria-selected="true">{{ __("Users Request") }}</button>
                    <button class="mt-2 me-2 rounded-pill nav-link" id="v-pills-withdrawals-tab" data-bs-toggle="pill" data-bs-target="#v-pills-withdrawals" type="button" role="tab" aria-controls="v-pills-withdrawals" aria-selected="false">{{ __("Withdrawal Requests") }}</button>
                    <button class="mt-2 me-2 rounded-pill nav-link" id="v-pills-commission-tab" data-bs-toggle="pill" data-bs-target="#v-pills-commission" type="button" role="tab" aria-controls="v-pills-commission" aria-selected="false">{{ __("Commission Settings") }}</button>
                </div>
            </div>

            <div class="tab-content p-4" id="v-pills-tabContent">
                <div class="tab-pane fade show active " id="v-pills-users" role="tabpanel" aria-labelledby="v-pills-users-tab">
                    <div class="card border-0" id="v-pills-users-table">
                        <h4 class="card-title">
                            {{ __("Affilaiate Users") }}
                        </h4>
                        <div class="card no-shadow">
                            <div class="card-body data-card p-0 mt-3">
                                <div class="table-responsive">
                                    <table class='table table-sm table-select' id="mytable1" >
                                        <thead>
                                        <tr class="table-light">
                                            <th>#</th>
                                            <th>
                                                {{-- <div class="form-check form-switch"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div> --}}
                                                <div class="form-check form-switch d-flex justify-content-center pt-2"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                            </th>
                                            <th>{{__("Name") }}</th>
                                            <th>{{__("Email") }}</th>
                                            <th>{{__("Referral") }}</th>
                                            <th>{{__("Balance") }}</th>
                                            <th>{{__("Approved") }}</th>
                                            <th>{{__("Pending") }}</th>
                                            <th>{{__("Actions") }}</th>
                                            <th>{{__("Last IP") }}</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center text-primary waiting mt-5"></div>
                    <div id="addOrUpdate">
                        @include('affiliate.affiliate_admin.user-form')
                    </div>
                </div>
                 <div class="tab-pane fade" id="v-pills-user-request" role="tabpanel" aria-labelledby="v-pills-user-request-tab">
                     <div class="card border-0" id="v-pills-users-request-table">
                        <h4 class="card-title">
                            {{ __("Affilaiate Users Request") }}
                        </h4>
                         <div class="card no-shadow">
                             <div class="card-body data-card p-0 mt-3">
                                 <div class="table-responsive">
                                    <table class='table table-sm table-select' id="mytable2" >
                                         <thead>
                                         <tr class="table-light">
                                             <th>#</th>
                                             <th>
                                                 <div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                                             </th>
                                             <th>{{__("Id") }}</th>
                                             <th>{{__("Name") }}</th>
                                             <th>{{__("Website") }}</th>
                                             <th>{{__('Affiliating process')}}</th>
                                             <th>{{__("Email") }}</th>
                                             <th>{{__("FB link") }}</th>
                                             <th>{{__("Submission Date") }}</th>
                                             <th>{{__("Status") }}</th>
                                             <th>{{__("Action") }}</th>
                                         </tr>
                                         </thead>
                                         <tbody></tbody>
                                     </table>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="text-center text-primary waiting mt-5"></div>
                 </div>
                <div class="tab-pane fade" id="v-pills-commission" role="tabpanel" aria-labelledby="v-pills-commission-tab">
                    @include('affiliate.affiliate_admin.commission-settings')
                </div>

                <div class="tab-pane fade" id="v-pills-withdrawals" role="tabpanel" aria-labelledby="v-pills-withdrawals-tab">
                    <div class="card border-0" id="v-pills-withdrawal-request-table1">
                        <h4 class="card-title">
                            {{ __("Withdrawal Requests") }}
                        </h4>
                        <div class="card no-shadow">
                            <div class="card-body data-card p-0 mt-3">
                                <div class="table-responsive">
                                    <table class='table table-sm table-select' id="mytable3" >
                                        <thead>
                                        <tr class="table-light">
                                            <th>#</th>
                                            <th>
                                                <div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox"  id=""></div>
                                            </th>
                                            <th>{{__("Id") }}</th>
                                            <th>{{__("Name") }}</th>
                                            <th>{{__("Available Amount") }}</th>
                                            <th>{{__("Requested Amount") }}</th>
                                            <th>{{__("Status") }}</th>
                                            <th>{{__("Submission Date") }}</th>
                                            <th>{{__("Action") }}</th>
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


        </div>
    </div>

</div>

<div class="modal fade" id="modal_send_sms_email" tabindex="-1" aria-labelledby="modal_send_sms_email_label"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{{ __("Send Email") }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <div id="show_message" class="text-center"></div>

                <div class="form-group">
                    <label for="subject">{{ __("Subject") }} *</label><br/>
                    <input type="text" id="subject" class="form-control"/>
                    <div class="invalid-feedback">{{ __("Subject is required") }}</div>
                </div>

                <div class="form-group">
                    <label for="message">{{ __("Message") }} *</label><br/>
                    <textarea name="message" class="h-min-200px form-control" id="message"></textarea>
                    <div class="invalid-feedback">{{ __("Message is required") }}</div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="send_sms_email" class="btn btn-primary" > <i class="fas fa-paper-plane"></i>  {{ __("Send") }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> {{ __("Close") }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="affiliating_process_information" tabindex="-1" aria-labelledby="affiliating_process_information"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{{ __("Affiliating Processing Information") }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <p id="information"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> {{ __("Close") }}</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/affiliate/manage-users.js') }}"></script>
    <script src="{{ asset('assets/js/pages/affiliate/commission-settings.js') }}"></script>
    <script src="{{ asset('assets/js/pages/affiliate/user-request.js') }}"></script>
    <script src="{{ asset('assets/js/pages/affiliate/withdrawal-requests.js') }}"></script>
    <script src="{{ asset('assets/js/pages/affiliate/withdrawal-requests-admin.js') }}"></script>
@endpush
