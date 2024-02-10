@section('content')
@extends('layouts.auth')
@section('title',__("Withdrawal Methods"))
@section('page-header-title',__("Withdrawal Methods"))
@section('page-header-details',__('Affiliate Withdrawal Methods'))
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/inlinecss.css') }}">

    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 col-lg-9 order-2 order-lg-1">
                <div class="card border-0">
                    <div class="card no-shadow">
                        <div class="card-body data-card">
                            <div class="d-sm-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="card-title card-title-dash">{{__('Withdrawal Methods')}}</h4>
                                    <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("List of all withdrawal methods")}}</p>
                                </div>
                                <div class="btn-wrapper mb-2">
                                   <a href="#" target="_BLANK" class="btn btn-otline-dark border btn-sm mb-0 me-0 add_method"><i class="fas fa-plus-circle"></i> {{ __("Create") }}</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class='table table-select w-100' id="mytable">
                                    <thead>
                                    <tr class="table-light">
                                        <th>#</th>
                                        <th>{{__("ID") }}</th>
                                        <th>{{__("Method") }}</th>
                                        <th>{{__("Created") }}</th>
                                        <th>{{__("Actions") }}</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('affiliate.affiliate_user.sidebar')
        </div>
    </div>
 
    <div class="modal fade" id="method_details_modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bbw">
                    <h4 class="modal-title text-center blue"><?php echo __("Method Details"); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body section">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-title fw-bold"><?php echo __('Name'); ?></div>
                            <p class="section-lead" id="method_name"></p>

                            <div class="section-title fw-bold"><?php echo __('Details'); ?></div>
                            <div class="section-lead">
                                <p class="" id="method_details"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_witdrawalMethod_modal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bbw">
                    <h5 class="modal-title text-center blue"><?php echo __("New Method"); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="#" enctype="multipart/form-data" id="witdrawalMethod_add_form" method="post">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><?php echo __('Method'); ?></label>
                                            <select name="method_type" id="method_type" class="form-control select2" >
                                                <option value=""><?php echo __('Select Method'); ?></option>
                                                <option value="paypal"><?php echo __('PayPal'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12" id="paypal_email_div" >
                                        <div class="form-group">
                                            <label><?php echo __('PayPal Email'); ?></label>
                                            <input type="email" class="form-control" name="paypal_email" id="paypal_email">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-12 padding-0">
                        <button type="submit" class="btn btn-sm rounded btn-success text-white" id="save_method_info"><i class="fas fa-save"></i>{{ __('Save') }}</button>
                        <a class="btn btn-sm rounded btn-light text-white float-end" data-bs-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo __("Cancel") ?> </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="edit_witdrawalMethod_modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bbw">
                    <h5 class="modal-title text-center blue"><?php echo __("Update Method"); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="method_update_body"></div>

                <div class="modal-footer bg-whitesmoke action_div">
                    <div class="col-12 padding-0">
                        <button type="submit" class="btn btn-sm rounded btn-success text-white" id="update_method_info"><i class="fas fa-edit"></i>{{ __('Update') }}</button>
                        <a class="btn btn-sm rounded btn-light text-white float-end" data-bs-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo __("Cancel") ?> </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/affiliate/withdrawal-methods.js') }}"></script>
@endpush
