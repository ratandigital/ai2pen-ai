<?php $display_currency = $curency_icon;?>
<div class="row">
    <div class="col-sm-12">
        <div class="statistics-details mb-0">
            <div class="row">
                <div class="col-sm-4 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <p class="card-title card-title-dash font-weight-medium">
                                    {{__('Package')}}
                                    <a class="badge badge-opacity-warning text-decoration-none" href="{{route('pricing-plan')}}">{{Auth::user()->package_id==1 ?__('Upgrade to Pro') : __('Renew / Upgrade')}}</a>
                                </p>
                                <h3 class="rate-percentage d-flex justify-content-between">
                                    {{$package_name}} <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-shopping-outline me-2 icon-md"></i></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <p class="card-title card-title-dash font-weight-medium">{{(__('Price'))}}</p>
                                <h3 class="rate-percentage d-flex justify-content-between align-items-center">
                                    <?php if($price=="Trial" || $price=="Free") $price=0; ?>
                                    <?php echo $price>0 ? $display_currency.' '.number_format($price,'2','.','').'/'.$validity.' '.__("Days") : $display_currency.' 0.00';?>
                                        <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-currency-usd me-2 icon-md"></i></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <p class="card-title card-title-dash font-weight-medium">{{__('Expiry')}}</p>
                                <h3 class="rate-percentage d-flex justify-content-between">
                                    <?php echo $price>0 ? date("jS M Y",strtotime($expired_date)) : __('Never'); ?>
                                        <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-calendar-range me-2 icon-md"></i></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
