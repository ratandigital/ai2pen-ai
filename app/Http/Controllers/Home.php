<?php

namespace App\Http\Controllers;

use App\Events\ChatEventPusherWhatsapp;
use App\Events\ChatEventPusherTelegram;
use App\Mail\SimpleHtmlEmail;
use App\Models\Usage_log;
use App\Services\AutoResponder\AutoResponderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Services\TelegramServiceInterface;
use App\Services\WhatsappServiceInterface;
use App\Services\SmsManagerServiceInterface;

class Home extends BaseController
{
    public $user_id = '';
    public $user_type = '';
    public $manager_id = '';
    public $is_admin = false;
    public $is_member = false;
    public $is_manager = false; // is manger can be true despite of is_admin = true
    public $current_package = '1';
    public $is_trial = false;
    public $module_ids = [];
    public $team_access = null;
    public $monthly_limit = [];
    public $user_limit = '-1';
    public $parent_user_id = '1';
    public $parent_package_id = null;
    public $parent_parent_user_id = null;
    public $expired_date = null;
    public $availatble_autoresponder_names = ['mailchimp','sendinblue','activecampaign','mautic'];
    public $module_id_token = 1;
    public $module_id_image = 2;
    public $module_id_audio = 3;
    public $module_id_template_manager = 20;
    public $module_id_settings = 21;
    public $module_id_team_member = 22;
    public $app_product_id=69;
    public $module_id_affiliate_system = 12;
    public $ai_livechat =15;

    protected function set_global_userdata($check_validity=false,$allowed_role=[], $denied_role=[] ,$module_id=null)
    {
        set_time_limit(0);
        $this->middleware('auth');
        $this->middleware(function ($request, $next) use ($check_validity,$allowed_role,$denied_role,$module_id) {
            if(Auth::user())
            {
                if(Auth::user()->status=='0') {
                    header('Location:'.route('logout'));
                    die;
                }
                $this->set_auth_variables();

                if(!empty($denied_role)){
                    $deny_access = false;
                    if(in_array('Admin',$denied_role) && $this->is_admin) $deny_access  = true;
                    if(in_array('Member',$denied_role) && $this->is_member) $deny_access  = true;
                    if(in_array('Manager',$denied_role) && $this->is_manager) $deny_access  = true;
                    if($deny_access) abort('403');
                }

                if(!empty($allowed_role)){
                    $allow_access = false;
                    if(in_array('Admin',$allowed_role) && $this->is_admin) $allow_access  = true;
                    if(in_array('Member',$allowed_role) && $this->is_member) $allow_access  = true;
                    if(in_array('Manager',$allowed_role) && $this->is_manager) $allow_access  = true;
                    if(!$allow_access) abort('403');
                }
                if($check_validity) {
                    $this->check_member_validity();
                    $is_passed = $this->important_feature(false);
                    if(!$is_passed) {
                        if($this->is_admin) return redirect()->route('credential-check');
                        else abort(403);
                    }
                }
                if(!$this->is_admin && !empty($module_id)){
                    if(!is_array($module_id) && !in_array($module_id,$this->module_ids)) abort('403');
                    else if(is_array($module_id) && count(array_intersect($this->module_ids,$module_id))==0) abort('403');
                }
                return $next($request);
            }
        });
    }

    protected function set_auth_variables(){
        if(!Auth::user()) return set_agency_config();

        $parent_user_id = Auth::user()->parent_user_id;
        $this->parent_user_id = $parent_user_id;
        $this->parent_parent_user_id = $parent_parent_user_id = $this->parent_package_id = $parent_package_id = null;

        $manager_parent_data = $parent_user_id>0 ? DB::table('users')->where('id',$parent_user_id)->first() : null;
        $parent_parent_user_id = $manager_parent_data->parent_user_id ?? null;
        $parent_package_id = $manager_parent_data->package_id ?? null;
        $parent_expired_date = $manager_parent_data->expired_date ?? null;
        $parent_module_ids = [];
        if(!empty($parent_package_id)){
            $parent_package_data = $this->get_package($parent_package_id);
            $parent_module_ids = isset($parent_package_data->module_ids) ? explode(',',$parent_package_data->module_ids) : [];
        }

        if(Auth::user()->user_type=="Manager"){
            Auth::user()->user_type = $manager_parent_data->user_type ?? '';
            Auth::user()->manager_id = Auth::user()->id;
            Auth::user()->id = $manager_parent_data->id ?? '';
            Auth::user()->expired_date = Auth::user()->user_type!='Admin' ? $parent_expired_date : null;
            Auth::user()->parent_parent_user_id = $this->parent_parent_user_id = $parent_parent_user_id;
            Auth::user()->parent_package_id = $this->parent_package_id = $parent_package_id;
            $this->is_manager = true;
            $this->manager_id = Auth::user()->manager_id;
        }

        $user_id = Auth::user()->id;
        $this->user_id = $user_id;
        session(['auth_user_id' => $this->user_id]);

        $user_type = Auth::user()->user_type;

        if($user_type=='Admin') $this->is_admin = true;
        else $this->is_member = true;

        set_agency_config();

        $this->user_type = $user_type;
        $this->current_package = Auth::user()->package_id;
        if($this->is_member || $this->is_manager){
            $package_data = $this->get_package($this->current_package);
            $module_ids = isset($package_data->module_ids) ? explode(',',$package_data->module_ids) : [];
            if($parent_user_id>1){
                // if parent user doesnt have a module we are revoking that module from child
                foreach ($module_ids as $kmod=>$mod){
                    if(!in_array($mod,$parent_module_ids)) unset($module_ids[$kmod]);
                }
            }
            $this->is_trial = isset($package_data->is_default) ? (bool) $package_data->is_default : false;
            $team_access = isset($package_data->team_access) && !empty($package_data->team_access) ? json_decode($package_data->team_access,true) : null;
            $monthly_limit = isset($package_data->monthly_limit) && !empty($package_data->monthly_limit) ? json_decode($package_data->monthly_limit,true) : [];
            Auth::user()->module_ids = $this->module_ids = $module_ids;
            if(!empty($team_access)) Auth::user()->team_access = $this->team_access = $team_access;
            if(!empty($monthly_limit)) Auth::user()->monthly_limit = $this->monthly_limit = $monthly_limit;
            Auth::user()->user_limit = $this->user_limit = $package_data->user_limit ?? '-1';
        }
        $this->expired_date = Auth::user()->expired_date;
        return true;
    }

    public function set_module_ids($data=[]){
        $data['module_id_token'] = $this->module_id_token;
        $data['module_id_image'] = $this->module_id_image;
        $data['module_id_audio'] = $this->module_id_audio;
        $data['module_id_team_member'] = $this->module_id_team_member;
        $data['module_id_settings'] = $this->module_id_settings;
        $data['module_id_template_manager'] = $this->module_id_template_manager;
        $data['module_id_affiliate_system'] = $this->module_id_affiliate_system;
        $data['ai_livechat'] = $this->ai_livechat;
        return $data;
    }

