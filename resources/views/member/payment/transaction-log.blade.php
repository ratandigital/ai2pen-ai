@section('content')
@extends('layouts.auth')
@section('title',$is_admin ? __('Earnings') : __('Transactions'))
@section('page-header-title',$is_admin ? __('Earning Summary') : __('Transactions Log'))
@section('page-header-details',__(':title at a glance',['title'=>$is_admin?__('Earning summary'):__('All your transactions')]))
@section('content')
    <div class="content-wrapper">
      @if (session('xendit_currency_error')!='')
          <div class="alert alert-danger">
              <h4 class="alert-heading">{{__('Payment Failed')}}</h4>
              <p>
                  {{ __('Something went wrong. Failed to complete payment.') }}&nbsp;{{session('xendit_currency_error')}}
              </p>
          </div>
      @elseif(session('payment_success')=='0')
          <div class="alert alert-danger">
              <h4 class="alert-heading">{{__('Payment Cancelled')}}</h4>
              <p>
                  {{ __('Something went wrong. The payment was cancelled.') }}
              </p>
          </div>
      @elseif(session('payment_success')=='1')
          <div class="alert alert-success">
              <h4 class="alert-heading">{{__('Payment Successful')}}</h4>
              <p>
                  {{ __('Payment has been processed successfully. It may take few minutes to appear payment in this list.') }}
              </p>
          </div>
      @endif


      @if($is_member && Auth::user()->package_id==1)
        <div class="row flex-grow">
          <div class="col-12 grid-margin stretch-card">
            <div class="card card-rounded table-darkBGImg">
              <div class="card-body">
                <div class="col-sm-8">
                  <h3 class="text-white upgrade-info mb-4">
                    {{__('Upgrade to Pro to get full access')}}
                  </h3>
                  <a href="{{route('pricing-plan')}}" class="btn btn-info upgrade-btn">{{__('Upgrade Account')}}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif

      @if($is_admin)

      <?php
          $payment_differnce = round($payment_month-$payment_month_previous);
          $payment_differnce_abs = abs($payment_differnce);
          if($payment_month_previous==0) {
              $payment_differnce_percentage = $payment_month*100;
              $payment_differnce_percentage = round($payment_differnce_percentage).'%';
          }
          else{
              $payment_differnce_percentage = ($payment_differnce>0) ? ($payment_differnce/max($payment_month_previous,1))*100 : ($payment_differnce/max($payment_month,1))*100;
              $payment_differnce_percentage = !empty($payment_differnce_percentage) ? round($payment_differnce_percentage).'%' : '';
          }

          $user_summary = [];
          $days_in_month =  cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
          for($i=1;$i<=$days_in_month;$i++){
              $user_summary[$i] = 0;
          }
          foreach ($user_data as $key=>$value){
              $day = (int) date('d',strtotime($value->created_at));
              $user_summary[$day]++;
          }
          $user_summary_data = array_values($user_summary);
          $user_summary_label = array_keys($user_summary);
      ?>

      <div class="row">
          <div class="col-sm-12">
              <div class="statistics-details mb-0">
                  <div class="row">
                      <div class="col-sm-3 grid-margin">
                          <div class="card">
                              <div class="card-body">
                                  <div>
                                      <p class="card-title card-title-dash font-weight-medium">
                                          {{__('Users')}}
                                      </p>
                                      <h3 class="rate-percentage d-flex justify-content-between">
                                          {{$user_count}} <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-account-arrow-left me-2 icon-md"></i></span>
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
                                          {{__('Today')}}
                                      </p>
                                      <h3 class="rate-percentage d-flex justify-content-between">
                                          <?php echo $currency_icon.convert_number_numeric_phrase($payment_today); ?> <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-calendar-today-outline me-2 icon-md"></i></span>
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
                                          {{__('This Month')}}
                                      </p>
                                      <h3 class="rate-percentage d-flex justify-content-between">
                                          <?php echo $currency_icon.convert_number_numeric_phrase($payment_month); ?> <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-calendar-month-outline me-2 icon-md"></i></span>
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
                                          {{__('This Year')}}
                                      </p>
                                      <h3 class="rate-percentage d-flex justify-content-between">
                                          <?php echo $currency_icon.convert_number_numeric_phrase($payment_year); ?> <span class="text-primary text-medium d-flex align-items-center"><i class="mdi mdi-airplane-clock me-2 icon-md"></i></span>
                                      </h3>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>


      <div class="row">
        <div class="col-sm-12">
            <div class="row">
              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-start">
                          <div>
                           <h4 class="card-title card-title-dash"><?php echo __("Earning Comparison") ?></h4>
                           <h5 class="card-subtitle card-subtitle-dash">{{$year." ".__("-")." ".$lastyear}}</h5>
                          </div>
                          <div id="earning-comparision-legend"></div>
                        </div>
                        <div class="chartjs-wrapper mt-4">
                          <canvas id="earningComparision"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                    <div class="card bg-primary card-rounded">
                      <div class="card-body pb-0">
                        <h4 class="card-title card-title-dash text-white mb-4">{{__('Earning Performance')}}</h4>
                        <div class="row">
                          <div class="col-12">
                            <p class="status-summary-ight-white text-white mb-1">{{$payment_differnce>0 ? __('Increased') : __('Decreased')}}</p>
                            <h2 class="text-info">
                                <?php
                                if($payment_differnce!=0) echo $payment_differnce>0 ? '<span class="text-info">+'.$currency_icon.convert_number_numeric_phrase($payment_differnce).' ('.$payment_differnce_percentage.')</span>' : ' <span class="text-danger">-'.$currency_icon.convert_number_numeric_phrase($payment_differnce_abs).' ('.$payment_differnce_percentage.')</span>';
                                else echo '<span class="text-muted">-</span>';
                                ?>
                            </h2>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="d-flex justify-content-between align-items-center mb-2 mb-sm-0">
                              <div>
                                <p class="text-small mb-2">{{__('Last month')}}</p>
                                <h4 class="mb-0 fw-bold">{{$currency_icon.$payment_month}}</h4>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <p class="text-small mb-2">{{__('This month')}}</p>
                                <h4 class="mb-0 fw-bold">{{$currency_icon.$payment_month_previous}}</h4>
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
            <div class="row">
              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                              <div>
                                <h4 class="card-title card-title-dash">{{__("Users Gain")}}</h4>
                                <h5 class="card-subtitle card-subtitle-dash">{{date('M Y')}}</h5>
                              </div>
                            </div>
                            <div class="mt-3">
                              <canvas id="userGain"></canvas>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <div>
                            <h4 class="card-title card-title-dash">{{__('Top countries')}}</h4>
                            {{$year." ".__("-")." ".$lastyear}}
                          </div>
                        </div>
                      </div>
                      <div class="col-6">
                         <?php
                         $count=1;
                         foreach ($this_year_top as $key => $value)
                         {
                            if(strtolower($key)=='uk') $key='GB';?>
                            <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                              <div class="d-flex">
                                <img class="img-sm rounded-10" src="{{asset('assets/vendors/flag-icon-css/flags/4x3').'/'.strtolower($key)}}.svg" alt="profile">
                                <div class="wrapper ms-2">
                                  <p class="ms-1 mb-0 fw-bold"><?php echo isset($country_names[$key]) ? __($country_names[$key]) : "-"; ?></p>
                                  <small class="text-muted mb-0"><?php echo '<b>'.$currency_icon.convert_number_numeric_phrase($value).'</b>'; ?></small>
                                </div>
                              </div>
                            </div>
                         <?php
                            $count++;
                            if($count==6) break;
                         } ?>
                      </div>
                      <div class="col-6">
                        <?php
                         $count=1;
                         foreach ($last_year_top as $key => $value)
                         {
                            if(strtolower($key)=='uk') $key='GB';?>
                            <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                              <div class="d-flex">
                                <img class="img-sm rounded-10" src="{{asset('assets/vendors/flag-icon-css/flags/4x3').'/'.strtolower($key)}}.svg" alt="profile">
                                <div class="wrapper ms-2">
                                  <p class="ms-1 mb-0 fw-bold"><?php echo isset($country_names[$key]) ? __($country_names[$key]) : "-"; ?></p>
                                  <small class="text-muted mb-0"><?php echo '<b>'.$currency_icon.convert_number_numeric_phrase($value).'</b>'; ?></small>
                                </div>
                              </div>
                            </div>
                         <?php
                            $count++;
                            if($count==6) break;
                         } ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
      @endif

      <div class="card card-rounded mb-4 p-lg-2">
        <div class="card-body card-rounded">
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h4 class="card-title card-title-dash">{{__('Transaction Log')}}</h4>
                    <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__("List of all transactions")}}</p>
                </div>
                <div class="btn-wrapper mb-2">
                    <a href="{{route('transaction-log-manual')}}" class="btn btn-otline-dark border btn-sm mb-0 me-0"><i class="icon-handbag"></i> {{__('Manual Payment')}}</a>
                    <a href="javascript:;" id="date_range_picker" class="btn btn-outline-dark btn-sm mb-0 me-0"><i class="far fa-calendar"></i> {{__('Date')}}</a><input type="hidden" id="date_range_val">
                </div>
            </div>
            <div class="table-responsive">
                <table class='table table-select' id="mytable" >
                    <thead>
                    <tr class="table-light">
                        <th>#</th>
                        <th>
                            <div class="form-check form-switch pt-2"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div>
                        </th>
                        <th>{{__("ID") }}</th>
                        <th>{{__("Email") }}</th>
                        <th>{{__("First Name") }}</th>
                        <th>{{__("Last Name") }}</th>
                        <th>{{__("Method") }}</th>
                        <th>{{__("Amount") }}</th>
                        <th>{{__("Package") }}</th>
                        <th>{{__("Billing Cycle") }}</th>
                        <th>{{__("Paid at") }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
      </div>

    </div>
@endsection

<?php
if($is_admin):
    $max1 = (!empty($this_year_earning)) ? max($this_year_earning) : 0;
    $max2 = (!empty($last_year_earning)) ? max($last_year_earning) : 0;
    $steps = round(max(array($max1,$max2))/7);
    if($steps==0) $steps = 1;

    $max_user = max($user_summary_data);
    if($max_user > 10) $user_step_size = floor($max_user/10);
    else $user_step_size = 1;
endif;
?>

@push('scripts-footer')
    @include('member.payment.transaction-log-js')
@endpush

@if($is_admin)
    @push('styles-header')
        <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    @endpush
@endif
