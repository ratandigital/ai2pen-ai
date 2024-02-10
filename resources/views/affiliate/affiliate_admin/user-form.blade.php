<form class="form-horizontal" action="" method="POST" id="affiliate-user-form">
    <input type="hidden" id="action_type" name="action_type">
    <input type="hidden" id="affiliate_id" name="affiliate_id">
    <div class="card no-border">
        <div class="card-body p-0">    

            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="name"> <?php echo __("Full Name")?> *</label>
                        <input name="name" value="<?php echo old('name');?>"  class="form-control form-control-lg" type="text">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="email"> <?php echo __("Email")?> *</label>
                        <input name="email" value="<?php echo old('email');?>"  class="form-control form-control-lg" type="email">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="mobile"><?php echo __("Mobile")?></label>              
                        <input name="mobile" value="<?php echo old('mobile');?>"  class="form-control form-control-lg" type="text">       
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <label for="password"> <?php echo __("Password")?> *</label>
                        <input name="password" class="form-control form-control-lg" type="password">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="password_confirmation"> <?php echo __("Confirm Password")?> *</label>
                        <input name="password_confirmation" class="form-control form-control-lg" type="password">
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="address"> <?php echo __("Address")?></label>
                        <textarea name="address" id="address" class="form-control form-control-lg"><?php echo old('address');?></textarea>
                    </div> 
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="status" > <?php echo __('Status');?></label><br>
                        <div class="form-check form-switch  mt-2">
                          <input class="form-check-input" name="status" type="checkbox" id="status" value="1" checked>
                          <label class="form-check-label" for="status">{{__('Active')}}</label>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="status"><?php echo __('Set Custom Commission');?>
                        <a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo __("Commission Settings"); ?>" data-content="<?php echo __("If you want to set special commission for this affiliate on signup/payment then enable it to start the procedure. If you set special commission, this affiliate will get commissions based on this settings instead of Generic payment setttings for affiliate."); ?>"><i class='fa fa-info-circle'></i> </a>
                        </label><br>
                        <div class="form-check form-switch  mt-2">
                            <input class="form-check-input" name="is_overwritten" type="checkbox" id="is_overwritten" value="1">
                            <label class="form-check-label" for="is_overwritten">{{__('Yes')}}</label>
                        </div>
                    </div>
                </div>      
            </div>

            <div class="row" id="commission_section">
                <div class="col-12">

                    <div class="card card-rounded mb-4 p-lg-2 border mt-4">
                        <div class="card-body card-rounded">
                            <div class="d-sm-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="card-title card-title-dash"><?php echo __('Affiliate Payment'); ?> </h4>
                                    <p>{{__('Settings')}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group mt-2 mb-3">
                                        <div class="form-check form-switch  mt-2">
                                            <input type="checkbox" class="form-check-input" value="1" id="by_signup" name="signup_commission">
                                            <label class="form-check-label" for="by_signup">{{__('Signup Commission')}} 
                                                <a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo __("Signup Commission"); ?>" data-content="<?php echo __("Affiliate will get commission on every user signup who have come through the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group" id="signup_sec_div">
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <?php echo $curency_icon??"$"; ?>
                                            </div>
                                            <input placeholder="{{__('Amount')}}" type="text" class="form-control form-control-lg" name="signup_amount" id="signup_amount">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="form-check form-switch  mt-3">
                                            <input type="checkbox" class="form-check-input" value="1" id="by_payment" name="is_payment">
                                            <label class="form-check-label" for="by_payment">{{__('Payment Commission')}} 
                                                <a href="#" data-placement="top" data-trigger="focus" data-toggle="popover" title="<?php echo __("Payment Commission"); ?>" data-content="<?php echo __("Affiliate will get commission on every package buying payment who have registered with the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a>
                                            </label>
                                        </div>
                                    </div>
                                    <div id="payment_sec_div" >
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <div class="form-check form-switch  mt-2">
                                                        <input type="radio" class="form-check-input" name="payment_type" id="payment_type" value="fixed">
                                                        <label class="form-check-label" for="payment_type">{{__('Fixed')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <div class="form-check form-switch  mt-2">
                                                        <input type="radio" class="form-check-input" name="payment_type" id="payment_type" value="percentage">
                                                        <label class="form-check-label" for="payment_type">{{__('Percentage')}}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="form-group">
                                                    <div class="form-check form-switch  mt-2">
                                                        <input type="checkbox" class="form-check-input" name="is_recurring" id="is_recurring" value="1">
                                                        <label class="form-check-label" for="is_recurring">{{__('Recurring')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mt-1" id="fixed_amount_div" >
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <?php echo $curency_icon ?? "$"; ?>
                                                </div>
                                                <input type="text" class="form-control form-control-lg" name="fixed_amount" id="fixed_amount">
                                            </div>
                                        </div>

                                        <div class="form-group mt-1" id="percentage_div">
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <i class="fas fa-percent"></i>
                                                </div>
                                                <input type="text" class="form-control form-control-lg" name="percent_amount" id="percent_amount">
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

        <div class="px-0 mt-4">
            <button name="submit" id="affiliate-user-form-submit" type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo __("save");?></button>
        </div>
    </div>
</form>