    protected function viewcontroller($data=array())
    {
        $data = $this->set_module_ids($data);
        if (!isset($data['body'])) return false;
        if (!isset($data['iframe'])) $data['iframe'] = false;
        if (!isset($data['load_datatable'])) $data['load_datatable'] = false;
        if (!isset($data['load_footer'])) $data['load_footer'] = true;
        $data['user_id'] = $this->user_id;
        $data['parent_parent_user_id'] = $this->parent_parent_user_id;
        $data['parent_package_id'] = $this->parent_package_id;
        $data['parent_user_id'] = $this->parent_user_id;
        $data['expired_date'] = $this->expired_date;
        $data['is_admin'] = $this->is_admin;
        $data['is_trial'] = $this->is_trial;
        $data['is_member'] = $this->is_member;
        $data['is_manager'] = $this->is_manager;
        $data['user_module_ids'] = $this->module_ids;
        $data['team_access'] = $this->team_access;
        $data['monthly_limit'] = $this->monthly_limit;

        $data['notifications'] = $this->get_notifications();
        $data['route_name'] = Route::currentRouteName();
        $data['get_selected_sidebar'] = get_selected_sidebar($data['route_name']);
        $data['full_width_page_routes'] = full_width_page_routes();
        $data['load_box_layout'] = false;
        $template_sidebar = DB::table("ai_templates")->select('ai_templates.*','group_name','group_slug','icon_class')
            ->leftJoin('ai_template_groups','ai_template_groups.id','=','ai_templates.ai_template_group_id')
            ->where(['ai_template_groups.status'=>'1','ai_templates.status'=>'1'])
            ->orderByRaw('ai_template_groups.serial asc')->get();
        $ai_sidebar_group_by_id = [];
        $template_group_icon_list = [];
        $template_list = [];
        foreach($template_sidebar as $tk=>$tv){
            $api_group = $tv->api_group;
            $has_access = true;
            if($api_group=='image' && !has_module_access($this->module_id_image,$this->module_ids,$this->is_admin,$this->is_manager)) {
                $has_access=false;
            }
            if($api_group=='audio' && !has_module_access($this->module_id_audio,$this->module_ids,$this->is_admin,$this->is_manager)) {
                $has_access=false;
            }
            if(($api_group=='text' || $api_group=='chat') && !has_module_access($this->module_id_token,$this->module_ids,$this->is_admin,$this->is_manager)) {
                $has_access=false;
            }
            $tv->has_access = $has_access;
            $ai_sidebar_group_by_id[$tv->ai_template_group_id][] = (array) $tv;
        }
        $template_group_data = DB::table("ai_template_groups")->where(['status'=>'1'])->orderByRaw('serial asc')->get();
        foreach($template_group_data as $tk=>$tv){
            $template_group_icon_list[$tv->id] = $tv->icon_class;
            $template_list[$tv->id] = __($tv->group_name);
        }
        $data['template_list']=$template_list;
        $data['template_group_icon_list']=$template_group_icon_list;
        $data['ai_sidebar_group_by_id']=$ai_sidebar_group_by_id;
        $data['openai_endpoint_list'] = get_openai_endpoint_list();
        return view($data['body'], $data);
    }

    protected function get_landing_language($user_id=1){
        $get_data = DB::table('settings')->where('user_id',$user_id)->select(['agency_landing_settings','analytics_code'])->first();
        if(!isset($get_data->agency_landing_settings) || empty($get_data->agency_landing_settings)){
            $get_data = $get_data = DB::table('settings')->where('user_id',1)->select(['agency_landing_settings','analytics_code'])->first();
        }
        return $get_data;
    }

    protected function set_meta_data($get_landing_language=null){
        $this->metadata = [
            'meta_title' => $get_landing_language->company_title ?? 'Best AI Writer Tools',
            'meta_description' => $get_landing_language->company_short_description ?? 'Produce exceptional content 10 times faster with our platform. From copywriting and blog articles to SEO content and product descriptions, our tool ensures plagiarism-free and SEO-optimized work. Create emails, ads, and many more with ease.',
            'meta_image' =>  $get_landing_language->company_cover_image ?? asset('assets/images/meta.jpg'),
            'meta_keyword' => $get_landing_language->company_keywords ?? 'Copy Writing,Blog Articles,Content Rephrase,SEO Content,Product Description,Sales Content,Text Summary,Email Message,Speech to Text,Youtube Description,Ads Content',
            'meta_author' => config('app.name')
        ];
    }

    public function make_view_data(){
        $user_id = 1;
        $this->set_auth_variables();
        $get_landing_language = $this->get_landing_language($user_id);
        $get_agency_landing = isset($get_landing_language->agency_landing_settings) ? json_decode($get_landing_language->agency_landing_settings) : [];
        $get_analytics_code = isset($get_landing_language->analytics_code) ? json_decode($get_landing_language->analytics_code,true) : [];
        $this->set_meta_data($get_agency_landing);
        $data = $this->metadata;
        $data['get_landing_language'] = $get_agency_landing;
        $data['get_analytics_code'] = $get_analytics_code;
        $data['disable_landing_page'] = $get_agency_landing->disable_landing_page ?? '0';
        $data['disable_ecommerce_feature'] = $get_agency_landing->disable_ecommerce_feature ?? '0';
        $data['disable_review_section'] = $get_agency_landing->disable_review_section ?? '0';
        $data['enable_dark_mode'] = $get_agency_landing->enable_dark_mode ?? '0';
        return $data;
    }

    protected function site_viewcontroller($data=array())
    {
        if (!isset($data['body'])) return false;
        $user_id = $parent_user_id = $user_type = "";
        $expired_date = null;
        $is_admin = $is_member = $is_manager = false;
        $enable_blog_comment = '0';
        if( Auth::user()){
            $user_type =  Auth::user()->user_type;
            $user_id =  Auth::user()->id;
            $parent_user_id =  Auth::user()->parent_user_id;
            $expired_date =  Auth::user()->expired_date;
            $enable_blog_comment =  Auth::user()->enable_blog_comment;
            $is_admin =  $user_type=="Admin";
            $is_member =  $user_type=="Member";
            $is_manager =  $user_type=="Manager";
        }
        $data['user_id'] = $user_id;
        $data['user_type'] = $user_type;
        $data['parent_user_id'] = $parent_user_id;
        $data['expired_date'] = $expired_date;
        $data['is_admin'] = $is_admin;
        $data['is_member'] = $is_member;
        $data['is_manager'] = $is_manager;
        $data['enable_blog_comment'] = $enable_blog_comment;

        $data['is_agency_site'] = false;


        $meta_image_width = $meta_image_height = '';
        if(!empty($data['meta_image'])){
            if (!filter_var($data['meta_image'], FILTER_VALIDATE_URL))
                $data['meta_image'] = $data['meta_image'];
            $meta_image_data = @getimagesize($data['meta_image']);
            $meta_image_width = $meta_image_data[0] ?? '';
            $meta_image_height = $meta_image_data[1] ?? '';
        }
        $data['meta_image_width'] = $meta_image_width;
        $data['meta_image_height'] = $meta_image_height;
        return view($data['body'], $data);
    }

