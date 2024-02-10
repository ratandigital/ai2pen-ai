<?php
$bg_effect = '
<div class="absolute bottom-0 left-1/2 -z-10 -translate-x-1/2">
  <svg width="1174" height="560" viewBox="0 0 1174 560" fill="none" xmlns="http://www.w3.org/2000/svg">
    <g opacity="0.4" filter="url(#filter0_f_41_257)">
      <rect x="450.531" y="279" width="272.933" height="328.051" fill="url(#paint0_linear_41_257)" />
    </g>
    <defs>
      <filter id="filter0_f_41_257" x="0.531494" y="-171" width="1172.93" height="1228.05" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
        <feFlood flood-opacity="0" result="BackgroundImageFix" />
        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
        <feGaussianBlur stdDeviation="225" result="effect1_foregroundBlur_41_257" />
      </filter>
      <linearGradient id="paint0_linear_41_257" x1="425.16" y1="343.693" x2="568.181" y2="660.639" gradientUnits="userSpaceOnUse">
        <stop stop-color="#ABBCFF" />
        <stop offset="0.859375" stop-color="#4A6CF7" />
      </linearGradient>
    </defs>
  </svg>
</div>';
?>

<section id="pricing" class="pt-14 sm:pt-20 lg:pt-[130px]">
    <div class="px-4 xl:container">
      <!-- Section Title -->
      <div class="relative mb-12 w-full pt-10 text-center md:mb-20 lg:pt-16" data-wow-delay=".2s">
        <span class="title whitespace-nowrap"> {{__("Pricing Plans")}})</span>
        <h2 class="mx-auto mb-5 max-w-[600px] font-heading text-3xl font-semibold text-dark dark:text-white sm:text-4xl md:text-[50px] md:leading-[60px]"> {{__("Affordable Pricing With Simple Plans")}} </h2>
        <p>
          @if(count($package_validity_list)>1)
                @foreach($package_validity_list as $kv=>$vv)
                    <a href="{{route('pricing-plan')}}?validity={{$kv}}"
                        class="
                          <?php if($kv==$default_validity) echo 'bg-primary';?>
                          cs-price-button
                          hover:bg-primary
                          inline-flex items-center rounded
                          bg-dark-text font-heading
                          text-base text-white
                          ">
                        {{$vv}}
                    </a>
                @endforeach
          @endif
        </p>
      </div>
      <div class="relative z-10 flex flex-wrap justify-center overflow-hidden rounded drop-shadow-light dark:drop-shadow-none">

        <div class="absolute top-0 left-0 -z-10 h-full w-full bg-cover bg-center opacity-10 dark:opacity-40 bg-noise-pattern">

        </div>
        {!!$bg_effect!!}

        <?php
          $i=0;
          $premium_packages = null;
          $agency_packages = null;
          $replace_array = [
              __('Day'),
              __('Month'),
              __('Week'),
              __('Year')
          ];
        ?>
        @foreach($get_pricing_list as $key=>$value)
            <?php
            $not_in_package = '';
            if($value->is_default=='0') {
                $premium_packages[] = (array) $value;
                continue;
            }
            $i++;
            $price = $value->price;

            $class = '';
            if($i==1 || $i==4) $class='first-item';
            if($i==3 || $i==6) $class='last-item';

            $validity = $value->validity;
            $validity_extra_info = $value->validity_extra_info;
            if($validity>0){
                $validity_text = convert_number_validity_phrase($validity);
            }
            if($validity==0) {
                $validity_text = __('Forever');
            }

            $module_ids = explode(',',$value->module_ids);
            $monthly_limit = json_decode($value->monthly_limit,true);
            $buy_button_name = __('Get Access');
            ?>

            <div class="w-full sm:w-1/2 lg:w-1/3">
              <div class="pt-10 pb-20 text-center" data-wow-delay=".2s">
                <div class="border-b dark:border-[#2E333D]">
                  <h3 class="mb-2 font-heading text-3xl font-medium text-dark dark:text-white">
                    {{$value->package_name}}
                  </h3>
                  <p class="pb-10 text-base text-dark-text"> {{__("The basic plan")}} </p>
                </div>
                <div class="border-b py-10 dark:border-[#2E333D]">
                  <p class="mx-auto max-w-[300px] text-base text-dark-text pb-2 mb-2">{{__("Just sign up and try it out")}}</p>
                  <h3 class="mb-6 flex items-end justify-center pt-2 font-heading text-base font-medium text-dark dark:text-white">
                     {!!$price=='Trial' ? '<sup class="-mb-2 package_price">'.__('Trial').'</sup>/'.strtolower($validity_text) : __('FREE')!!}
                  </h3>
                </div>
                <div class="space-y-4 px-6 pt-10 pb-[60px] text-left sm:px-10 md:px-8 lg:px-10 xl:px-20">

                  <?php
                  foreach($get_modules as $key2=>$value2):
                      $check_class = !in_array($value2->id,$module_ids) ? '' : 'text-[#00CB99]';
                      
                      $limit=0;
                      $limit=convert_number_numeric_phrase($monthly_limit[$value2->id]??0,0);
                      if($limit=="0") $limit2=strtolower(__("Unlimited"));
                      else $limit2=$limit;

                      if($value2->extra_text=='') $value2->extra_text = strtolower(__("Unit"));
                      if($limit>0)
                          $limit2=$limit2." ".strtolower(__($value2->extra_text));

                      // removing limit if have no access  
                      if(!in_array($value2->id,$module_ids)) $limit2="";

                      $module_name = $value2->module_name;

                      echo '
                        <p class="flex items-center text-base text-dark-text">
                          <span class="inline-block pr-2 '.$check_class.'">
                            <svg width="24" height="24" viewBox="0 0 24 24" class="fill-current">
                              <path d="M9.99999 15.172L19.192 5.979L20.607 7.393L9.99999 18L3.63599 11.636L5.04999 10.222L9.99999 15.172Z" />
                            </svg>
                          </span> '.__($module_name).' <br> '.$limit2.'
                        </p>';
                  endforeach;?>
                </div>
                @if(!Auth::user())
                  <a href="{{route('register')}}" class="inline-flex items-center rounded bg-dark-text py-[14px] px-8 font-heading text-base text-white hover:bg-primary">
                      {{$buy_button_name}}
                  </a>
                @endif
              </div>
            </div>
        @endforeach

        <?php $package_map = [];?>
        @if(!empty($premium_packages))
            <?php
                $count_premium = 0;
                $min_subscriber = 0;
                $first_package_id = 0;
                $first_package_price = 0;
                $first_package_discount_message = '';
                $first_package_discount_terms = '';
                $first_package_discount_percent = '';
                $first_package_validity = '';
                $first_package_name = '';
                $premium_package_li_str = '';
            ?>
            @foreach($premium_packages as $key=>$value)
                @php
                    $monthly_limit_temp = json_decode($value['monthly_limit'],true);
                    $discount_data = $value['discount_data'];
                    $price_raw_data = format_price($value['price'],$format_settings,$discount_data,['return_raw_array'=>true]);

                    $discount_message = '';
                    if(isset($price_raw_data->discount_valid) && $price_raw_data->discount_valid)
                    $discount_message = __('Save').' '.$price_raw_data->discount_amount_formatted_currency;
                    $package_subscriber_map = convert_number_numeric_phrase($monthly_limit_temp[1],0) ?? -1;
                    $package_id_map = $value['id'];
                    $package_name = $value['package_name'];

                    $validity = $value['validity'];
                    $validity_extra_info = $value['validity_extra_info'];
                    $validity_unit = __('Day');
                    if($validity>0){
                        $validity_text = convert_number_validity_phrase($validity);
                    }
                    if($validity==0) {
                        $validity_text = __('Forever');
                    }

                    $package_discount_percent = $price_raw_data->discount_percent;
                    $package_discount_percent = $package_discount_percent>0  ? " ".$package_discount_percent.'% '.__('OFF') : '';

                    if($count_premium==0){
                        $min_subscriber = $package_subscriber_map;
                        $first_package_id = $package_id_map;
                        $first_package_price = $price_raw_data->display_price_currency ?? '';
                        $first_package_discount_message = $discount_message;
                        $first_package_discount_terms = $price_raw_data->discount_terms;
                        $first_package_validity = $validity_text;
                        $first_package_name = $package_name;

                        $first_package_discount_percent = $package_discount_percent;
                    }
                    $count_premium++;
                    $package_price_map = $price_raw_data->display_price_currency ?? '';
                    $package_map[$count_premium] = ['id'=>$package_id_map,'price'=>$package_price_map,'subscriber'=>$package_subscriber_map,'discount_message'=>$discount_message,"validity_text"=>$validity_text,'name'=>$package_name,'terms'=>$price_raw_data->discount_terms,'percent'=>$package_discount_percent];

                    $module_ids = explode(',',$value['module_ids']);
                    foreach($get_modules as $key2=>$value2):                        
                        $module_name = $value2->module_name;
                        $li_class = 'premium-li premium-'.$count_premium;
                        $hide_other_package_unavailable_module = $count_premium>1 ? 'cs-d-none' : '';
                        $check_class = !in_array($value2->id,$module_ids) ? '' : 'text-[#00CB99]';
                        if(!in_array($value2->id,$module_ids)) {
                            $premium_package_li_str .= '
                            <p class="flex items-center text-base text-dark-text '.$li_class.' '.$hide_other_package_unavailable_module.'">
                              <span class="inline-block pr-2 '.$check_class.'">
                                <svg width="24" height="24" viewBox="0 0 24 24" class="fill-current">
                                  <path d="M9.99999 15.172L19.192 5.979L20.607 7.393L9.99999 18L3.63599 11.636L5.04999 10.222L9.99999 15.172Z" />
                                </svg>
                              </span> <span class="">'.__($module_name).'</span>
                            </p>';
                            continue;
                        }
                        $limit=0;
                        $limit=convert_number_numeric_phrase($monthly_limit_temp[$value2->id??0],0);
                        if($limit=="0") $limit2=strtolower(__("Unlimited"));
                        else $limit2=$limit;
                        if($value2->extra_text=='') $value2->extra_text = strtolower(__("Unit"));
                        if($limit>0)
                            $limit2=$limit2." ".strtolower(__($value2->extra_text));
                        if($count_premium>1) $li_class .= ' cs-d-none';

                        $premium_package_li_str .= '
                        <p class="flex items-center text-base text-dark-text '.$li_class.'">
                          <span class="inline-block pr-2 '.$check_class.'">
                            <svg width="24" height="24" viewBox="0 0 24 24" class="fill-current">
                              <path d="M9.99999 15.172L19.192 5.979L20.607 7.393L9.99999 18L3.63599 11.636L5.04999 10.222L9.99999 15.172Z" />
                            </svg>
                          </span> <span class="">'.__($module_name).' <br> '.$limit2.'</span>
                        </p>';
                    endforeach;
                @endphp
            @endforeach

            <div class="w-full dark:border-[#2E333D] sm:w-1/2 sm:border-l lg:w-1/3 lg:border-l">
              <div class="pt-10 pb-20 text-center" data-wow-delay=".2s">
                <div class="border-b dark:border-[#2E333D]">
                  <h3 class="mb-2 font-heading text-3xl font-medium text-dark dark:text-white">
                    <span id="package_name">{{$first_package_name}}</span> <span id="discount_percentage" class="text-success">{{!empty($first_package_discount_percent) ? ":".$first_package_discount_percent : '';}}</span>
                  </h3>
                  <p class="pb-10 text-base text-dark-text">
                    {{__("The premium plan")}}
                    <b id="package_price_save" class="<?php echo !empty($first_package_discount_message) ? 'cs-d-inline' : 'cs-d-none'; ?>">(<?php echo $first_package_discount_message;?>)</b>
                  </p>
                </div>
                <div class="border-b py-10 dark:border-[#2E333D]">

                  <p class="mx-auto max-w-[300px] text-base text-dark-text pb-2 mb-2" id="discount_extra_message">
                    {{!empty($first_package_discount_terms) ? $first_package_discount_terms :__("All of these at affortable price")}}
                  </p>
                  <h3 class="mb-6 flex items-end justify-center pt-2 font-heading text-base font-medium text-dark dark:text-white">
                     <sup class="-mb-2 text-[55px]" id="package_price"><?php echo $first_package_price;?></sup>/<span id="validity_text">{{strtolower($first_package_validity)}}</span>
                  </h3>
                </div>
                <div class="space-y-4 px-6 pt-10 pb-[60px] text-left sm:px-10 md:px-8 lg:px-10 xl:px-20">
                  <input type="range" class="cs-w-100" min="1" max="{{$count_premium}}" step="1" value="1" id="package_bot_subscriber_range">

                  <p class="flex items-center text-base text-dark-text">
                    {!!$premium_package_li_str!!}
                  </p>
                </div>
                <a href="{{route('buy-package',$first_package_id)}}" id="package_link" class="inline-flex items-center rounded bg-primary py-[14px] px-8 font-heading text-base text-white hover:bg-opacity-90">
                    {{__("Purchase")}}
                </a>
              </div>
            </div>=
        @endif
        <?php $package_map = json_encode($package_map);?>
      </div>

      <div class="pt-12 text-center">
          <h3 class="mb-5 font-heading text-xl font-medium text-dark dark:text-white sm:text-3xl">{{__("Looking for a customized solution?")}}</h3>
          <p class="text-base text-dark-text"> {{__("Contact our team to get a quote")}} : {{$get_landing_language->company_email??''}} </p>
          <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
      </div>
    </div>
</section>


@push('scripts-footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @include('landing.partials.show-pricing-js')
@endpush
