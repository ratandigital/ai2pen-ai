@extends('layouts.auth')
@section('title',__("Affiliate Settings"))
@section('page-header-title',__("Settings"))
@section('page-header-details',__('Affiliate Setting'))
@section('content')

    <div class="content-wrapper">
        <div class="col-sm-12">
            <div class="statistics-details mb-0">
                <div class="row">
                    <div class="col-sm-4 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div class="col" data-bs-toggle='tooltip' title="<?php echo __('Affiliate will get commission on every user signup who have come through the affiliation link.'); ?>">
                                    <div>
                                        <p class="card-title card-title-dash font-weight-medium">
                                            {{__('Signup Commission')}}
                                        </p>
                                        <h3 class="rate-percentage d-flex justify-content-between">
                                            {{$curency_icon.' '.$signup_amount}} <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-account-arrow-left me-2 icon-md"></i></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div class="col" data-bs-toggle='tooltip' title="<?php echo __('Affiliate will get fixed/percentage commission on package buying.'); ?>">
                                    <div>
                                        <p class="card-title card-title-dash font-weight-medium">
                                            {{__('Payment Commission')}}
                                        </p>
                                        <h3 class="rate-percentage d-flex justify-content-between">
                                            <?php echo  ucfirst($payment_commission_type) ; ?> <span class="text-primary text-medium d-flex align-items-center"> <i class="mdi mdi-calendar-month-outline me-2 icon-md"></i></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div class="col" data-bs-toggle='tooltip' title="<?php echo __('Affiliate will get commission on every user package buying who have come through the affiliation link.'); ?>">
                                    <div>
                                         <?php
                                                if($payment_commission_type == 'percentage')
                                                    $payment_commission_amount = $payment_commission_amount.' %';
                                                else
                                                    $payment_commission_amount = $curency_icon.' '.$payment_commission_amount;
                                            ?>
                                        <p class="card-title card-title-dash font-weight-medium">
                                            {{__('Amount')}}
                                        </p>
                                        <h3 class="rate-percentage d-flex justify-content-between">
                                             {{$payment_commission_amount}} $ <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-calendar-today-outline me-2 icon-md"></i></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
                <div class="row">
                    <div class="col-12 col-lg-9 order-2 order-lg-1">

                        <div class="card card-rounded mb-4 p-lg-2">
                            <div class="card-body card-rounded">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="card-title card-title-dash"><?php echo __('Affiliate Link'); ?> </h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("Get Link")}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-center" id="gif_div">
                                            <img width="30%" class="center-block" src="<?php echo asset('assets/images/pre-loader/loading-animations.gif'); ?>" alt="Processing...">
                                        </div>
                                        <div class="link_div_class"  id="link_div">
                                            <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword"><?php echo $aff_link; ?></span></code></pre>
                                        </div>
                                    </div>
                                </div>     
                            </div>         
                        </div>  
                    </div>
                    @include('affiliate.affiliate_user.sidebar')
                </div>
        </div>

    </div>

@endsection


@push('scripts-footer')
    <script src="{{ asset('assets/js/pages/affiliate/settings.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/inlinecss.css') }}">
@endpush