    protected function docs_viewcontroller($data=array())
    {
        if (!isset($data['body'])) return false;

        $agent_user_id = 1;
        set_agency_config();

        $user_id = $parent_user_id = $user_type = "";
        $expired_date = null;
        $is_admin = $is_member = $is_manager = false;
        $enable_blog_comment = '0';
        if( Auth::user()){
            $user_type =  Auth::user()->user_type;
            $user_id =  Auth::user()->id;
            $parent_user_id =  Auth::user()->parent_user_id;
            $expired_date =  Auth::user()->expired_date;
            $enable_blog_comment =  Auth::user()->enable_blog_comment;
            $is_admin =  $user_type=="Admin";
            $is_member =  $user_type=="Member";
            $is_manager =  $user_type=="Manager";
        }
        $data['user_id'] = $user_id;
        $data['user_type'] = $user_type;
        $data['parent_user_id'] = $parent_user_id;
        $data['expired_date'] = $expired_date;
        $data['is_admin'] = $is_admin;
        $data['is_member'] = $is_member;
        $data['is_manager'] = $is_manager;
        $data['enable_blog_comment'] = $enable_blog_comment;
        $data['agent_user_id'] = $agent_user_id;
        $data['is_agency_site'] = false;
        return view($data['body'], $data);
    }

    protected function bare_viewcontroller($data=array())
    {
        $data = $this->set_module_ids($data);
        if (!isset($data['body'])) return false;
        if (!isset($data['load_datatable'])) $data['load_datatable'] = false;

        $auth_check = Auth::check() ? true :false;
        $data['user_id'] = $auth_check ? $this->user_id : 0;
        $data['parent_user_id'] = $auth_check ? $this->parent_user_id : null;
        $data['expired_date'] = $auth_check ? $this->expired_date : null;
        $data['is_admin'] = $auth_check ? $this->is_admin : false;
        $data['is_member'] = $auth_check ? $this->is_member : false;
        $data['user_module_ids'] =  $auth_check ? $this->module_ids : [];

        $data['route_name'] = Route::currentRouteName();
        $data['get_selected_sidebar'] = '';
        return view($data['body'], $data);
    }

    protected function insert_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {
        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->user_id;
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");

        $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);

        // insert new entry if not exit, increment usage_count otherwise
        $usage_log = Usage_log::firstOrNew($where);
        $usage_log->usage_count = ($usage_log->usage_count + $usage_count);
        $usage_log->save();

