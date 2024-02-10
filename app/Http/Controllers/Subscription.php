<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Form;

class Subscription extends Home
{
    public function __construct()
    {
        $this->set_global_userdata(true,['Admin','Member'],['Manager']);
    }

    public function list_package()
    {
       $payment_config = $this->get_payment_config();
       $has_team_access = has_module_access($this->module_id_team_member, $this->module_ids, $this->is_admin);
       $data = array('body'=>'subscription/package/list-package','payment_config'=>$payment_config,'load_datatable'=>true,'has_team_access'=>$has_team_access);
       return $this->viewcontroller($data);
    }

    public function list_package_data(Request $request)
    {
        $search_value = $request->search_value;
        $search_package_type = $request->search_package_type;

        $display_columns = array("#",'id', 'package_name','package_type','price','validity','is_default');
        $search_columns = array('package_name','price','validity');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 1;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="packages";
        $query = DB::table($table)->where('user_id',$this->user_id)->where('deleted','0');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_type)) $query->where('package_type','=',$search_package_type);
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($table.'id')->where('user_id',$this->user_id)->where('deleted','0');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_type)) $query->where('package_type','=',$search_package_type);
        $total_result = $query->count();

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function create_package()
    {
        $team_package = isset(request()->type) && request()->type=='team';
        $data['has_team_access'] = has_module_access($this->module_id_team_member, $this->module_ids, $this->is_admin);
        $data['body'] = 'subscription/package/create-package';
        $data['modules'] = $this->get_modules($team_package);
        $data['payment_config'] = $this->get_payment_config();
        $data['validity_types'] = $this->get_validity_types();
        return $this->viewcontroller($data);
    }

    public function save_package(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return \redirect(route('restricted-access'));
        $package_type = $request->package_type ?? 'team';
        if($package_type=='team' && !has_module_access($this->module_id_team_member, $this->module_ids, $this->is_admin)) abort(403);
        if($package_type=='subscription' && !$this->is_admin) abort(403);

        $rules =
        [
            'package_type' => 'required|string',
            'package_name' => 'required|string|max:99',
            'visible' => 'nullable|sometimes|boolean',
            'highlight' => 'nullable|sometimes|boolean',
            'modules' => 'nullable|sometimes',
            'team_access' => 'nullable|sometimes'
        ];

        if($package_type!='team'){
            if($request->is_default == '1') $rules['price'] = 'required';
            else $rules['price'] = 'required|numeric|min:1';
        }
        else $rules['price'] = 'nullable|sometimes';


        if($package_type!='team' && (($request->is_default == '1' && $request->price == "Trial") || is_null($request->is_default)))
        {
            $rules['validity'] = 'required|integer|min:1';
            $rules['validity_type'] = 'required|string';
        }
        else
        {
            $rules['validity'] = 'nullable|sometimes|integer';
            $rules['validity_type'] = 'nullable|sometimes|string';
        }
        $validate_data = $request->validate($rules);

        $modules = $validate_data['modules'];
        if($package_type=='team'){
            $team_access = $validate_data['team_access'] ?? [];
            // if module is not checked but permission checked then remove the permission
            foreach ($team_access as $k=>$v) {
                if(!in_array($k,$modules)) unset($team_access[$k]);
            }
            // if no permission allowed for a module then assign empty manually
            foreach ($modules as $k=>$v) {
                if(!isset($team_access[$v])) $team_access[$v] = [];
            }
            $validate_data['team_access'] = json_encode($team_access);
            $validate_data['price'] = null;
        }
        else $validate_data['team_access'] = null;

        $validate_data['visible'] = isset($_POST['visible']) ? "1" : "0";
        $validate_data['highlight'] = isset($_POST['highlight']) ? "1" : "0";

        $product_data = [
            'paypal' =>[
                'plan_id'=>$request->paypal_plan_id
            ]
        ];
        $validate_data['product_data'] = $package_type=='team' ? null : json_encode($product_data);

        $discount_apply_all = isset($_POST['discount_apply_all']) ? "1" : "0";
        $discount_data = [
            'percent' => $request->discount_percent,
            'terms' => $request->discount_terms,
            'start_date' => $request->discount_start_date,
            'end_date' => $request->discount_end_date,
            'timezone' => $request->discount_timezone,
            'status' => isset($_POST['discount_status']) ? "1" : "0"
        ];
        $validate_data['discount_data'] = $package_type=='team' ? null : json_encode($discount_data);

        $validity_type_arr = ['D' => 1,'W' => 7,'M' => 30,'Y' => 365];
        $validity = $validate_data['validity'] ?? 0;
        $validate_data['validity'] =   $package_type=='team' ? null : $validity* $validity_type_arr[$validate_data['validity_type'] ?? 'D'];
        $validate_data['validity_extra_info'] = $package_type=='team' ? null : implode(',', array( $validity,  $validate_data['validity_type']));

        $bulk_limit=array();
        $monthly_limit=array();

        foreach ($modules as $value)
        {
            $monthly_field="monthly_".$value;
            $val=$request->$monthly_field;
            if($val=="") $val=0;
            $monthly_limit[$value]=$val;
            $bulk_field="bulk_".$value;
            $val=$request->$bulk_field;
            if($val=="") $val=0;
            $bulk_limit[$value]=$val;
        }
        if(isset($validate_data['modules'])) unset($validate_data['modules']);
        if(isset($validate_data['validity_type'])) unset($validate_data['validity_type']);
        $validate_data['module_ids'] = implode(',',$modules);
        $validate_data['monthly_limit'] = $package_type=='team' ? null : json_encode($monthly_limit);
        $validate_data['bulk_limit'] = $package_type=='team' ? null : json_encode($bulk_limit);
        if(!isset($request->id)) $validate_data['user_id'] = $this->user_id;

        $query = true;
        if(isset($request->id)) DB::table("packages")->where(['id'=>$request->id,'user_id'=>$this->user_id])->update($validate_data);
        else $query = DB::table("packages")->insert($validate_data);

        if($discount_apply_all=='1'){
            DB::table("packages")->where(['user_id'=>$this->user_id,'is_default'=>'0','package_type'=>'subscription'])->update(['discount_data'=>$discount_data]);
        }
        if($query) $request->session()->flash('save_package_status', '1');
        else $request->session()->flash('save_package_status', '0');
        return redirect(route('list-package'));

    }

    public function update_package($id)
    {
        $xdata = DB::table('packages')->where(['id'=>$id,'user_id'=>$this->user_id])->first();
        if(!isset($xdata)) abort(403);
        $team_package = $xdata->package_type == 'team';

        $data['has_team_access'] = has_module_access($this->module_id_team_member, $this->module_ids, $this->is_admin);
        $data['body'] = 'subscription/package/update-package';
        $data['modules'] = $this->get_modules($team_package);
        $data['payment_config'] = $this->get_payment_config();
        $data['validity_types'] = $this->get_validity_types();
        $data['xdata'] = $xdata;

        $validity_days = $xdata->validity;
        if ($validity_days % 365 == 0) {
            $validity_type = 'Y';
            $validity_amount = $validity_days / 365;
        }
        else if ($validity_days % 30 == 0) {
            $validity_type = 'M';
            $validity_amount = $validity_days / 30;
        }
        else if ($validity_days % 7 == 0) {
            $validity_type = 'W';
            $validity_amount = $validity_days / 7;
        }
        else {
            $validity_type = 'D';
            $validity_amount = $validity_days;
        }
        $data['validity_type'] = $validity_type;
        $data['validity_amount'] = $validity_amount;

        return $this->viewcontroller($data);
    }

    public function delete_package(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin)
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        $id = $request->id;
        $query = DB::table('packages')->where('id',$id)->where('user_id',$this->user_id)->where('is_default','0')->update(['deleted'=>'1']);
        if($query) return response()->json(['error' => false,'message' => __('Package has been deleted successfully')]);
        else return response()->json(['error' => true,'message' => __('Something went wrong')]);
    }

    public function list_user()
    {
        $has_team_access = has_module_access($this->module_id_team_member, $this->module_ids, $this->is_admin);
        $data = array('body'=>'subscription/user/list-user','load_datatable'=>true,'has_team_access'=>$has_team_access);
        $package_list = $this->get_packages_all();
        $packages = [''=>__('Any Package/Role')];
        if($this->is_admin) $packages['Subscribed']='--- '.__('Only Paid Subscription').' ---';
        foreach ($package_list as $k=>$v){
            $extra_text = $has_team_access && !empty($v->package_type) ? ucfirst($v->package_type).' : ' : '';
            $packages[$v->id] = $extra_text.$v->package_name;
        }
        $data['packages']=$packages;
        return $this->viewcontroller($data);
    }

    public function list_user_data(Request $request)
    {
        $search_value = $request->search_value;
        $search_package_id = $request->search_package_id;
        $search_user_type = $request->search_user_type;
        $display_columns = array("#","CHECKBOX", 'profile_pic','name', 'email','package_name', 'status', 'user_type', 'actions','expired_date', 'created_at','last_login_at','last_login_ip','user_id');
        $search_columns = array('name', 'email');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 13;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'user_id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="users";
        $select= ["users.*","users.id as user_id","packages.package_name"];
        $query = DB::table($table)->select($select)->where('parent_user_id',$this->user_id)->where('user_type','!=','Affiliate')->where($table.'.deleted','0')->leftJoin('packages', 'users.package_id', '=', 'packages.id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_id)) {
            if($search_package_id=='Subscribed') $query->where('package_id','>','1');
            else $query->where('package_id','=',$search_package_id);
        }
        if (!empty($search_user_type)) $query->where('user_type','=',$search_user_type);

        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($table.'id')->where('parent_user_id',$this->user_id)->where('user_type','!=','Affiliate')->where($table.'.deleted','0')->leftJoin('packages', 'users.package_id', '=', 'packages.id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_package_id)) {
            if($search_package_id=='Subscribed') $query->where('package_id','>','1');
            else $query->where('package_id','=',$search_package_id);
        }
        if (!empty($search_user_type)) $query->where('user_type','=',$search_user_type);
        $total_result = $query->count();

        $i=0;
        foreach ($info as $key => $value)
        {
            $status_checked = ($value->status=='1') ? 'checked' : '';
            $value->status = '<div class="pt-2 form-check form-switch update-status-switch d-flex justify-content-center"><input data-url="'.route('update-user-status').'" data-id="'.$value->id.'" class="form-check-input update-status" type="checkbox" '.$status_checked.' value="'.$value->status.'"></div>';

            $last_login_at = $value->last_login_at;
            if($last_login_at=='0000-00-00 00:00:00')  $value->last_login_at = __("Never");
            else  $value->last_login_at = convert_datetime_to_timezone($value->last_login_at);

            if($value->user_type=='Manager') $value->expired_date = '-';
            else{
                $expired_date =  $value->expired_date;
                if($expired_date=='0000-00-00 00:00:00' ||  $value->user_type == "Admin")  $value->expired_date = "-";
                else  $value->expired_date = convert_datetime_to_timezone($value->expired_date,'',false,'jS M y');
            }

            $value->created_at = convert_datetime_to_timezone($value->created_at,'',false,'jS M y');

            if($value->package_name=="") $value->package_name = "-";

            $user_name = $value->name;
            $user_id = $value->id;
            $edit_url = route('update-user',$value->id);
            $dash_url = route('dashboard-user').'?id='.$value->id;

            $delete_url = route('delete-user');
            $str="";

            $str=$str."&nbsp;&nbsp;<a class='btn btn-circle btn-outline-warning' href='".$edit_url."' title='".__('Edit')."'>".'<i class="fas fa-edit"></i>'."</a>";
            $str=$str."&nbsp;&nbsp;<a href='".$delete_url."' data-id='".$value->id."' class='delete-row btn btn-circle btn-outline-danger' title='".__('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->actions = "<div class='w-min-120px'>".$str."</div>";

            $profile_pic  = !empty($value->profile_pic) ? $value->profile_pic : asset('assets/images/avatar/avatar-1.png');
            $value->profile_pic = "<img src='".$profile_pic."' width='40px' height='40px' class='rounded-circle'>";

            if($value->user_type=='Manager') $value->user_type=__("Team");
            else if($value->user_type=='Member') $value->user_type=__("Member");

            if(config('app.is_demo')=='1' && $this->is_admin) $value->email = "xxxxxx@something.com";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function create_user()
    {
        $team_package = isset(request()->type) && request()->type=='team';
        $data['body'] = 'subscription/user/create-user';
        $data['packages'] = $this->get_packages('*',$team_package);
        return $this->viewcontroller($data);
    }

    public function save_user(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return \redirect(route('restricted-access'));
        $action_type = $request->action_type;

        $team_limit_error = false;
        $user_password = $request->password;
        $user_type = $request->user_type;
        if($user_type=='Manager' && !has_module_access($this->module_id_team_member, $this->module_ids, $this->is_admin)) abort(403);
        if($user_type=='Member' && !$this->is_admin) abort(403);
        $status = isset($_POST['status']) ? "1" : "0";

        if(!$this->is_admin && $action_type=='team' && !isset($request->id))
        {
            $team_limit = $this->monthly_limit[$this->module_id_team_member] ?? -1;
            if($team_limit < 0 ) $team_limit_error = true;
            else if($team_limit > 0)
            {
                $count_team = DB::table('users')->select('id')->where('parent_user_id',$this->user_id)->where('user_type','Manager')->where('status','1')->count();
                if($count_team>=$team_limit) $team_limit_error = true;
            }
        }
        if($team_limit_error)
        {
            $request->session()->flash('save_team_limit_error', '1');
            return redirect(route('list-user'));
        }

        $rules =
        [
            'name' => 'required|string|max:99',
            'mobile' => 'nullable|sometimes|string',
            'address' => 'nullable|sometimes|string',
            'package_id' => 'required|integer',
            'status' => 'required|sometimes|boolean',
            'user_type' => 'required|string',
            'expired_date' => 'required_if:user_type,Member|date|nullable|sometimes',
        ];

        if(!isset($request->id)) {
            $rules['password'] = 'required|min:6|confirmed';
            $rules['email'] = 'required|string|email|max:99|unique:users';
        }
        else {
            $rules['password'] = 'nullable|sometimes|min:6|confirmed';
            $rules['email'] = 'required|email|max:99|unique:users,email,' . $request->id;
        }

        $validate_data = $request->validate($rules);

        $curdate = date("Y-m-d H:i:s");
        $validate_data['status'] = $status;
        $validate_data['updated_at'] = $curdate;
        if(!isset($request->id)) {
            $validate_data['parent_user_id'] = $this->user_id;
            $validate_data['created_at'] = $curdate;
            $validate_data['purchase_date'] = $curdate;
            $validate_data['password'] =  Hash::make($user_password);
        }
        else {
            if(empty($user_password)) unset($validate_data['password']);
            else $validate_data['password'] =  Hash::make($user_password);
        }

        if(!isset($validate_data['expired_date'])) $validate_data['expired_date'] = null;
        if($action_type=="user") $validate_data['user_type'] = 'Member';

        if(!isset($request->id) || $request->email != $request->xemail) // new user email and edited user with a new email need to be verified
        $validate_data['email_verified_at'] = null;

        $error = false;
        try {
            if (isset($request->id)) {
                DB::table("users")->where(['id' => $request->id, 'parent_user_id' => $this->user_id])->update($validate_data);
                $insert_id = $request->id;
            } else {
                DB::table("users")->insert($validate_data);
                $insert_id = DB::getPdo()->lastInsertId();
            }
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error = $e->getMessage();
        }

        if(!$error) $request->session()->flash('save_user_status', '1');
        else {
            $request->session()->flash('save_user_status', '0');
            $request->session()->flash('save_user_status_error', __($error));
        }

        return redirect(route('list-user'));

    }

    public function update_user($id)
    {
        if(config('app.is_demo')=='1' && $this->is_admin) return \redirect(route('restricted-access'));
        $xdata = DB::table('users')->where(['id'=>$id,'parent_user_id'=>$this->user_id])->first();
        if(!isset($xdata)) abort(403);
        $team_package = $xdata->user_type == 'Manager';
        $data['body'] = 'subscription/user/update-user';
        $data['packages'] = $this->get_packages('*',$team_package);
        $data['xdata'] = $xdata;
        return $this->viewcontroller($data);
    }

    public function delete_user(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin)
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        if($this->is_manager) {
            return response()->json(['error' => true,'message' => __('Access Denied')]);
        }

        $user_id = $request->id;
        if($user_id==$this->user_id){
            return response()->json(['error' => true,'message' => __('You cannot delete yourself. Please contact your service provider.')]);
        }

        $table = 'users';
        $where = ['parent_user_id'=>$this->user_id,'id'=>$user_id];
        if(!valid_to_delete($table,$where)) {
            return response()->json(['error'=>true,'message'=>__('Bad request.')]);
        }

        //finding all users
        $user_data = DB::table($table)
            ->select('id')
            ->where('user_type','!=','Admin')
            ->where(function ($query) use ($user_id){
                $query->where('id', '=', $user_id)
                    ->orWhere('parent_user_id', '=',$user_id);
            })->get();
        $user_ids = [];
        foreach ($user_data as $value) array_push($user_ids,$value->id);
        if(empty($user_ids)) {
            return response()->json(['error'=>true,'message'=>__('User not found.')]);
        }

        $success = false;
        $error_message = '';
        try {
            DB::beginTransaction();

            DB::table($table)->whereIntegerInRaw('id',$user_ids)->delete();

            DB::commit();
            $success = true;
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error_message = $e->getMessage();
        }
        return response()->json(['error' => true,'message' => __('Database error : ').$error_message]);

    }

    public function update_user_status(Request $request)
    {
        if(config('app.is_demo')=='1' && $this->is_admin)
            return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);
        $id = $request->id;
        $status = $request->status;
        $where = ['id'=> $id,'parent_user_id'=>$this->user_id];
        $query = DB::table('users')->where($where)->update(['status' => $status,'updated_at'=>date("Y-m-d H:i:s")]);
        if($query) return response()->json(['error' => false,'message' => __('User status has been updated successfully')]);
        else return response()->json(['error' => true,'message' => __('Something went wrong')]);
    }

    public function user_send_email(Request $request)
    {
        if(config('app.is_demo')=='1') {
            echo "<b>This feature has been disabled in this demo version. We recommend to sign up as user and check.</b>";
            exit();
        }

        $subject = $request->subject;
        $message = $request->message;
        $user_ids = $request->user_ids;
        $count=0;

        $info = DB::table("users")->whereIn("id",$user_ids)->select('email','name')->get();

        $error_message = __("Emails have been sent successfully.");
        try {
            foreach($info as $member)
            {
                $email = $member->email;
                $name = $member->name;
                if($message=="" || $subject=="") continue;
                $title = __("Hello").' '.$name;
                $response = $this->send_email($email,$message,$subject,$title);
                if(isset($response['error']) && !$response['error']) $count++;
            }
        }
        catch (\Throwable $e){
            $error_message = $e->getMessage();
        }
        echo "<b> $count / ".count($info)." : ".$error_message."</b>";
    }



    public function affiliate_settings()
    {
        if(!$this->is_admin) abort('404');
        $data['body'] = 'member.settings.settings-affiliate';
        $data['load_datatable'] = true;
        $commission_data = (array) DB::table("affiliate_payment_settings")->where("user_id",$this->user_id)->first();
        $currency = (array) $this->get_payment_config_parent($this->parent_user_id,'currency');
        $data['curency_icon'] = $currency['currency'] ?? "USD";
        $data['info'] = $commission_data;
        $data['commission_view'] = resource_path('views/affiliate/affiliate_admin/settings-affiliate-commission');
        return $this->viewcontroller($data);
    }

    public function affiliate_list_user_data(Request $request)
    {
        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $display_columns = array("#","CHECKBOX",'name', 'email','referal_users','available_balance','approved_withdrawal', 'pending_withdrawal','actions','last_login_ip','user_id');
        $search_columns = array('name', 'email',);
        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 2;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'user_id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="users";
        $select= ["users.*","users.id as user_id"];

        $query_part = '';
        if(!empty($search_value)){
            $query_part = 'AND (u1.name like "%'.$search_value.'%" OR u1.email like "%'.$search_value.'%")';
        }

        $query = DB::select(DB::raw('SELECT u1.*,(SELECT count(id) FROM users as u2 WHERE u2.under_which_affiliate_user= u1.id) as referal_users,(SELECT SUM(requested_amount) from affiliate_withdrawal_requests where status = "1" AND user_id=u1.id) as approved_withdrawal, (SELECT SUM(requested_amount) from affiliate_withdrawal_requests where status = "0" AND user_id=u1.id) as pending_withdrawal FROM users as u1 LEFT JOIN affiliate_withdrawal_requests ON affiliate_withdrawal_requests.user_id = u1.id WHERE u1.is_affiliate = "1" '.$query_part.' GROUP BY u1.id'));

        $query2 = DB::select(DB::raw('SELECT u1.*,(SELECT count(id) FROM users as u2 WHERE u2.under_which_affiliate_user= u1.id) as referal_users,(SELECT SUM(requested_amount) from affiliate_withdrawal_requests where status = "1" AND user_id=u1.id) as approved_withdrawal, (SELECT SUM(requested_amount) from affiliate_withdrawal_requests where status = "0" AND user_id=u1.id) as pending_withdrawal FROM users as u1 LEFT JOIN affiliate_withdrawal_requests ON affiliate_withdrawal_requests.user_id = u1.id WHERE u1.is_affiliate = "1" '.$query_part.' GROUP BY u1.id  LIMIT '.$limit.' OFFSET '.$start.' '));

        $total_result =count($query);

        $info = (object) $query2;
        $i=0;
        foreach ( $info as $key => $value)
        {


            $value->available_balance = $value->total_earn;
            $user_name = $value->name;
            $user_id = $value->id;
            $value->user_id = $user_id;
            $edit_url = route('update-user',$value->id);
            $dash_url = route('affiliate-dashboard').'?id='.$value->id;
            $str="";

            $str=$str."<a class='btn btn-circle btn-outline-primary' target='_BLANK' href='".$dash_url."' title='".__('Dashboard')."'>".'<i class="fas fa-chart-pie"></i>'."</a>";
            $str=$str."&nbsp;&nbsp;<a class='btn btn-circle btn-outline-warning update_affiliate_user' actionType='update' id='".$user_id."' href='#' title='".__('Edit')."'>".'<i class="fas fa-edit"></i>'."</a>";
            $width = $this->is_admin ? 160 : 120;
            $value->actions = "<div style='min-width:".$width."px'>".$str."</div>";
            if($value->approved_withdrawal == NULL){
                $value->approved_withdrawal = 0;
            }
             if($value->pending_withdrawal == NULL){
                $value->pending_withdrawal = 0;
            }
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);

        echo json_encode($data);
    }

    public function affiliate_user_request_list(Request $request)
    {
        if(!$this->is_admin) abort('403');

        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $display_columns = array("#","CHECKBOX",'id','name', 'website','affiliating_process_information', 'email', 'facebook_link','submission_date','status','action');
        $search_columns = array('name','email');
        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 2;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'user_id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;
        $query = DB::table('affiliate_requests')->select('affiliate_requests.*','users.name')->leftJoin('users','affiliate_requests.user_id','=','users.id');

        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table('affiliate_requests')->select('affiliate_requests.id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $total_result = $query->count();

        $i=0;
        $request_status_arr = array("3"=>__('pending'),"1"=>__("Rejected"),"2"=>__("Approved"));

        foreach ($info as $key => $value)
        {
            $pending_selected = $approved_selected = $rejected_selected =$hidden_approved=$hidden_rejected='';
            if($value->status =='3')
            {
                $pending_selected ='selected';
                $value->status = '<span class="badge rounded-pill bg-primary">'.__('Pending').'</span>';
            }
            else if($value->status =='2')
            {
                $approved_selected ='selected';
                $value->status = '<span class="badge rounded-pill bg-success">'.__('Approved').'</span>';
                $hidden_approved = 'hidden';
            }
            else if($value->status =='1')
            {
                $rejected_selected ='selected';
                $value->status = '<span class="badge rounded-pill bg-danger">'.__('Rejected').'</span>';
                $hidden_rejected = 'hidden';
            }
            $delete_url = route('delete-user-affiliate-request');
            $str ='';
            $action_str="&nbsp;&nbsp;<a href='#' class='btn btn-circle btn-outline-success request_status_change ".$hidden_approved."' status_value='2'data-id='".$value->id."' title='".__('Approve')."'>".'<i class="fa fa-check-circle"></i>'."</a>";
            $action_str.="&nbsp;&nbsp;<a href='#' class='btn btn-circle btn-outline-warning request_status_change ".$hidden_rejected."' status_value='1' data-id='".$value->id."' title='".__('Reject')."'>".'<i class="fa fa-times-circle"></i>'."</a>";
            $action_str.="&nbsp;&nbsp;<a href='".$delete_url."' data-table-name='table2' data-id='".$value->id."' class='delete-row btn btn-circle btn-outline-danger' title='".__('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->action = $action_str;

            $value->affiliating_process_information ='<div class="affiliating_process_information" info="'.$value->affiliating_process.'"><i class="fas fa-briefcase" style="font-size:18px"></i>
            </div>' ;
            $value->delete = $str;
            $value->facebook_link = null;
            $value->website = '<a target="_BLANK" href="'.$value->website.'"><i class="fas fa-globe"></i></a> '.' <a target="_BLANK" class="ps-2" href="';
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function delete_user_affiliate_request(Request $request){
       if(config('settings.is_demo') == '1') abort('403');
       if(!$this->is_admin) abort('403');
       $id = $request->id;
       $data = DB::table('affiliate_requests')->select('*')->where('id',$id)->first();
       $user_id = $data->user_id;
       $error_message = '';
        try {
            DB::beginTransaction();

            DB::table('affiliate_requests')->where('id',$id)->delete();

            DB::commit();
            $success = true;
        }
        catch (\Throwable $e){
            DB::rollBack();
            $success = false;
            $error_message = $e->getMessage();
        }
        if($success)
        {
            DB::table('users')->where('id',$user_id)->update(['is_affiliate'=>'0']);

            return response()->json(['error' => false,'message' => __('User Request has been deleted successfully.')]);
        }
        else return response()->json(['error' => true,'message' => __('Database error : ').$error_message]);
    }

    public function affiliate_request_status_change(Request $request)
    {
        if(config('settings.is_demo') == '1') abort('403');
        if(!$this->is_admin) abort('403');
        $status = $request->status;
        $request_id = $request->request_id;
        $request_user_info = DB::table('affiliate_requests')->select(['user_id','email'])->where('id',$request_id)->first();
        $request_user_id = $request_user_info->user_id;
        $email = $request_user_info->email;

        if($status =='2')
        {
            DB::table('users')->where('id',$request_user_id)->update(['is_affiliate'=>'1']);
        }
        else
        {
            DB::table('users')->where('id',$request_user_id)->update(['is_affiliate'=>'0']);
        }
        $info = DB::table("users")->where("id",$this->user_id)->select('name')->first();
        $name = $info->name;
        $message ='';
        $subject = __("Your Affiliate Request");
        if($status == '1'){
            $message =__("Your Request has been rejected,Please resubmit your request again");
        }
        else if($status=='2'){
            $message =__('Congratulations,Your affiliate request has been Aprroved');
        }
        $title = __("Hello").' '.$name;
        $response = $this->send_email($email,$message,$subject,$title);
        if(isset($response['error']) && $response['error']==1){
            return Response::json(['error'=>true,'message'=>$response['message']]);
        }
        else{

            DB::table('affiliate_requests')->where('id',$request_id)->update(['status'=>$status]);
            return Response::json(['error'=>false,'message'=>'Your request has been updated']);
        }

    }

    public function affiliate_withdrawal_request_status_change(Request $request)
    {
        if(config('settings.is_demo') == '1') abort('403');
        if(!$this->is_admin) abort('403');

        $status = $request->status;
        $request_id = $request->request_id;

        $request_user = DB::table('affiliate_withdrawal_requests')->select('user_id','requested_amount','status')->where('id',$request_id)->first();
        $request_user_id = $request_user->user_id;
        $requested_amount = $request_user->requested_amount;

        //no action needed for same status
        if($status == $request_user->status)
        {
            return response()->json(['message'=>'Your request has been updated']);
            exit;
        }

        if($status == '1')
        {
            $update_request = ['status'=>$status,'completed_at'=>date("Y-m-d H:i:s")];
        }
        else
        {
            $update_request = ['status'=>$status];
            $update_data = ['total_earn'=>DB::raw('total_earn+'.$requested_amount)];
            DB::table('users')->where(['id'=>$request_user_id])->update($update_data);
        }


        DB::table('affiliate_withdrawal_requests')->where('id',$request_id)->update($update_request);
        return response()->json(['message'=>'Your request has been updated']);
    }

    public function affiliate_withdrawal_requests_admin(Request $request)
    {
        if(!$this->is_admin) abort('403');

        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $display_columns = array("#","CHECKBOX",'id','name', 'total_earn','requested_amount', 'status', 'created_at','action');
        $search_columns = array('name');
        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 2;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'user_id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $query = DB::table('affiliate_withdrawal_requests')->select('affiliate_withdrawal_requests.*','users.name','users.total_earn')->leftJoin('users','affiliate_withdrawal_requests.user_id','=','users.id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();
        $total_result = $query->count();
        foreach ($info as $key => $value)
        {
            $pending_selected = $approved_selected = $rejected_selected ='';
            if($value->status =='0')
            {
                $pending_selected ='selected';
                $value->status = '<span class="badge rounded-pill bg-primary">'.__('Pending').'</span>';
            }
            else if($value->status =='1')
            {
                $approved_selected ='selected';
                $value->status = '<span class="badge rounded-pill bg-success">'.__('Approved').'</span>';
            }
            else if($value->status =='2')
            {
                $rejected_selected ='selected';
                $value->status = '<span class="badge rounded-pill bg-danger">'.__('Canceled').'</span>';
            }

            $delete_url = route('affiliate-withdrawal-request-delete-admin');
            $str ='';
            $str="&nbsp;&nbsp;<a href='#' class='btn btn-circle btn-outline-success request_status_admin' status_value='1'data-id='".$value->id."' title='".__('Approve')."'>".'<i class="fa fa-check-circle"></i>'."</a>";
            $str.="&nbsp;&nbsp;<a href='#' class='btn btn-circle btn-outline-warning request_status_admin' status_value='2' data-id='".$value->id."' title='".__('Cancel')."'>".'<i class="fa fa-times-circle"></i>'."</a>";
            $str.="&nbsp;&nbsp;<a href='".$delete_url."' data-table-name='table1' data-id='".$value->id."' class='delete-row btn btn-circle btn-outline-danger' title='".__('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->action = $str;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function affiliate_withdrawal_requests_delete_admin(Request $request)
    {
        if(config('settings.is_demo') == '1') abort('403');
        if(!$this->is_admin) abort('403');
        $id = $request->id;
        $request = DB::table('affiliate_withdrawal_requests')->where(['id'=>$id])->select(['status','requested_amount','user_id'])->first();
        if($request->status == '0')
        {
            $update_data = ['total_earn'=>DB::raw('total_earn+'.$request->requested_amount)];
            DB::table('users')->where(['id'=>$request->user_id])->update($update_data);
        }

        $success = DB::table('affiliate_withdrawal_requests')->where('id',$id)->delete();

        if($success)
            return response()->json(['error' => false,'message' => __('Withdrawal Request has been deleted successfully.')]);
        else return response()->json(['error' => true,'message' => __('Database error : ').$error_message]);
    }


    public function affiliate_send_email(Request $request)
    {
        $subject = $request->subject;
        $message = $request->message;
        $user_ids = $request->user_ids;
        $count=0;

        $info = DB::table("users")->whereIn("id",$user_ids)->select('email','name')->get();

        foreach($info as $member)
        {
            $email = $member->email;
            $name = $member->name;
            if($message=="" || $subject=="") continue;
            $title = __("Hello").' '.$name;

            if(isset($response['error']) && !$response['error']) $count++;

        }
        echo "<b> $count / ".count($info)." : ".__("Emails have been sent successfully.")."</b>";
    }

    public function affiliate_send_whatsapp_otp(Request $request){
        $email = trim($request->email);
        $generate_otp = random_int(100000, 999999);
        $info = DB::table("users")->where("id",$this->user_id)->select('name')->first();
        DB::table('affiliate_requests')->updateOrInsert(
            ['user_id'=>$this->user_id],
            ['otp'=>$generate_otp]
        );
        $name = $info->name;
        $subject = __("Your OTP code");
        $message = $generate_otp;
        $title = __("Hello").' '.$name;
        $response = $this->send_email($email,$message,$subject,$title);
        if(isset($response['error']) && $response['error']==1){
            return Response::json(['error'=>true,'message'=>$response['message']]);
        }
        return true;
    }

    public function affiliate_user_form_submission(Request $request)
    {
        $data = $update_data = [];
        $rules = [
            'name' => 'required|string|max:99',
            'mobile' => 'nullable|sometimes|string',
            'address' => 'nullable|sometimes|string',
            'status' => 'required|sometimes|boolean',
        ];

        if($request->is_overwritten=='1') {
            $rules['signup_amount'] = 'required_if:signup_commission,1';
            $rules['payment_type'] = 'required_if:payment_commission,1';
            $rules['fixed_amount'] = 'required_if:payment_type,fixed';
            $rules['percent_amount'] = 'required_if:payment_type,percentage';
        }

        if(!isset($request->affiliate_id)) {
            $rules['password'] = 'required|min:6|confirmed';
            $rules['email'] = 'required|string|email|max:99|unique:users';
        }
        else {
            $rules['password'] = 'nullable|sometimes|min:6|confirmed';
            $rules['email'] = 'required|email|max:99|unique:users,email,' . $request->affiliate_id;
        }

        $validateRules = Validator::make($request->all(),$rules);

        if($validateRules->fails()) {
            $message = $validateRules->errors()->first();
            return Response::json(['error'=>true,'message'=>$message]);
        }

        $currentdate = date("Y-m-d H:i:s");
        $data['name'] = trim($request->name);
        $data['email'] = trim($request->email);
        $data['mobile'] = trim($request->mobile);
        $data['is_affiliate'] = '1';

        if(!isset($request->affiliate_id)) {
            $data['parent_user_id'] = $this->user_id;
            $data['created_at'] = $currentdate;
            $data['password'] =  Hash::make($request->password);
        }
        else {
            if($request->password != NULL)
                $data['password'] =  Hash::make($request->password);
        }

        $is_commssion_overwritten = isset($request->is_overwritten) ? $request->is_overwritten:'0';


        if($is_commssion_overwritten=='1' && is_null($request->signup_commission) && is_null($request->is_payment)) {
            return Response::json(['error'=>true,'message'=>__('Please Select any of Commission Type.')]);
        }

        $transError = false;
        DB::beginTransaction();

        try {

            if (isset($request->affiliate_id) && !empty($request->affiliate_id)) {
                DB::table("users")->where(['id' => $request->affiliate_id, 'parent_user_id' => $this->user_id])->update($data);
                $insertId = $request->affiliate_id;
            } else {
                DB::table("users")->insert($data);
                $insertId = DB::getPdo()->lastInsertId();
            }

            if($is_commssion_overwritten=='1') {

                $signup_commission = $request->input("signup_commission") != "" ? $request->input("signup_commission"):'0';
                $signup_amount = $signup_commission != "0" ? trim($request->input("signup_amount")): "";
                $payment_commission = $request->input("is_payment") != ""? $request->input("is_payment"): '0';
                $payment_type = $request->input("payment_type");
                $fixed_amount = $payment_type == 'fixed' ? trim($request->input("fixed_amount")):"";
                $percent_amount = $payment_type == 'percentage'? trim($request->input("percent_amount")):"";
                $is_recurring = $request->input("is_recurring") != '' ? $request->input("is_recurring"):'0';
                if($payment_commission == '0') $payment_type = $percent_amount = $fixed_amount = '';

                $update_data['user_id'] = $insertId;
                $update_data['signup_commission'] = $signup_commission;
                $update_data['sign_up_amount'] = $signup_amount;
                $update_data['payment_commission'] = $payment_commission;
                $update_data['payment_type'] = $payment_type;
                $update_data['fixed_amount'] = $fixed_amount;
                $update_data['percentage'] = $percent_amount;
                $update_data['is_recurring'] = $is_recurring;

                DB::table("affiliate_payment_settings")->updateOrInsert(['user_id'=>$request->affiliate_id],$update_data);
            } else {
                DB::table("affiliate_payment_settings")->where("user_id",$insertId)->delete();
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $transError = $e->getMessage();
        }

        if(!$transError) {
            return Response::json(['error'=>false,'message'=>__('User has been created successfully.')]);
        } else {
            return Response::json(['error'=>true,'message'=>strip_tags($transError)]);

        }
    }

    public function affiliate_user_get_info(Request $request)
    {
        $affiliateid = $request->affiliateId;
        $select = ['users.id as affiliateid','users.name','users.email','users.mobile','users.address','users.status','users.user_type','affiliate_payment_settings.*','affiliate_payment_settings.id as individual_id'];
        $getInfo = (array) DB::table("users")
                    ->select($select)
                    ->leftJoin("affiliate_payment_settings","users.id","=","affiliate_payment_settings.user_id")
                    ->where(["users.id"=>$affiliateid,"users.is_affiliate"=>"1"])
                    ->first();
        echo json_encode($getInfo);
    }

    public function affiliate_commission_settings_set(Request $request)
    {
        if(config('settings.is_demo') == '1') abort('403');

        $rules = [
            'signup_amount_common' => 'required_if:signup_commission_common,1',
            'payment_type_common' => 'required_if:payment_commission_common,1',
            'fixed_amount_common' => 'required_if:payment_type_common,fixed',
            'percent_amount_common' => 'required_if:payment_type_common,percentage',
        ];

        $validateRules = Validator::make($request->all(),$rules);
        // go to config form page if validation wrong
        if($validateRules->fails())  {
            $errors = $validateRules->errors();
            if($errors->has('signup_amount_common')) {
                $message = __('Signup Amount is Required.');
            }
            else if($errors->has('payment_type_common')) {
                $message = __('Payment Type is Required.');
            }
            else if($errors->has('fixed_amount_common')) {
                $message = __('Fixed Amount is Required.');
            }
            else if($errors->has('percent_amount_common')) {
                $message = __('Percentage is Required.');
            }
            return Response::json(['error'=>true,'message'=>$message]);
        }

        $signup_commission = $request->input("signup_commission_common") != "" ? $request->input("signup_commission_common"):'0';
        $signup_amount = $signup_commission != "0" ? trim($request->input("signup_amount_common")): "";
        $payment_commission = $request->input("payment_commission_common") != ""? $request->input("payment_commission_common"): '0';
        $payment_type = $request->input("payment_type_common");
        $fixed_amount = $payment_type == 'fixed' ? trim($request->input("fixed_amount_common")):"";
        $percent_amount = $payment_type == 'percentage'? trim($request->input("percent_amount_common")):"";
        $is_recurring = $request->input("is_recurring_common") != '' ? $request->input("is_recurring_common"):'0';
        if($payment_commission == '0') $payment_type = $percent_amount = $fixed_amount = '';

        $update_data = [];
        $update_data['user_id'] = $this->user_id;
        $update_data['signup_commission'] = $signup_commission;
        $update_data['sign_up_amount'] = $signup_amount;
        $update_data['payment_commission'] = $payment_commission;
        $update_data['payment_type'] = $payment_type;
        $update_data['fixed_amount'] = $fixed_amount;
        $update_data['percentage'] = $percent_amount;
        $update_data['is_recurring'] = $is_recurring;

        DB::table("affiliate_payment_settings")->updateOrInsert(['user_id'=>$this->user_id],$update_data);
        return Response::json(['error'=>false,'message'=>__("Commission settings have been stored successfully.")]);
    }
}
