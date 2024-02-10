<link rel="stylesheet" href="{{ asset('assets/css/inlinecss.css') }}">

<div class="row">
	<div class="col-12">
		<form action="" method="POST" id="affiliate_commission_settings_form">
			<div class="card">
				<div class="card-body pb-0">

					<div class="row">
						<div class="col-12"><p class="section-title"><?php echo __('Commission Settings'); ?></p></div>
						<div class="col-12 col-md-6">
							<ul class="list-group mb-4">
								<li class="list-group-item">
									<div class="form-group mb-0">
										<div class="form-check form-switch  mt-3">
										    <input type="checkbox" class="form-check-input" value="1" id="by_signup_common" name="signup_commission_common" <?php if(isset($info['signup_commission']) && $info['signup_commission'] == '1') echo "checked"; else echo ""; ?>>
										    <label class="form-check-label" for="by_signup2">{{__('Sign up Commission')}}
										        <a href="#" data-bs-placement="top" data-bs-trigger="focus" data-bs-toggle="popover" title="<?php echo __("Signup Commission"); ?>" data-bs-content="<?php echo __("Affiliate will get commission on every user signup who have come through the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a>
										    </label>
										</div>
									</div>
								</li>
							</ul>

							<div class="" id="signup_sec_div_common" >
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-text">
                                            <?php echo $curency_icon?? "$"; ?>
                                        </div>
                                        <input type="text" placeholder="{{__('Amount')}}" class="form-control form-control-lg" name="signup_amount_common" id="signup_amount_common" value="<?php echo isset($info['sign_up_amount'])? $info['sign_up_amount']:""; ?>">
                                    </div>
                                </div>
							</div>
						</div>

						<div class="col-12 col-md-6">
							<ul class="list-group mb-4">
								<li class="list-group-item">
									<div class="form-group mb-0">
										<div class="form-check form-switch  mt-3">
										    <input type="checkbox" class="form-check-input" value="1" id="by_payment_common" name="payment_commission_common" <?php if(isset($info['payment_commission']) && $info['payment_commission'] == '1') echo "checked";?>>
										    <label class="form-check-label" for="by_payment_common">{{__('Payment Commission')}}
										        <a href="#" data-bs-placement="top" data-bs-trigger="focus" data-bs-toggle="popover" title="<?php echo __("Payment Commission"); ?>" data-bs-content="<?php echo __("Affiliate will get commission on every package buying package payment who have registered with the affiliate link."); ?>"><i class='fa fa-info-circle'></i> </a>
										    </label>
										</div>

									</div>
								</li>
							</ul>

                            <div class="card card-rounded mb-4 p-lg-2 border" id="payment_sec_div_common">
                                <div class="card-body card-rounded">
                                    <div class="d-sm-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="card-title card-title-dash"><?php echo __('Payment Type'); ?> </h4>
                                            <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4"><?php echo __('Settings'); ?></p>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <div class="form-check form-switch ">
                                                    <input type="radio" class="form-check-input" name="payment_type_common" id="payment_type_common" value="fixed" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='fixed') echo "checked";?>>
                                                    <label class="form-check-label" for="payment_type">{{__('Fixed')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <div class="form-check form-switch ">
                                                    <input type="radio" class="form-check-input" name="payment_type_common" id="payment_type_common" value="percentage" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='percentage') echo "checked";?>>
                                                    <label class="form-check-label" for="payment_type_common">{{__('Percentage')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <div class="form-check form-switch ">
                                                    <input type="radio" class="form-check-input" name="is_recurring_common" id="is_recurring_common" value="1" <?php if(isset($info["is_recurring"]) && $info["is_recurring"]=='1') echo "checked";?>>
                                                    <label class="form-check-label" for="payment_type2">{{__('Recurring')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" id="fixed_amount_div_common" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='fixed') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <?php echo $curency_icon; ?>
                                            </div>
                                            <input type="text" class="form-control form-control-lg" name="fixed_amount_common" id="fixed_amount_common" value="<?php echo isset($info['fixed_amount']) ? $info['fixed_amount']:""; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group" id="percentage_div_common" <?php if(isset($info["payment_type"]) && $info["payment_type"]=='percentage') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>>
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <i class="fas fa-percent"></i>
                                            </div>
                                            <input type="text" class="form-control form-control-lg" name="percent_amount_common" id="percent_amount_common" value="<?php echo isset($info['percentage']) ? $info['percentage']:""; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
					<div>
						<button type="submit" class="btn btn-success text-white align-items-center" id="submit_commission"><i class="fas fa-check-circle"></i><?php echo __('Save'); ?></button>
					</div>
				</div>
			</div>
	    </form>
	</div>
</div>