        return true;
    }

    protected function delete_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {
        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->user_id;
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");

        $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);

        // insert new entry if not exit, decrement usage_count otherwise
        $usage_log = Usage_log::firstOrNew($where);
        if($usage_log) $usage_log->usage_count = ($usage_log->usage_count - $usage_count);
        else $usage_log->usage_count = 0;
        $usage_log->save();

        return true;
    }

    protected function check_usage($module_id=0,$request=0,$user_id=0)
    {
        if($this->is_admin) return '1';

        if($module_id==0 || $request==0) return "0";
        if($user_id==0) $user_id=$this->user_id;
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");

        $module = DB::table('modules')->select('extra_text')->where('id',$module_id)->first();
        $extra_text = $module->extra_text;

        if($extra_text=="") $where = [
            ['module_id', '=', $module_id],
            ['user_id', '=', $user_id]
        ];
        else $where = [
            ['module_id', '=', $module_id],
            ['user_id', '=', $user_id],
            ['usage_month', '=', $usage_month],
            ['usage_year', '=', $usage_year]
        ];

        $usage_count = Usage_log::where($where)->sum('usage_count');

        $monthly_limit=array();
        $bulk_limit=array();
        $module_ids=array();

        $package_id = $this->is_manager ? $this->parent_package_id : $this->current_package;
        $package_info = DB::table('packages')->where('id',$package_id)->first();

        if(isset($package_info->bulk_limit))    $bulk_limit=json_decode($package_info->bulk_limit,true);
        if(isset($package_info->monthly_limit)) $monthly_limit=json_decode($package_info->monthly_limit,true);
        if(isset($package_info->module_ids))    $module_ids=explode(',', $package_info->module_ids);

        $return = "0";
        if(in_array($module_id, $module_ids) && $bulk_limit[$module_id] > 0 && $bulk_limit[$module_id]<$request)
            $return = "2"; // bulk limit crossed | 0 means unlimited
        else if(in_array($module_id, $module_ids) && $monthly_limit[$module_id] > 0 && $monthly_limit[$module_id]<($request+$usage_count))
            $return = "3"; // montly limit crossed | 0 means unlimited
        else  $return = "1"; //success

        return $return;
    }

    protected function print_limit_message($module_id=0,$request=0)
    {
        $status=$this->check_usage($module_id,$request);
        if($status=="2") {
            Session::flash('module_limit_exceed_message', __("Sorry, bulk action limit has been exceeded for this module."));
            return false;
        }
        else if($status=="3") {
            Session::flash('module_limit_exceed_message', __("Sorry, usage limit has been exceeded for this module."));
            return false;
        }
        return true;
    }

    protected function check_member_validity()
    {
        $pricing_link = $this->parent_user_id==1 ? env('APP_URL').'/pricing' : route('pricing-plan');
        if(!$this->is_admin)
        {
            $expire_date = $this->expired_date;
            if($expire_date=='0000-00-00 00:00:00' || $expire_date==null) return true;
            $expire_date = strtotime($expire_date);
            $current_date = strtotime(date("Y-m-d"));
            $package_id = $this->is_manager ? $this->parent_package_id : $this->current_package;
            $package_info = DB::table('packages')->where('id',$package_id)->first();
            $price = isset($package_info->price) ? $package_info->price : 0;
            if($price=="Trial") $price=1;
            if ($expire_date < $current_date && ($price>0 && $price!="")) {
                header('Location:'.$pricing_link);
                die;
            }
        }
        if($this->parent_user_id > 1)
        {
            $parent_user_data = DB::table('users')->select('expired_date','parent_user_id')->where(['id'=>$this->parent_user_id,'status'=>'1','deleted'=>'0'])->first();
            if(!isset($parent_user_data)) abort('403');
            $expire_date = isset($parent_user_data->expired_date) ? $parent_user_data->expired_date : null;
            if($expire_date=='0000-00-00 00:00:00' || $expire_date==null) return true;
            $expire_date = strtotime($expire_date);
            $current_date = strtotime(date("Y-m-d"));
            if ($expire_date < $current_date) abort('403');
        }
        return true;

    }

    public function get_notifications($user_id=0)
    {
        $last_days = date('Y-m-d', strtotime('-15 days', strtotime(date("Y-m-d"))));
        $last_days = $last_days." 00:00:00";
        if($user_id==0) $user_id = $this->user_id;
        $where = "((user_id={$user_id} AND is_seen='0') OR (user_id=0 AND NOT FIND_IN_SET('".$user_id."', seen_by))) AND created_at>='".$last_days."'";
        $notifications =  DB::table('notifications')->whereRaw($where)->orderByRaw('created_at DESC')->get();
        return $notifications;
    }

    protected function get_payment_config($select='*')
    {
        return DB::table('settings_payments')->select($select)->first();
    }

    protected function get_payment_config_parent($parent_user_id=0,$select='*')
    {
        if($parent_user_id == 0) $parent_user_id = Auth::user()->parent_user_id;
        return DB::table('settings_payments')->select($select)->where(['user_id'=>$parent_user_id,'users.status'=>'1','users.deleted'=>'0'])->leftJoin('users', 'users.id', '=', 'settings_payments.user_id')->first();
    }

    protected function get_payment_status()
    {
        return array('pending'=>__('Pending'),'approved'=>__('Approved'),'rejected'=>__('Rejected'),'shipped'=>__('Shipped'),'delivered'=>__('Delivered'),'completed'=>__('Completed'));
    }

    protected function get_user($id=0,$select='*')
    {
        if($id==0) return null;
        $user_data = DB::table("users")->select($select)->where(['id' => $id])->first();
        return $user_data;
    }

    protected function get_modules($team_package=false)
    {
        $query = DB::table('modules')->where('status','1');
        if($team_package) $query->where('team_module','1');
        else $query->where('subscription_module','1');

        if(!$this->is_admin || !$team_package) $query->where('admin_module','0');

        return $query->orderBy('sl','asc')->get();
    }

    protected function get_package($id=0,$select='*',$where='')
    {
        if($id==0) $id = $this->current_package;
        if(empty($where)) $where = ['id'=>$id];
        return DB::table('packages')->select($select)->where($where)->first();
    }

    protected function get_packages($select='*',$team_package=false)
    {
        $query =  DB::table('packages')->select($select)->where(['deleted'=>'0']);
        if($team_package) $query->where('package_type','team');
        else $query->where('package_type','subscription');
        return $query->orderBy('package_name','asc')->get();
    }

    protected function get_packages_all($select='*')
    {
        $query =  DB::table('packages')->select($select)->where(['deleted'=>'0']);
        return $query->orderByRaw('package_type asc,package_name asc')->get();
    }

    protected function get_packages_parent($select='*',$team_package=false)
    {
        $query = DB::table('packages')->select($select)
            ->where(["is_default"=>"0","visible"=>"1","deleted"=>"0"]);
        if($team_package) {
            $query->where('package_type','team');
        }
        else {
            $query->where('price','>',0)->where('validity','>',0)->where('package_type','subscription');
        }
        return $query->orderByRaw('CAST(price AS SIGNED)')->get();
    }

    protected function get_templates(){
        $templates = DB::table("ai_template_groups")->where(['status'=>'1'])->orderByRaw('serial asc')->get();
        $template_list = [''=>__('Select')];
        foreach ($templates as $key=>$value){
            $template_list[$value->id] = $value->group_name;
        }
        return $template_list;
    }

    protected function get_validity_types()
    {
        return array('D' => __('Days'), 'W' => __('Weeks'), 'M' => __('Months'), 'Y' => __('Years'));
    }

    protected function get_payment_formatting_data(){
        $user_id = 1;
        $payment_config = DB::table('settings_payments')
            ->where('user_id',$user_id)
            ->select(['currency','decimal_point','thousand_comma','currency_position'])->first();
        return $format_settings = ['currency'=>$payment_config->currency ?? 'USD','decimal_point'=>$payment_config->decimal_point ?? null,'thousand_comma'=>$payment_config->thousand_comma ?? '0','currency_position'=>$payment_config->currency_position ?? 'left'];
    }

    protected function get_email_template($template_type='')
    {
        return null;
    }

    public function get_sms_email_profiles($api_type='email',$assoc=true,$user_id=0,$select='*')
    {
        if($user_id==0) $user_id = $this->user_id;
        $info_type = DB::table('settings_sms_emails')->where('user_id','=',$user_id)->where('api_type','=',$api_type)->where('status','=','1')->orderByRaw('api_name ASC')->get();
        if(!$assoc) return $info_type;

        $return = [];
        foreach ($info_type as  $value)
        {
            $return[$value->id] = $value->api_name.' : '.$value->profile_name;
        }
        return $return;
    }

    public function get_enum_values($table,$column){
        $enum = DB::select(DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$column.'"'));
        $return = [];
        if(!empty($enum)){
            $values_str = $enum[0]->Type ?? '';
            $values_str = ltrim($values_str,'enum(');
            $values_str = rtrim($values_str,')');
            $values = explode(',',$values_str);
            $return = array_map(function ($item) {
                return trim($item,"'");
            }, $values);
        }
        return $return;

    }

    protected function get_payment_validity_data($buyer_user_id=0,$package_id=0)
    {
        $package_data = $this->get_package($package_id,['package_name','price','validity','discount_data','product_data']);
        $package_name = $package_data->package_name ?? '';
        $discount_data = $package_data->discount_data ?? null;
        $product_data = $package_data->product_data ?? null;
        $price = $package_data->price ?? 0;
        $validity = $package_data->validity ?? 0;
        $validity_str='+'.$validity.' day';

        $prev_payment_info = DB::table('transaction_logs')->select('cycle_start_date','cycle_expired_date')
            ->where(['buyer_user_id'=>$buyer_user_id])->whereNotNull('package_id')
            ->orderByRaw('id DESC')->first();
        $prev_cycle_expired_date = $prev_payment_info->cycle_expired_date ?? '';
        $cycle_start_date = $cycle_expired_date = date('Y-m-d');
        if(empty($prev_cycle_expired_date)) $cycle_expired_date = date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
        else if (strtotime($prev_cycle_expired_date) <= strtotime(date('Y-m-d'))) $cycle_expired_date = date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
        else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d')))
        {
            $cycle_start_date = date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
            $cycle_expired_date = date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
        }

        $user_data = DB::table("users")->where(['id'=>$buyer_user_id])->select('parent_user_id','email','name')->first();
        $parent_user_id = $user_data->parent_user_id ?? 0;
        $email = $user_data->email ?? '';
        $name = $user_data->name ?? '';

        return ['parent_user_id'=>$parent_user_id,'email'=>$email,'name'=>$name,'package_name'=>$package_name,'price'=>$price,'cycle_start_date'=>$cycle_start_date,'cycle_expired_date'=>$cycle_expired_date,'validity'=>$validity,'discount_data'=>$discount_data,'product_data'=>$product_data];
    }


    protected function complete_payment($insert_data=[],$payment_type='')
    {
        $curtime = date("Y-m-d H:i:s");
        $last_payment_method = $payment_type;
        $user_email = $insert_data['user_email'] ?? '';
        $user_name = $insert_data['user_name'] ?? '';
        $package_name = $insert_data['package_name'] ?? '';
        $package_id = $insert_data['package_id'] ?? null;
        $paid_currency = $insert_data['paid_currency'] ?? "USD";
        $paid_amount = $insert_data['paid_amount'] ?? 0;
        $buyer_user_id = $insert_data['buyer_user_id'] ?? 0;
        $parent_user_id = $insert_data['user_id'] ?? 0;
        $cycle_expired_date = $insert_data['cycle_expired_date'] ?? null;
        $paypal_next_check_time = $insert_data['paypal_next_check_time'] ?? null;
        $update_data = array
        (
            "updated_at"=>$curtime,
            "purchase_date"=>$curtime,
            "last_payment_method"=>$last_payment_method
        );
        if(!empty($paypal_next_check_time)) $update_data['paypal_next_check_time'] = $paypal_next_check_time;
        if(!empty($cycle_expired_date)) $update_data['expired_date'] = $cycle_expired_date;
        if(!empty($package_id)) $update_data['package_id'] = $package_id;
        $update_data['user_type'] =  'Member';
        if(!empty($is_whitelabel)) $update_data['agent_has_whitelabel'] = $is_whitelabel;

        $error = false;
        try {
            DB::beginTransaction();
            unset($insert_data['user_email']);
            unset($insert_data['user_name']);
            if(isset($insert_data['paypal_next_check_time'])) unset($insert_data['paypal_next_check_time']);
            DB::table('transaction_logs')->insert($insert_data);
            DB::table('users')->where(['id'=>$buyer_user_id])->update($update_data);

            $insert_data = [
                'title'=> __('Payment Confirmation'),
                'description'=> __('We have received your payment of')." {$paid_currency} {$paid_amount}",
                'created_at' => date("Y-m-d H:i:s"),
                'user_id' => $buyer_user_id,
                'color_class' => 'bg-success',
                'icon' => 'fas fa-shopping-bag',
                'published' => '1',
                'linkable' => '1',
                'custom_link' => route('transaction-log')
            ];
            DB::table("notifications")->insert($insert_data);
            $insert_data['title'] = __('New Payment Received');
            $insert_data['description'] =  __('You have received a payment of')." {$paid_currency} {$paid_amount}";
            $insert_data['user_id'] =  $parent_user_id;
            $insert_data['icon'] =  'fas fa-dollar-sign';
            DB::table("notifications")->insert($insert_data);

            DB::commit();
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error = true;
            $error_message = $e->getMessage();
        }

        if($error) dd($error_message);
        else
        {
            $user_info = DB::table('users')->where(['id'=>$buyer_user_id])->select('under_which_affiliate_user')->first();
            if($user_info->under_which_affiliate_user != 0)
                $this->affiliate_commission($user_info->under_which_affiliate_user,$buyer_user_id,$event='payment',$paid_amount);
            $param_subject = __('Payment Confirmation');
            $param_name = 'Hello'.' '.$user_name;
            $param_message = __("Congratulation, We have received your payment of")." {$paid_currency} {$paid_amount} ({$package_name}) ".__("New billing cycle will continue until")." {$cycle_expired_date}.";
            if(!empty($user_email)) Mail::to($user_email)->send(new SimpleHtmlEmail($param_name,$param_message,$param_subject));

            $parent_userdata = DB::table('users')->select('email','name')->where(['id'=>$parent_user_id])->first();
            $param_subject = __('New Payment Received');
            $admin_email = $parent_userdata->email ?? '';
            $admin_name = $parent_userdata->name ?? '';
            $param_name = 'Hello'.' '.$admin_name;
            $param_message = __("Congratulation, You have received a new payment of")." {$paid_currency} {$paid_amount} ({$package_name}).".__("The payment was sent by")." : {$user_name}.";
            if(!empty($admin_email)) Mail::to($admin_email)->send(new SimpleHtmlEmail($param_name,$param_message,$param_subject));
        }
        return true;
    }

    public function set_api_error($code='',$message='',$return_json=false)
    {
        if($code=='' && $message=='') return false;
        $error_message = 'Error '.$code.' : '.$message;

        if($return_json) return json_encode(['error' => true,'message' => $error_message]);
        session()->flash('api_error_message',$error_message);
        return true;
    }

    public function get_thirdparty_api_profile_dropdown(Request $request)
    {
        $user_id = $request->user_id;
        $icon = $request->icon;
        $field_name = $request->field_name ?? 'default_ai_api';
        $field_id = $request->field_id ?? '';
        if(empty($user_id)) $user_id = Auth::user()->id ?? 0;
        if(empty($icon)) $icon = false;

        $settings = DB::table('settings')->where('user_id',$user_id)->first();
        $thirdpary_api_settings = $settings->thirdpary_api_settings ?? '';
        $thirdpary_api_settings = json_decode($thirdpary_api_settings);
        $default = $thirdpary_api_settings->a->default ?? '';

        $info_type = DB::table('settings_thirdparty_apis')->where('user_id','=',$user_id)->where('api_type','=','ai')->orderByRaw('api_name ASC')->get();
        $response = $icon ? '<span class="input-group-text"><i class="fas fa-robot"></i></span>' : '';
        $response .= "<select name='".$field_name."'  id='".$field_id."' class='form-control form-control-lg'>";
        $response .= '<option value="#RANDOM#">'.__("Use API Randomly").'</option>';
        foreach ($info_type as  $value)
        {
            $selected = $default==$value->id ? 'selected' : '';
            $response .= "<option value='{$value->id}' ".$selected.">".$value->api_name." : ".$value->profile_name."</option>";
        }
        $response .= "</select>";
        echo $response;
    }

    public function get_email_profile_dropdown(Request $request) // common function both
    {
        $user_id = $request->user_id;
        $icon = $request->icon;
        $field_name = $request->field_name ?? 'default_email';
        $field_id = $request->field_id ?? '';
        if(empty($user_id)) $user_id = Auth::user()->id ?? 0;
        if(empty($icon)) $icon = false;

        $settings = DB::table('settings')->where('user_id',$user_id)->first();
        $email_settings = $settings->email_settings ?? '';
        $email_settings = json_decode($email_settings);
        $default = $email_settings->default ?? '';

        $info_type = DB::table('settings_sms_emails')->where('user_id','=',$user_id)->where('api_type','=','email')->orderByRaw('api_name ASC')->get();

        $response = $icon ? '<span class="input-group-text"><i class="far fa-envelope-open"></i></span>' : '';
        $response .= "<select name='".$field_name."'  id='".$field_id."' class='form-control form-control-lg'>";
        $response .= "<option value=''>".__('System')."</option>";
        foreach ($info_type as  $value)
        {
            $selected = $default==$value->id ? 'selected' : '';
            $response .= "<option value='{$value->id}' ".$selected.">".$value->api_name." : ".$value->profile_name."</option>";
        }
        $response .= "</select>";
        echo $response;
    }

    public function get_sms_profile_dropdown(Request $request) // common function both
    {
        $user_id = $request->user_id;
        $icon = $request->icon;
        $field_name = $request->field_name ?? 'default_sms';
        $field_id = $request->field_id ?? '';
        if(empty($user_id)) $user_id = Auth::user()->id ?? 0;
        if(empty($icon)) $icon = false;

        $settings = DB::table('settings')->where('user_id',$user_id)->first();
        $sms_settings = $settings->sms_settings ?? '';
        $sms_settings = json_decode($sms_settings);
        $default = $sms_settings->default ?? '';

        $info_type = DB::table('settings_sms_emails')->where('user_id','=',$user_id)->where('api_type','=','sms')->orderByRaw('api_name ASC')->get();

        $response = $icon ? '<span class="input-group-text"><i class="fas fa-phone"></i></span>' : '';
        $response .= "<select name='".$field_name."' id='".$field_id."' class='form-control form-control-lg'>";
        $response .= "<option value=''>".__('Select')."</option>";
        foreach ($info_type as  $value)
        {
            $selected = $default==$value->id ? 'selected' : '';
            $response .= "<option value='{$value->id}' ".$selected.">".$value->api_name." : ".$value->profile_name."</option>";
        }
        $response .= "</select>";
        echo $response;
    }

    public function _random_number_generator($length=6)
    {
        $rand = substr(uniqid(mt_rand(), true), 0, $length);
        return $rand;
    }

    protected  function send_email_using_api_id($email_api_id='', $email='', $email_reply_message='', $email_reply_subject='', $user_id='', $email_reply_message_header='')
    {
        if(empty($user_id)) $user_id = $this->user_id;
        if(empty($email) || empty($email_reply_message) || empty($email_reply_subject) ) return ['error'=>true,'message'=>__('Missing params.')];
        if(set_email_config($email_api_id))
        {
            $response = $this->send_email($email,$email_reply_message,$email_reply_subject,$email_reply_message_header);
            $status = $response['status'] ?? 'Unknown';
            $now_time=date('Y-m-d H:i:s');
            $insert_data=array('user_id'=>$user_id,'settings_type'=>'quick-reply','status'=>$status,'email'=>$email,'api_type'=>"Email Sender",'api_name'=>config('mail.default'),'response'=>json_encode($response),'updated_at'=>$now_time,'email_api_id'=>$email_api_id);
            DB::table("sms_email_send_logs")->insert($insert_data);
            return $response;
        }
        else return ['error'=>true,'message'=>__('Email settings not found.')];
    }

    protected  function send_email($email='', $email_reply_message='', $email_reply_subject='', $email_reply_message_header='')
    {
        if(empty($email) || empty($email_reply_message) || empty($email_reply_subject) ) return ['error'=>true,'message'=>__('Missing params.')];
        try
        {
            Mail::to($email)->send(new SimpleHtmlEmail($email_reply_message_header,$email_reply_message,$email_reply_subject));
            return ['error'=>false,'message'=>__('Email sent successfully.')];
        }
        catch(\Swift_TransportException $e){
            return ['error'=>true,'message'=>$e->getMessage()];
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            return ['error'=>true,'message'=>$e->getMessage()];
        }
        catch(Exception $e) {
            return ['error'=>true,'message'=>$e->getMessage()];
        }

    }

    protected  function send_sms_using_api_id($sms_api_id='', $phone_number='', $sms_reply_message='', $user_id='')
    {
        $error_response = ['ok'=>false,'description'=>__('API settings not found.'),'error_code'=>''];
        if($user_id=='') $user_id = $this->user_id;
        $where = ['id'=>$sms_api_id,'user_id'=>$user_id];
        $api_data = DB::table('settings_sms_emails')->where($where)->first();
        if(empty($api_data)) return $error_response;

        $settings_data = isset($api_data->settings_data) ? json_decode($api_data->settings_data,true) : [];
        $api_name = $api_data->api_name ?? '';
        if(empty($settings_data) || empty($api_name)) return $error_response;

        $sms_manager = app(SmsManagerServiceInterface::class);

        $sms_manager->api_name = $api_name;
        foreach ($settings_data as $key=>$value){
            if(empty($key) || empty($value)) continue;
            $index = $api_name.'_'.$key;
            $sms_manager->$index = $value;
        }
        return $sms_manager->send_sms($sms_reply_message, $phone_number);
    }

    public function get_autoresponder_list(){
        $autoresponder_info = DB::table('settings_email_autoresponders')
            ->select('settings_email_autoresponder_lists.*', 'profile_name', 'api_name')
            ->leftJoin('settings_email_autoresponder_lists', 'settings_email_autoresponder_lists.settings_email_autoresponder_id', '=', 'settings_email_autoresponders.id')
            ->where(['user_id' => $this->user_id])->orderByRaw('api_name ASC')->get();
        return $autoresponder_info;
    }

    public function sync_email_to_autoresponder($email_auto_responder_settings='', $email='',$first_name='',$last_name='',$type='signup',$user_id="0",$tags='')
    {
        if(empty($email)) return false;
        $email_auto_responder_settings = json_decode($email_auto_responder_settings);
        if(empty($email_auto_responder_settings)) return false;

        $now_time = date('Y-m-d H:i:s');
        $data_to_send = ['firstname' => $first_name,'lastname' => $last_name,'email' => $email];

        $autoresponder = app(AutoResponderServiceInterface::class);

        foreach ($email_auto_responder_settings as $key=>$value)
        {
            if(empty($value)) continue;
            $settings_email_autoresponders = DB::table('settings_email_autoresponders')
                ->whereIntegerInRaw('settings_email_autoresponder_lists.id',$value)->select('settings_email_autoresponder_id','list_id','api_name','settings_data')
                ->leftJoin('settings_email_autoresponder_lists','settings_email_autoresponder_lists.settings_email_autoresponder_id','=','settings_email_autoresponders.id')
                ->get();

            foreach($settings_email_autoresponders as $key2=>$value2){
                $settings_email_autoresponder_id = $value2->settings_email_autoresponder_id ?? 0;
                $list_id = $value2->list_id ?? '';
                $api_name = $value2->api_name ?? 'mailchimp';
                $settings_data = json_decode($value2->settings_data) ?? [];

                if($api_name=='mailchimp') {
                    $api_key = $settings_data->api_key ?? '';
                    $response = $autoresponder->mailchimp_add_contact($api_key, $list_id, $data_to_send, $tags);
                }
                else if($api_name=='sendinblue') {
                    $api_key = $settings_data->api_key ?? '';
                    $response = $autoresponder->sendinblue_add_contact($api_key, $list_id, $data_to_send);
                }
                else if($api_name=='activecampaign') {
                    $api_key = $settings_data->api_key ?? '';
                    $api_url = $settings_data->api_url ?? '';
                    $response = $autoresponder->activecampaign_add_contact($api_key, $api_url, $list_id, $data_to_send);
                }
                else if($api_name=='mautic') {
                    $username = $settings_data->username ?? '';
                    $password = $settings_data->password ?? '';
                    $base_url = $settings_data->base_url ?? '';
                    $base64 = base64_encode($username . ":" . $password);
                    $response = $autoresponder->mautic_add_contact($base64, $base_url, $list_id, $data_to_send,$tags);
                }
                else if($api_name=='acelle') {
                    $api_key = $settings_data->api_key ?? '';
                    $api_url = $settings_data->api_url ?? '';
                    $response = $autoresponder->acelle_add_contact($api_key, $api_url, $list_id, $data_to_send);
                }
                $ok = $response['ok'] ?? false;
                $status = $ok===true ? '1' : '0';
                $insert_data = array('user_id' => $user_id, 'settings_type' => $type, 'status' => $status, 'email' => $email, 'api_type' => "Autoresponder", 'api_name' => $api_name, 'response' => json_encode($response), 'updated_at' => $now_time, 'email_api_id' => $settings_email_autoresponder_id);
                DB::table("sms_email_send_logs")->insert($insert_data);
            }
        }

        return true;

    }

    protected function get_available_language_list(){
        $user_id = 1;

        $all_language_list = get_language_list();

        $languages = ['en'=>'English'];
        $files = File::allFiles(resource_path().DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'translation');
        foreach ($files as $key=>$value){
            $getRelativePath = $value->getRelativePath();
            if(!isset($languages[$getRelativePath])){
                $langName = rtrim($getRelativePath,'-'.$this->user_id);
                $languages[$getRelativePath] = $all_language_list[$langName] ?? $langName;
            }
        }
        return $languages;
    }

    public function custom_error_page($error_title='',$error_code='',$error_message=''){
        if(empty($error_title)) $error_title = __('Bad Request');
        if(empty($error_code)) $error_code = __('Bad Request')." : 400";
        if(empty($error_message)) $error_message = __('An Unexpected Error Occurred.');
        $data = ['error_title'=>$error_title,'error_code'=>$error_code,'error_message'=>$error_message,'body'=>'errors.custom'];
        return view($data['body'], $data);
    }

    protected function get_team_member_list(){
        $user_id = $this->is_manager ? $this->parent_user_id : $this->user_id;
        $team_data = DB::table("users")->where(['parent_user_id'=>$user_id,'user_type'=>'Manager'])->orderByRaw('name ASC')->get();
        $team_list = [$user_id=>'Admin'];
        foreach ($team_data as $k=>$v){
            $team_list[$v->id] = $v->name;
        }
        return $team_list;
    }


    public function affiliate_commission($affiliate_id=0,$userid=0,$event='',$package_price=0)
    {

        if($event == 'signup')
        {
            $check_affiliate_special = DB::table('affiliate_payment_settings')->where(['user_id'=>$affiliate_id])->first();
            if(empty($check_affiliate_special))
                $check_affiliate_special = DB::table('affiliate_payment_settings')->where(['user_id'=>1])->first();

            $is_affiliate_special_signup_commission = $check_affiliate_special->signup_commission ?? '0';
            $is_affiliate_special_payment_commission = $check_affiliate_special->payment_commission ?? '0';
            $is_affiliate_special_payment_type = $check_affiliate_special->payment_type ?? '';

            $user_update_data = [
                'payment_commission' => $check_affiliate_special->payment_commission ?? 0,
                'payment_type' => $check_affiliate_special->payment_type ?? '',
                'percentage' => $check_affiliate_special->percentage ?? 0,
                'fixed_amount' => $check_affiliate_special->fixed_amount ?? 0,
                'is_recurring' => $check_affiliate_special->is_recurring ?? '0'
            ];
            DB::table('users')->where(['id'=>$userid])->update($user_update_data);
        }
        else
        {
            $check_affiliate_special = DB::table('users')->where(['id'=>$userid])->select(['id','payment_commission','payment_type','percentage','fixed_amount','is_recurring','affiliate_commission_given'])->first();
            $is_affiliate_special_payment_commission = $check_affiliate_special->payment_commission ?? '0';
            $is_affiliate_special_payment_type = $check_affiliate_special->payment_type ?? '';
        }

        if(isset($check_affiliate_special->id))
        {
            $commission_amount = 0;
            $special_payment_data = [];
            $special_payment_data['affiliate_id'] = $affiliate_id;
            $special_payment_data['user_id'] = $userid;
            $special_payment_data['event'] = $event;
            $special_payment_data['event_date'] = date("Y-m-d");

            if(isset($event) && $event == "signup")
            {
                if($is_affiliate_special_signup_commission == '1')
                    $commission_amount = $check_affiliate_special->sign_up_amount ?? 0;
            }
            else if(isset($event) && $event == "payment")
            {
                if($check_affiliate_special->is_recurring == '1' || ($check_affiliate_special->is_recurring == '0' && $check_affiliate_special->affiliate_commission_given == 0))
                {
                    if($is_affiliate_special_payment_commission == '1')
                    {
                        if(isset($is_affiliate_special_payment_type) && $is_affiliate_special_payment_type == 'fixed')
                        {
                            if(isset($check_affiliate_special->fixed_amount))
                                $commission_amount = $check_affiliate_special->fixed_amount;
                        }
                        else if(isset($is_affiliate_special_payment_type) && $is_affiliate_special_payment_type == 'percentage')
                        {

                            if(isset($check_affiliate_special->percentage))
                                $percentage_for_affiliate = $check_affiliate_special->percentage;
                            else
                                $percentage_for_affiliate = 0;

                            if($percentage_for_affiliate > 0)
                                $commission_amount = ($package_price * $percentage_for_affiliate) / 100;
                        }
                    }
                }
            }

            $commission_amount = number_format($commission_amount,2);
            $special_payment_data['amount'] = $commission_amount;

            if($commission_amount > 0)
            {
                DB::table('affiliate_earning_history')->insert($special_payment_data);
                $update_data = ['total_earn'=>DB::raw('total_earn+'.$commission_amount)];
                DB::table('users')->where(['id'=>$affiliate_id])->update($update_data);
                $update_given_commission = ['affiliate_commission_given'=>DB::raw('affiliate_commission_given+'.$commission_amount)];
                DB::table('users')->where(['id'=>$userid])->update($update_given_commission);
            }

        }
    }
    protected function get_usage_log_data($only_usage=false,$return_admin=false){
        if($this->is_admin && !$return_admin) return [];
        $current_month = date("n");
        $current_year = date("Y");
        // getting count of fixed modules usage
        $fixed_usage_log = DB::table('usage_logs')->select('usage_logs.*',DB::raw('sum(usage_count) as usage_count'))
            ->leftJoin('modules','modules.id','=','usage_logs.module_id')
            ->where(['user_id'=>$this->user_id,'extra_text'=>''])
            ->groupByRaw('module_id')->get();

        // getting count of monthly modules usage
        $monthly_usage_log = DB::table('usage_logs')->select('usage_logs.*',DB::raw('sum(usage_count) as usage_count'))
            ->leftJoin('modules','modules.id','=','usage_logs.module_id')
            ->where(['user_id'=>$this->user_id,'usage_month' => $current_month,'usage_year' => $current_year])
            ->where('extra_text','!=','')
            ->groupByRaw('module_id')->get();

        $usage_info = [];
        foreach ($fixed_usage_log as $key=>$value){
            $usage_info[$value->module_id] = (array) $value;
        }
        foreach ($monthly_usage_log as $key=>$value){
            $usage_info[$value->module_id] = (array) $value;
        }

        $get_modules = $this->get_modules();
        $modules = [];
        foreach ($get_modules as $key=>$value){
            $modules[$value->id] = $value;
        }

        $package_info = $this->get_package();

        $monthly_limit='';
        $bulk_limit='';

        if(isset($package_info->monthly_limit))  $monthly_limit=$package_info->monthly_limit;
        if(isset($package_info->bulk_limit))  $bulk_limit=$package_info->bulk_limit;
        $package_name= __("No Package");
        if(isset($package_info->package_name))  $package_name=$package_info->package_name;
        $validity="0";
        if(isset($package_info->validity))  $validity=$package_info->validity;
        $price="0";
        if(isset($package_info->price))  $price=$package_info->price;;
        $user_count = 0;
        $team_count = DB::table('users')->where(['user_type'=>'Manager'])->count();

        $data['user_count']=$user_count;
        $data['team_count']=$team_count;
        $data['usage_info']=$usage_info;
        $data['modules']=$modules;
        $data['monthly_limit'] = json_decode($monthly_limit,true);
        $data['bulk_limit'] = json_decode($bulk_limit,true);
        $data['package_name']=$package_name;
        $data['validity']=$validity;
        $data['price']=$price;

        if(!$only_usage){
            $config_data = $this->get_payment_config();
            $currency = $config_data->currency ?? "USD";
            $currency_icons =  get_country_iso_phone_currency_list('currency_icon');
            $data["currency"]=$currency;
            $data["curency_icon"]= isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";
        }
        return $data;
    }

    public function important_feature($redirect=true)
    {
        if(File::exists(base_path('config/build.txt')) && File::exists(base_path('assets/build.txt')))
        {
            $config_existing_content = File::get(base_path('config/build.txt'));
            $config_decoded_content = json_decode($config_existing_content, true);

            $core_existing_content = File::get(base_path('assets/build.txt'));
            $core_decoded_content = json_decode($core_existing_content, true);

            if($config_decoded_content['is_active'] != md5($config_decoded_content['purchase_code']) || $core_decoded_content['is_active'] != md5(md5($core_decoded_content['purchase_code'])))
            {
                if($redirect) return redirect()->route('credential-check');
                else return false;
            }
        }
        else
        {
            if($redirect) return redirect()->route('credential-check');
            else return false;
        }

        return true;
    }


    public function credential_check()
    {
        $permission = 0;
        if(Auth::user()->user_type =="Admin") $permission = 1;
        else $permission = 0;
        if($permission == 0) abort(403);

        $data["page_title"] = __("Credential Check");
        return view('auth.credential-check');
    }

    public function credential_check_action(Request $request)
    {
        $domain_name = $request->domain_name;
        $purchase_code = $request->purchase_code;
        $only_domain = get_domain_only($domain_name);
        $response=$this->code_activation_check_action($purchase_code,$only_domain);
        echo json_encode($response);
    }

    function get_general_content_with_checking($url,$proxy="")
    {
        $ch = curl_init(); // initialize curl handle

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
        curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 50s
        curl_setopt($ch, CURLOPT_POST, 0); // set POST method

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $content = curl_exec($ch); // run the whole process
        $response['content'] = $content;

        $res = curl_getinfo($ch);
        if($res['http_code'] != 200)
            $response['error'] = 'error';
        curl_close($ch);
        return json_encode($response);

    }

    public function code_activation_check_action($purchase_code,$only_domain,$periodic=0)
    {
        $url = "https://xeroneit.net/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=Aipen";
        $credentials = $this->get_general_content_with_checking($url);

        $decoded_credentials = json_decode($credentials,true);

        $decoded_credentials = json_decode($decoded_credentials['content'],true);


        if(!isset($decoded_credentials['error']))
        {
            if($decoded_credentials['status'] == 'success')
            {
                $content_to_write = array(
                    'is_active' => md5($purchase_code),
                    'purchase_code' => $purchase_code,
                    'item_name' => $decoded_credentials['item_name'],
                    'buy_at' => $decoded_credentials['buy_at'],
                    'licence_type' => $decoded_credentials['license'],
                    'domain' => $only_domain,
                    'checking_date'=>date('Y-m-d')
                    );

                $config_json_content_to_write = json_encode($content_to_write);
                file_put_contents(base_path('config/build.txt'), $config_json_content_to_write, LOCK_EX);


                $content_to_write['is_active'] = md5(md5($purchase_code));
                $core_json_content_to_write = json_encode($content_to_write);
                file_put_contents(base_path('assets/build.txt'), $core_json_content_to_write, LOCK_EX);

                // added by mostofa 06/03/2017
                $license_type = $decoded_credentials['license'];

                if($license_type == 'Extended License')
                    $str = $purchase_code."_double";
                else
                    $str = $purchase_code."_single";

                $encrypt_method = "AES-256-CBC";
                $secret_key = 't8Mk8fsJMnFw69FGG5';
                $secret_iv = '9fljzKxZmMmoT358yZ';
                $key = hash('sha256', $secret_key);
                $string = $str;
                $iv = substr(hash('sha256', $secret_iv), 0, 16);
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $encoded = base64_encode($output);
                file_put_contents(base_path('config/build-type.txt'), $encoded, LOCK_EX);

                return json_encode($decoded_credentials);
            }
            else if($decoded_credentials['status'] == 'error'){
                if(File::exists(base_path('config/build.txt'))) unlink(base_path('config/build.txt'));
                return json_encode($decoded_credentials);
            }
        }
        else
        {
            if($periodic == 1)
                return json_encode(['status'=>"success"]);
            else
            {
                $response['reason'] = __('cURL is not working properly, please contact your hosting provider.');
                return json_encode($response);
            }
        }
    }

    public function restricted_access(){
        $data = [
            "error_title" => "Demo Restriction",
            "error_code" => "Demo Restriction",
            "error_message" => "This feature has been disabled in this demo version. We recommend to sign up as user and check."
        ];
        return view("errors.custom", $data);
    }

}
