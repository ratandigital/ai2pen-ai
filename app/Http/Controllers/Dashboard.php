<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use DateTime;

class Dashboard extends Home
{
    public function __construct()
    {
        $this->set_global_userdata();
    }

    public function index()
    {
        if(session('email_just_verified')=='1'){
            $settings_data = DB::table('settings')->where('user_id',$this->parent_user_id)->first();
            $auto_responder_signup_settings = $settings_data->auto_responder_signup_settings ?? '';
            if(!empty($auto_responder_signup_settings)){
                $this->sync_email_to_autoresponder(
                    $auto_responder_signup_settings,
                    $email = Auth::user()->email,
                    $first_name = Auth::user()->name,
                    $last_name = '',
                    $type='signup',
                    $this->parent_user_id
                );
            }
            session(['email_just_verified' => '0']);
        }

        $current_date = new DateTime();
        $current_week_start = $current_date->modify('this week')->format('Y-m-d');
        $current_week_end = date('Y-m-d', strtotime($current_week_start. ' + 6 day'));
        $last_week_start = $current_date->modify('last week')->format('Y-m-d');
        $last_week_end = date('Y-m-d', strtotime($last_week_start. ' + 6 day'));
        $get_seatch_content = DB::table("ai_search_contents")
            ->select("ai_template_id","tokens","searched_at","api_group")
            ->leftJoin("ai_templates","ai_templates.id","=","ai_search_contents.ai_template_id")
            ->where('user_id',$this->user_id)
            ->whereBetween('searched_at', [$last_week_start, $current_week_end])->get();
        $start_date = new DateTime($current_week_start);
        $end_date = new DateTime($current_week_end);
        $this_week_token_usage = [];
        while ($start_date <= $end_date) {
            $this_week_token_usage[$start_date->format('Y-m-d')] = 0;
            $start_date->modify('+1 day');
        }
        $start_date = new DateTime($last_week_start);
        $end_date = new DateTime($last_week_end);
        $last_week_token_usage = [];
        while ($start_date <= $end_date) {
            $last_week_token_usage[$start_date->format('Y-m-d')] = 0;
            $start_date->modify('+1 day');
        }
        $this_week_token_usage_count = $this_week_token_image_usage_count = $this_week_token_audio_usage_count = $last_week_token_usage_count = 0;
        foreach ($get_seatch_content as $key=>$value){
            $date = date('Y-m-d',strtotime($value->searched_at));

            if(!empty($value->api_group=='text')){
                if(isset($this_week_token_usage[$date])) {
                    $this_week_token_usage[$date] += $value->tokens;
                    $this_week_token_usage_count += $value->tokens;
                }
                else {
                    $last_week_token_usage[$date] += $value->tokens;
                    $last_week_token_usage_count += $value->tokens;
                }
            }
            else if($value->api_group=='image'){
                $this_week_token_image_usage_count += $value->tokens;
            }
            else if($value->api_group=='audio'){
                $this_week_token_audio_usage_count += $value->tokens;
            }
        }
        $data['this_week_token_usage'] = array_values($this_week_token_usage);
        $data['last_week_token_usage'] = array_values($last_week_token_usage);
        $data['token_usage_week_labels'] = [__('Mon'),__('Tue'),__('Wed'),__('Thu'),__('Fri'),__('Sat'),__('Sun')];
        $data['this_week_token_usage_count'] = $this_week_token_usage_count;
        $data['last_week_token_usage_count'] = $last_week_token_usage_count;
        $data['this_week_token_image_usage_count'] = $this_week_token_image_usage_count;
        $data['this_week_token_audio_usage_count'] = $this_week_token_audio_usage_count;
        $data['usage_stat'] = $this->get_usage_log_data(true,true);
        $data['body'] = 'dashboard';
        return $this->viewcontroller($data);
    }
}
