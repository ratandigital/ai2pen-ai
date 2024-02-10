
@section('content')
@extends('layouts.auth')
@section('title',__("Affiliate Dashboard"))
@section('page-header-title',__("Dashboard"))
@section('page-header-details',__('Affiliate Dashboard'))
@section('content')
    <?php 
       $previous_month_affiliate_income = $current_month_affiliate_income =0;
       $current_month_name = date('M', mktime(0, 0, 0, $dashboard_selected_month, 10));
       $previous_month_name = date('M', mktime(0, 0, 0, ($dashboard_selected_month-1), 10));
       $subscriber_count_yearly = 0;
       $month_year_name = $current_month_name.' '.$year;
       $current_month = (int) $dashboard_selected_month;
       $affiliate_income_yearly  = 0;
       $affiliate_gain_data = [];
       $subscriber_gain_data = [];
       $subscriber_gain_data[] = 0;
       $total_subscriber_gain_data = [];
       for($i=1;$i<=12;$i++){
           $total_subscriber_gain_data[$i] = 0;
           $subscriber_gain_data[$i] = 0;
           $affiliate_gain_data[$i] = 0;
           $total_affiliate_gain_data[$i] = 0;
           $subscriber_gain_previous_year_data[$i] = 0;
       }
        foreach ($affliate_income_monthly as $key=>$value){
            $new_date = (int) $value->new_date;
            $affiliate_income_yearly = $affiliate_income_yearly+$value->data;
            $affiliate_gain_data[$new_date] = $value->data;
            $total_affiliate_gain_data[$new_date] = isset($total_affiliate_gain_data[$new_date]) ? $total_affiliate_gain_data[$new_date] = $total_affiliate_gain_data[$new_date]+$value->data : $total_affiliate_gain_data[$new_date];
            if($new_date==$current_month) $current_month_affiliate_income = $value->data;
            if($new_date==$current_month-1) $previous_month_affiliate_income = $value->data;
        }
        foreach ($affiliate_monthly_subscriber_data as $key=>$value){
            $new_date = (int) $value->new_date;
            $subscriber_count_yearly = $subscriber_count_yearly+$value->data;
            $subscriber_gain_data[$new_date] = $value->data;
            $total_subscriber_gain_data[$new_date] = isset($total_subscriber_gain_data[$new_date]) ? $total_subscriber_gain_data[$new_date] = $total_subscriber_gain_data[$new_date]+$value->data : $total_subscriber_gain_data[$new_date];
            if($new_date==$current_month) $current_month_subscriber = $value->data;
            if($new_date==$current_month-1) $previous_month_subscriber = $value->data;
        }
        ksort($affiliate_gain_data);
        ksort($subscriber_gain_data);
        $affiliate_gain_data_month_names = array_keys($affiliate_gain_data);
        $affiliate_gain_month_data = array_values($affiliate_gain_data);
        $subscriber_gain_data_month_names = array_keys($subscriber_gain_data);
        $subscriber_gain_data_month_data = array_values($subscriber_gain_data);
        $total_subscriber_gain_data_month_data = array_values($total_subscriber_gain_data);
        foreach ($affiliate_gain_data_month_names as $key=>$val)
        {
            $montn_name = date("M", mktime(0, 0, 0, $val, 10));
            $affiliate_gain_data_month_names[$key] = __($montn_name);
        }
        $max_value1 = max($subscriber_gain_data_month_data);
        $max_value2 = max($affiliate_gain_month_data);
        $max_value = max([$max_value1,$max_value2]);
        if($max_value > 10) $step_size = floor($max_value/10);
        else $step_size = 1;

        $max_value_total = max($total_subscriber_gain_data_month_data);
        if($max_value_total > 10) $total_step_size = floor($max_value_total/10);
        else $total_step_size = 1;


        $affiliate_income_difference = round($current_month_affiliate_income-$previous_month_affiliate_income);
        $income_difference_abs = abs($affiliate_income_difference);
        if($previous_month_affiliate_income==0) {
            $affliate_difference_percentage = $current_month_affiliate_income*100;
            $affliate_difference_percentage = round($affliate_difference_percentage).'%';
        }
        else{
             $affliate_difference_percentage = ($affiliate_income_difference>0) ? ($affiliate_income_difference/max($previous_month_affiliate_income,1))*100 : ($affiliate_income_difference/max($current_month_affiliate_income,1))*100;
             $affliate_difference_percentage = !empty($affliate_difference_percentage) ? round($affliate_difference_percentage).'%' : '';
        }
        $current_month_affiliate_income_percentage = $current_month_affiliate_income>0 ? ($current_month_affiliate_income/max($affiliate_income_yearly,1))*100 : 0;
        $previous_month_affiliate_income_percentage = $previous_month_affiliate_income>0 ? ($previous_month_affiliate_income/max($affiliate_income_yearly,1))*100 : 0;
        $current_month_affiliate_income_percentage = round($current_month_affiliate_income_percentage);
        $previous_month_affiliate_income_percentage = round($previous_month_affiliate_income_percentage);
    ?>
    <div class="content-wrapper">
        <div class="col-sm-12">
            <div class="statistics-details mb-0">
                <div class="row">
                    <div class="col-sm-3 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <p class="card-title card-title-dash font-weight-medium">
                                        {{__('Total User')}}
                                    </p>
                                    <h3 class="rate-percentage d-flex justify-content-between">
                                        {{$total_users}} <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-account-arrow-left me-2 icon-md"></i></span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <p class="card-title card-title-dash font-weight-medium">
                                        {{__('Lifetime Income')}}
                                    </p>
                                    <h3 class="rate-percentage d-flex justify-content-between">
                                        <?php echo $payment_life; ?> <span class="text-primary text-medium d-flex align-items-center"> <i class="mdi mdi-calendar-month-outline me-2 icon-md"></i></span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <p class="card-title card-title-dash font-weight-medium">
                                        {{__('Earning Today')}}
                                    </p>
                                    <h3 class="rate-percentage d-flex justify-content-between">
                                         {{convert_number_numeric_phrase($payment_today)}} $ <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-calendar-today-outline me-2 icon-md"></i></span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <p class="card-title card-title-dash font-weight-medium">
                                        {{__('Earning')}} {{$year}}
                                    </p>
                                    <h3 class="rate-percentage d-flex justify-content-between">
                                        {{convert_number_numeric_phrase($payment_year)}} $ <span class="text-primary text-medium d-flex align-items-center"> <i class="mdi mdi-airplane-clock me-2 icon-md"></i></span>
                                    </h3>
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
                  <div class="row mb-4">
                      <div class="col-12">
                          <div class="card card-rounded mb-4 p-lg-2">
                              <div class="card-body card-rounded">
                                  <div class="d-sm-flex justify-content-between align-items-start">
                                      <div>
                                          <h4 class="card-title card-title-dash">{{__('Affiliate Monthly Users vs  Earn History')}} : {{$year}}</h4>
                                          <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">&nbsp;</p>
                                      </div>
                                  </div>
                                  
                                  <div class="row">
                                      <div class="col-12">
                                          <canvas id="monthly_subscriber_years" height="191px" class="mt-2"></canvas>
                                      </div>
                                  </div>      
                              </div>         
                          </div>
                      </div>
                      <div class="col-12 mt-4">
                        <div class="card card-rounded mb-4 p-lg-2">
                            <div class="card-body card-rounded">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="card-title card-title-dash">{{__('Affiliate Daily earnings')}} : {{__('Last 30 days')}}</h4>
                                        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">&nbsp;</p>
                                    </div>
                                </div>
                                <canvas id="ecommerce_earning_chart" height="137px"></canvas>  
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
<script>
    "use strict";
    var earning_chart_labels = <?php echo json_encode($earning_chart_labels)?>;
    var earning_chart_values = <?php echo json_encode($earning_chart_values)?>;
    var affiliate_gain_data_month_names = <?php echo json_encode($affiliate_gain_data_month_names)?>;
    var affiliate_gain_month_data = <?php echo json_encode($affiliate_gain_month_data)?>;
    var current_month_name = '{{$current_month_name}}';
    var affiliate_income_yearly = '{{$affiliate_income_yearly}}';
    var step_size = '{{$step_size}}';
    var previous_month_name = '{{__($previous_month_name)}}';
    var current_year_name = "{{ __('Affiliate') }} - {{$year}}";
    var ecommerce_earning = '{{__('Earning')}}';
    var total_income = '{{__('Earn')}}';
    var current_month_affiliate_income = '<?php echo $current_month_affiliate_income?>';
    var previous_month_affiliate_income = '<?php echo $previous_month_affiliate_income?>';
    var current_month_affiliate_income_percentage = '{{$current_month_affiliate_income_percentage}}';
    var previous_month_affiliate_income_percentage = '{{$previous_month_affiliate_income_percentage}}';
    var subscriber_gain_data_month_names = <?php echo json_encode($subscriber_gain_data_month_names)?>;
    var subscriber_gain_data_month_data = <?php echo json_encode($subscriber_gain_data_month_data)?>;
    var total_subscriber_gain_data_month_data = <?php echo json_encode($total_subscriber_gain_data_month_data)?>;
    var local_subscribers = '{{__('Users')}}';
    var local_subscribers_this_year = local_subscribers+" - "+'{{$dashboard_selected_year}}';
</script>
<script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/affiliate/dashboard.js') }}"></script>
@endpush

@push('styles-header')
<link rel="stylesheet" href="{{ asset('assets/vendors/chartjs/Chart.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/inlinecss.css') }}">
@endpush
