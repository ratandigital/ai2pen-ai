<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use DateTime;
use DateTimeZone;

class Cron extends Home
{
    public function __construct()
    {
    }

    public function clean_junk_data(){
        unlink(storage_path('logs/laravel.log'));
    }

    public function get_paypal_subscriber_transaction(){
        $where = [
            ['paypal_subscriber_id', '!=',''],
            ['subscription_enabled','=','1'],
            ['paypal_next_check_time','<=',Carbon::now()->toDateTimeString()],
        ];
        $data = DB::table('users')->select('users.*','settings_payments.paypal','settings_payments.currency')->leftJoin("settings_payments","users.parent_user_id","=","settings_payments.user_id")->where($where)->orderByRaw('paypal_next_check_time asc')->limit(10)->get();
        $paypal_processing_data = [];
        foreach ($data as $user) {
            array_push($paypal_processing_data,$user->id);
        }
        DB::table('users')->whereIn('id',$paypal_processing_data)->update(['paypal_processing'=>'1']);
        foreach ($data as $user) {
            $id = $user->id;
            $paypal_credintial = $user->paypal;
            $paypal_credintial = json_decode($paypal_credintial,true);
            $paypal_client_id = $paypal_credintial['paypal_client_id'];
            $paypal_client_secret = $paypal_credintial['paypal_client_secret'];
            $currency = $user->currency;

            $paypal_app_id = $paypal_credintial['paypal_app_id'];
            $paypal_mode = $paypal_credintial['paypal_mode'];
            $paypal_subscriber_id = $user->paypal_subscriber_id;
            $expired_date = strtotime($user->expired_date);
            $provider = new PayPalClient;
            $subscription_data = json_decode($user->subscription_data,true);
            $package_id = $subscription_data['package_id'];
            if($paypal_mode == 'sandbox'){
               $config = [
                   'mode'    => 'sandbox',
                   'sandbox' => [
                       'client_id'         => $paypal_client_id,
                       'client_secret'     => $paypal_client_secret,
                       'app_id'            => $paypal_app_id,
                   ],
                   'payment_action' => 'Sale',
                   'currency'       => $currency,
                   'notify_url'     => '',
                   'locale'         => 'en_US',
                   'validate_ssl'   => true,
               ];
               $provider->setApiCredentials($config);
            }
            else{
                $config = [
                    'mode'    => 'live',
                    'live' => [
                        'client_id'         => $paypal_client_id,
                        'client_secret'     => $paypal_client_secret,
                        'app_id'            => $paypal_app_id,
                    ],
                    'payment_action' => 'Sale',
                    'currency'       => $currency,
                    'notify_url'     => '',
                    'locale'         => 'en_US',
                    'validate_ssl'   => true,
                ];
                $provider->setApiCredentials($config);
            }
            $provider->getAccessToken();
            $timestamp = time()-(365*24*60*60);
            $one_year_ago_date = gmdate("Y-m-d\TH:i:s\Z",$timestamp);

            $response = $provider->listSubscriptionTransactions($paypal_subscriber_id,$one_year_ago_date,gmdate("Y-m-d\TH:i:s\Z",time()));
            $transaction_id = $response['transactions'][0]['id'] ?? '';
            $buyer_user_id = $user->id ?? null;
            $payment_type = "PayPal";

            $check_duplicate = DB::table("transaction_logs")->select('transaction_id')->where(['buyer_user_id'=>$buyer_user_id,'transaction_id'=>$transaction_id,'payment_method'=>$payment_type])->first();
            $previous_transaction_id = $check_duplicate->transaction_id ?? '';
            if($previous_transaction_id == $transaction_id && get_domain_only(env('APP_URL'))!='aipen.test') dd("Transaction ID duplicated.");

            $subscription_time = strtotime($response['transactions'][0]['time']);
            $get_payment_validity_data = $this->get_payment_validity_data($user->id,$package_id);
            // dd($get_payment_validity_data);
            $cycle_start_date = $get_payment_validity_data['cycle_start_date'] ?? date("Y-m-d");
            $cycle_expired_date = $get_payment_validity_data['cycle_expired_date'] ?? date("Y-m-d");
            $insert_data=array(
                "verify_status"     => $response['transactions'][0]['status'] ?? '',
                "user_id"           => 1,
                "buyer_user_id"     => $buyer_user_id,
                "first_name"        => $response['transactions'][0]['payer_name']['given_name'] ?? '',
                "last_name"         => $response['transactions'][0]['payer_name']['surname'] ?? '',
                "buyer_email"       => $response['transactions'][0]['payer_email'] ?? '',
                "paid_currency"     => $response['transactions'][0]['amount_with_breakdown']['gross_amount']['currency_code'] ?? '',
                "paid_at"           => $response['transactions'][0]['time'] ?? '',
                "payment_method"    => $payment_type ?? '',
                "transaction_id"    => $transaction_id,
                "paid_amount"       => $response['transactions'][0]['amount_with_breakdown']['gross_amount']['value'] ?? '',
                "cycle_start_date"  => $cycle_start_date,
                "cycle_expired_date"=> $cycle_expired_date,
                "paypal_next_check_time"=> $cycle_expired_date,
                "package_id"        => $package_id,
                "response_source"   => json_encode($response),
                "package_name"      => $get_payment_validity_data['package_name'] ?? '',
                "user_email"        => $get_payment_validity_data['email'] ?? '', // not for insert, for sending email
                "user_name"         => $get_payment_validity_data['name'] ?? '' // not for insert, for sending email
            );
            $this->complete_payment($insert_data,$payment_type);
        }
        DB::table('users')->whereIn('id',$paypal_processing_data)->update(['paypal_processing'=>'0']);
    }

}
