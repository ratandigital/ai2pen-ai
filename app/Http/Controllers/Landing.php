<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Config;

class Landing extends Home
{
    public function index(Request $request){

        $aff_track = $request->aff_track;
        if($aff_track !== null)
        {
            $affiliate_user_id = hex2bin($aff_track);
            //cookie set for 1 year (525,600 minutes)
            Cookie::queue('affiliate_user_id', $affiliate_user_id, 525600);
        }
        $data = $this->make_view_data();
        if($data['disable_landing_page']=='1') return redirect()->route('login');
        $data['body'] = 'landing.index';
        $data['title'] = $data['get_landing_language']->company_title ?? '';

        $template_sidebar = DB::table("ai_templates")->select('ai_templates.*','group_name','group_slug','icon_class')
            ->leftJoin('ai_template_groups','ai_template_groups.id','=','ai_templates.ai_template_group_id')
            ->where(['ai_template_groups.status'=>'1','ai_templates.status'=>'1'])
            ->orderByRaw('ai_template_groups.serial asc')->get();
        $ai_sidebar_group_by_id = [];
        $template_group_icon_list = [];
        $template_list = [];
        foreach($template_sidebar as $tk=>$tv){
            $api_group = $tv->api_group;
            $tv->has_access = true;
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
        return $this->site_viewcontroller($data);
    }

    public function pricing_plan(){

        $validity = !empty(request()->validity) ? request()->validity : 30;
        $data = $this->make_view_data();
        $data['body'] = 'landing.pricing';
        $data['title'] = __('Pricing Plan');
        $get_pricing_list = $this->get_pricing_list();
        $data['get_modules'] = $this->get_modules();
        $data['other_available_plan_show'] = false;
        $data['format_settings'] = $this->get_payment_formatting_data();
        $package_validity_list = [];
        foreach($get_pricing_list as $key=>$value){
            if(!isset($package_validity_list[$value->validity]) && $value->is_default!='1') $package_validity_list[$value->validity] = convert_number_validity_phrase($value->validity);
            if(!empty($validity) && $value->validity!=$validity && $value->is_default!='1') $get_pricing_list->forget($key);
        }
        ksort($package_validity_list);
        $data['package_validity_list'] = $package_validity_list;
        $data['get_pricing_list'] = $get_pricing_list;
        $data['default_validity'] = $validity;
        return $this->site_viewcontroller($data);
    }

    public function policy_privacy(){
        $data = $this->make_view_data();
        $data['body'] = 'landing.policy.privacy';
        $data['title'] = __('Privacy Policy');
        return $this->site_viewcontroller($data);
    }

    public function policy_terms(){
        $data = $this->make_view_data();
        $data['body'] = 'landing.policy.terms';
        $data['title'] = __('Terms of Service');
        return $this->site_viewcontroller($data);
    }

    public function policy_refund(){
        $data = $this->make_view_data();
        $data['body'] = 'landing.policy.refund';
        $data['title'] = __('Refund Policy');
        return $this->site_viewcontroller($data);
    }

    public function policy_gdpr(){
        $data = $this->make_view_data();
        $data['body'] = 'landing.policy.gdpr';
        $data['title'] = __('GDPR');
        return $this->site_viewcontroller($data);
    }

    public function accept_cookie(){
        session(['allow_cookie'=>'yes']);
    }

    public function popup_stat(Request $request){
        $users = DB::table('users')->where(['parent_user_id'=>1])->orderByDesc('created_at')->limit(10)->get();
        $str ='';
        $i=1;
        foreach ($users as $user) {
            $name = $user->name;
            if(strlen($name)>18) $name = substr($name,0,).'...';
            $register_time = Carbon::parse($user->created_at)->diffForHumans();
            $profile_pic  = !empty($user->profile_pic) ? $user->profile_pic : asset('assets/images/avatar/avatar-1.png');
            $str.= '<div class="toast-container" id="tost_container'.$i.'">
            <img class="rounded-circle" src="'.$profile_pic.'" alt="" />
            <div class="text-content text-dark">
                <span class="customer_name text-primary">'.$name.'</span>
                <span class="text-text-2">'.__("just joined").'</span>
                <span class="customers_product fw-bold">'.config('app.name').'</span>
                <span class="time">'.$register_time.' </span>
             </div>
            </div>';
            $i++;
        }
        return response()->json(array('str'=>$str));
    }


    protected function get_pricing_list($limit=9999){
        $user_id = 1;
        if(empty($user_id)) $user_id = 1;
        $query = DB::table('packages')->where(['user_id'=>$user_id,'visible'=>'1','deleted'=>'0','package_type'=>'subscription']);
        $result = $query->orderByRaw('CAST(`price` AS SIGNED)')->limit($limit)->get();
        return $result;
    }

    public function install(){
        \Artisan::call('storage:link');
        $source = base_path('assets');
        $target = public_path('assets');
        if (!file_exists($target)) {
            @File::link($source, $target);
        }

        $install_txt_permission = File::isWritable(public_path("install.txt"));
        $env_file_permission = File::isWritable(base_path('.env'));

        $views_file_permission =
        File::isWritable(base_path('resources/views')) &&
        File::isWritable(base_path('resources/views/auth')) &&
        File::isWritable(base_path('resources/views/emails')) &&
        File::isWritable(base_path('resources/views/errors')) &&
        File::isWritable(base_path('resources/views/include')) &&
        File::isWritable(base_path('resources/views/landing')) &&
        File::isWritable(base_path('resources/views/layouts')) &&
        File::isWritable(base_path('resources/views/member')) &&
        File::isWritable(base_path('resources/views/openai')) &&
        File::isWritable(base_path('resources/views/subscription')) &&
        File::isWritable(base_path('resources/views/vendor'));

        $lang_file_permission =
        File::isWritable(base_path('resources/lang')) &&
        File::isWritable(base_path('resources/lang/en')) &&
        File::isWritable(base_path('resources/lang/vendor/translation/en'));

        $helpers_file_permission = File::isWritable(base_path('app/Helpers'));

        $controllers_file_permission =
        File::isWritable(base_path('app/Http/Controllers')) &&
        File::isWritable(base_path('app/Http/Controllers/Auth'));

        $middleware_file_permission = File::isWritable(base_path('app/Http/Middleware'));

        $services_file_permission =
        File::isWritable(base_path('app/Services')) &&
        File::isWritable(base_path('app/Services/AutoResponder')) &&
        File::isWritable(base_path('app/Services/Payment')) &&
        File::isWritable(base_path('app/Providers')) &&
        File::isWritable(base_path('app/Providers/AutoResponder')) &&
        File::isWritable(base_path('app/Providers/Payment'));

        $config_file_permission = File::isWritable(base_path('config/app.php'));

        $assets_file_permission =
        File::isWritable(base_path('assets/images')) &&
        File::isWritable(base_path('assets/css')) &&
        File::isWritable(base_path('assets/css/pages')) &&
        File::isWritable(base_path('assets/js')) &&
        File::isWritable(base_path('assets/js/common')) &&
        File::isWritable(base_path('assets/js/pages')) &&
        File::isWritable(base_path('assets/landing'));

        $routes_file_permission = File::isWritable(base_path('routes/web.php'));

        $storage_file_permission =
        File::isWritable(base_path('storage/framework/cache/data')) &&
        File::isWritable(base_path('storage/framework/sessions')) &&
        File::isWritable(base_path('storage/framework/testing')) &&
        File::isWritable(base_path('storage/framework/views')) &&
        File::isWritable(base_path('storage/logs')) &&
        File::isWritable(base_path('storage/app/public/profile')) &&
        File::isWritable(base_path('storage/app/public/agency')) &&
        File::isWritable(base_path('storage/app/public/template')) &&
        File::isWritable(base_path('storage/app/public/download')) &&
        File::isWritable(base_path('storage/app/public/tools'));

        $data['body'] = 'auth.install';
        $data['install_txt_permission'] = $install_txt_permission;
        $data['env_file_permission'] = $env_file_permission;
        $data['resource_file_permission'] = $views_file_permission && $lang_file_permission;
        $data['http_file_permission'] = $controllers_file_permission && $middleware_file_permission;
        $data['helpers_file_permission'] = $helpers_file_permission;
        $data['services_file_permission'] = $services_file_permission;
        $data['config_file_permission'] = $config_file_permission;
        $data['assets_file_permission'] = $assets_file_permission;
        $data['routes_file_permission'] = $routes_file_permission;
        $data['storage_file_permission'] = $storage_file_permission;

        return $this->site_viewcontroller($data);
    }

    public function installation_submit(Request $request)
    {
        $current_url = trim(url()->current(),'/');
        $https = str_starts_with($current_url,'https');
        $domain = get_domain_only($current_url);
        $rules = [];
        $rules['host_name'] = 'required';
        $rules['database_name'] = 'required';
        $rules['database_username'] = 'required';

        $rules['app_username'] = 'required|email';
        $rules['app_password'] = 'required';
        $request->validate($rules);

        $host_name = $request->host_name;
        $database_name = $request->database_name;

        $database_username = $request->database_username;
        $database_password = $request->database_password;

        $app_username = $request->app_username;
        $app_password = $request->app_password;


        $con=@mysqli_connect($host_name, $database_username, $database_password);
        if (!$con) {
            $mysql_error = "Could not connect to MySQL : ";
            $mysql_error .= mysqli_connect_error();

            die($mysql_error);
        }
        if (!@mysqli_select_db($con,$database_name)) {
            die("database not found");
        }

        Config::set('database.connections.mysql.host', $host_name);
        Config::set('database.connections.mysql.database',  $database_name);
        Config::set('database.connections.mysql.username', $database_username);
        Config::set('database.connections.mysql.password', $database_password);

        $path = base_path('.env');
        $initial_env = public_path('initial_env.txt');
        $test = file_get_contents($initial_env);
        if (file_exists($path))
        {
            $test = str_replace('DB_HOST=', 'DB_HOST="'.$host_name.'"', $test);
            $test = str_replace('DB_DATABASE=', 'DB_DATABASE="'.$database_name.'"', $test);
            $test = str_replace('DB_USERNAME=', 'DB_USERNAME="'.$database_username.'"', $test);
            $test = str_replace('DB_PASSWORD=', 'DB_PASSWORD="'.$database_password.'"', $test);
            $test = str_replace('APP_DOMAIN="aipen.test"', 'APP_DOMAIN="'.$domain.'"', $test);
            if($https) {
                $test = str_replace('APP_PROTOCOL="http://"', 'APP_PROTOCOL="https://"', $test);
                $test = str_replace('FORCE_HTTPS=false', 'FORCE_HTTPS=true', $test);
            }
            file_put_contents($path,$test);
        }

        $dump_sql_path = public_path('initial_db.sql');
        $dump_file = $this->import_dump($dump_sql_path,$con);
        DB::table('version')->insert(['version'=>trim(env('APP_VERSION')),'current'=>'1','date'=>date('Y-m-d H:i:s')]);
        //generating hash password for admin and updaing database
        $app_password = Hash::make($app_password);
        DB::table('users')->where('user_type','Admin')->update(["email" => $app_username, "password" => $app_password,"status" => "1", "deleted" => "0"]);
        //generating hash password for admin and updaing database

        //deleting the install.txt file,because installation is complete
        if (file_exists(public_path('install.txt'))) {
          unlink(public_path('install.txt'));
        }
        //deleting the install.txt file,because installation is complete
        return redirect(route('login'));
    }

    public function import_dump($filename = '',$con='')
    {
        if ($filename=='') {
            return false;
        }
        if (!file_exists($filename)) {
            return false;
        }
        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($filename);
        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {

                mysqli_query($con, $templine);
                // Reset temp variable to empty
                $templine = '';
            }
        }
        return true;

    }

}
