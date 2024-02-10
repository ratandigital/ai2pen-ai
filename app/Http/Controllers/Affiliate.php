<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

class Affiliate extends Home
{
	public function __construct()
	{
		$this->set_global_userdata(true,['Admin','Member'],['Manager']);
	}

	protected function affiliateViewcontroller($data=array())
	{
		$data = $this->set_module_ids($data);
	    if (!isset($data['body'])) return false;
	    if (!isset($data['iframe'])) $data['iframe'] = false;
	    if (!isset($data['load_datatable'])) $data['load_datatable'] = false;
	    $data['user_id'] = $this->user_id;
        $data['parent_parent_user_id'] = $this->parent_parent_user_id;
        $data['parent_package_id'] = $this->parent_package_id;
	    $data['parent_user_id'] = $this->parent_user_id;
	    $data['expired_date'] = $this->expired_date;
	    $data['is_admin'] = $this->is_admin;
	    $data['is_agent'] = $this->is_agent;
	    $data['agent_has_whitelabel'] = $this->agent_has_whitelabel;
	    $data['is_member'] = $this->is_member;
	    $data['is_manager'] = $this->is_manager;
	    $data['is_team'] = false;
	    $data['is_affiliate'] = $this->is_affiliate;
        $data['user_module_ids'] = $this->module_ids;
	    $data['is_rtl'] = $this->is_rtl ? '1' : '0';

	    $data['notifications'] = $this->get_notifications();
	    $data['route_name'] = Route::currentRouteName();
	    $data['get_selected_sidebar'] = get_selected_sidebar($data['route_name']);
	    $data['full_width_page_routes'] = full_width_page_routes();
	    return view($data['body'], $data);
	}

	public function affiliate_request(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $request_status = DB::table('affiliate_requests')->select('*')->where('user_id',$user->id)->first();
        if($request_status){
            if($request_status->status == 2){
               return  redirect(route('affiliate-user-self-settings'));
            }
            $data['request_status'] = $request_status;
        }
        $data['body'] = 'affiliate/affiliate_admin/request';
        $data['data'] = $user;
        return $this->viewcontroller($data);
    }

    public function affiliate_request_action(Request $request){
        $user = Auth::user();
        $user_id = $user->id;

        $rules =
            [
                'email' => 'required|string|email|max:99',
                'website' => 'required|string',
                'affiliating_process' => 'required|string',
                'otp'=>'required|string'
            ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return Redirect::back()->withInput()->withErrors($validator);
        }
        $data['user_id']=$user_id;
        $data['email'] = $request->email;
        $data['website'] = $request->website;
        $data['fb_link'] = '';
        $data['affiliating_process'] = $request->affiliating_process;
        $data['otp']= $request->otp;
        $data['status'] = '3';
        $data['submission_date'] = date("Y-m-d H:i:s");
        $otp_data = DB::table('affiliate_requests')->select('otp')->where('user_id',$user_id)->first();
        $send_otp = $otp_data->otp;
        if($send_otp ==$request->otp){
            DB::table('affiliate_requests')->where('user_id',$user_id)->update($data);
            session()->flash('message', __('Request Sent successfully'));
        }
        else{
            session()->flash('error_otp_message',__('Your OTP is incorrect') );
        }
        return  Redirect::back()->withInput();

    }

	public function index(Request $request)
	{
		$data =[];
		if(!isset($request->id)){
			$user_id = $this->user_id;
		}
		else
		{
			$user_id = $request->id;
		}
		$total_earn = DB::table('users')->select('total_earn')->where('id',$user_id)->first();
		$data['total_earn'] = $total_earn->total_earn ?? 0;
		$data['body'] = 'affiliate.affiliate_user.dashboard';
		$dashboard_selected_year = (int) session('dashboard_selected_year');
		if($dashboard_selected_year==0) $dashboard_selected_year = date('Y');
		$dashboard_selected_month = session('dashboard_selected_month');
		if($dashboard_selected_month=='') $dashboard_selected_month = date('m');
		$dashboard_selected_month_year = $dashboard_selected_year.'-'.$dashboard_selected_month;
		$previous_year = ($dashboard_selected_year-1);
		$to_date = date("Y-m-d");
		$from_date = date("Y-m-d", strtotime("$to_date - 30 days"));
		$month = date("m");
		$year = date("Y");
		$affiliate_id = $user_id;
		$total_users = DB::table('users')->where('under_which_affiliate_user',$user_id)->selectRaw('Count(id) as user')->first();
		$total_users =$total_users->user;

	    $affiliate_monthly_subscriber_data = DB::table('users')
        ->select(DB::raw('count(id) as `data`'),DB::raw("DATE_FORMAT(created_at, '%m') new_date"))
        ->where('under_which_affiliate_user','=',$user_id)->where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"),'=',$dashboard_selected_year)
        ->groupBy('new_date')->orderBy('new_date')->get();

       //last 30 days
		$earnings = DB::table('affiliate_earning_history')->where('affiliate_id','=',$user_id)->where('event_date','>=',$from_date)->where('event_date','<=',$to_date)->orderBy(DB::raw("(DATE_FORMAT(event_date,'%Y-%m-%d'))"),'asc')->get();

		$all_earnings = DB::table('affiliate_earning_history')->where('affiliate_id','=',$user_id)->orderBy(DB::raw("(DATE_FORMAT(event_date,'%Y-%m-%d'))"),'asc')->get();
		// monthly

		 $affliate_income_monthly = DB::table('affiliate_earning_history')
            ->select(DB::raw('SUM(amount) as `data`'),DB::raw("DATE_FORMAT(event_date, '%m') new_date"))
            ->where('affiliate_id','=',$user_id)->where(DB::raw("(DATE_FORMAT(event_date,'%Y'))"),'=',$dashboard_selected_year)
            ->groupBy('new_date')->orderBy('new_date')->get();

		$earning_chart_labels = array();
		$earning_chart_values = array();

		$from_date = strtotime($from_date);
		$to_date = strtotime($to_date);
		$array_month = array();
		$array_year = array();
		$payment_today=$payment_month=$payment_year=$payment_life=0;

		do
		{
		   $temp = date("Y-m-d",$from_date);
		   $temp2 = date("j M",$from_date);;
		   $earning_chart_values[$temp] = 0;
		   $earning_chart_labels[] = $temp2;
		   $from_date = strtotime('+1 day',$from_date);
		}
		while ($from_date <= $to_date);
		foreach ($earnings as $key => $value)
		{

		    $updated_at_formatted = date("Y-m-d",strtotime($value->event_date));

		    if(isset($earning_chart_values[$updated_at_formatted])) {
		        $earning_chart_values[$updated_at_formatted] += $value->amount;
		    }
		    else {
		        $earning_chart_values[$updated_at_formatted] = $value->amount;
		    }
		}
		$singup_earning = $payment_earning = $recurring_earning = 0;

		foreach ($all_earnings as $key1 => $value1)
		{

		    $mon = date("F",strtotime($value1->event_date));
		    $mon2 = date("m",strtotime($value1->event_date));

		    if(strtotime($value1->event_date) == $to_date) $payment_today += $value1->amount;

		    if(date("m",strtotime($value1->event_date)) == $month && date("Y",strtotime($value1->event_date)) == $year)
		    {
		         $payment_month += $value1->amount;
		         $event_date = date("jS M y",strtotime($value1->event_date));

		         if(!isset($array_month[$event_date])) $array_month[$event_date] = 0;
		         $array_month[$event_date] += $value1->amount;
		    }

		    if(date("Y",strtotime($value1->event_date)) == $year)
		    {
		         $payment_year += $value1->amount;
		         $payment_life += $value1->amount;
		         if(!isset($array_year[$mon])) $array_year[$mon] = 0;
		         $array_year[$mon] += $value1->amount;
		    }

		    if($value1->event == 'signup') {
		        $singup_earning = $singup_earning + $value1->amount;
		    }

		    if($value1->event == 'payment') {
		        $payment_earning = $payment_earning + $value1->amount;
		    }

		    if($value1->event == 'recurring') {
		        $recurring_earning = $recurring_earning + $value1->amount;
		    }
		}
		$data['month']= $month;
		$data['year']= $year;
		$data['earning_chart_labels'] = $earning_chart_labels;
		$data['earning_chart_values'] = array_values($earning_chart_values);
		$data['payment_today'] = $payment_today;
		$data['payment_month'] = $payment_month;
		$data['payment_year'] = $payment_year;
		$data['payment_life'] = $payment_life;
		$data['array_month'] = $array_month;
		$data['array_year'] = $array_year;
		$data['singup_earning'] = $singup_earning;
		$data['payment_earning'] = $payment_earning;
		$data['recurring_earning'] = $recurring_earning;
		$data['dashboard_selected_month']= $dashboard_selected_month;
		$data['dashboard_selected_year']= $dashboard_selected_year;
		$data['affliate_income_monthly']= $affliate_income_monthly;
		$data['total_users']= $total_users;
		$data['affiliate_monthly_subscriber_data']= $affiliate_monthly_subscriber_data;
		return $this->viewcontroller($data);



	}


	public function settings()
	{
		if(!in_array($this->parent_user_id,[1])) abort('403');

		$affiliateTrack = '?aff_track=' . bin2hex($this->user_id);
		$aff_link = url('/') . $affiliateTrack;
		$data['body'] = "affiliate.affiliate_user.settings";
		$data['aff_link'] = $aff_link;
		$paymentCommissions = [];
		$payment_type = __("Not Set");
		$payment_amount = $signup_amount = $fixed_amount = $percentage = "0";
		$info = (array) DB::table("users")
				->select(['users.id as userid','users.parent_user_id','name','email','status','is_affiliate','affiliate_payment_settings.*','affiliate_payment_settings.id as individual_id'])
				->leftJoin("affiliate_payment_settings","users.id","=","affiliate_payment_settings.user_id")
				->where(["users.id"=>$this->user_id,"users.parent_user_id"=>$this->parent_user_id,"users.is_affiliate"=>"1"])
				->first();

		$signup_amount = isset($info['signup_commission']) && $info['signup_commission']=='1' ? $info['sign_up_amount']:'0';
		$payment_type = !empty($info['payment_type']) ? $info['payment_type']:__("Not Set");
		if(isset($info['payment_type']) && $info['payment_type']=="fixed") $payment_amount = $info['fixed_amount'];
		else if(isset($info['payment_type']) && $info['payment_type']=="percentage") $payment_amount = $info['percentage'];

		$currency = (array) $this->get_payment_config_parent($this->parent_user_id,'currency');
		$data['curency_icon'] = $currency['currency'] ?? "USD";

		if(empty($info['individual_id'])) {
			$paymentCommissions = (array) DB::table("affiliate_payment_settings")
									->where("user_id",$this->parent_user_id)
									->first();

			$signup_amount = isset($paymentCommissions['signup_commission']) && $paymentCommissions['signup_commission']=='1' ? $paymentCommissions['sign_up_amount']:'0';
			if(!empty($paymentCommissions)) {
				$payment_type = !empty($paymentCommissions['payment_type']) ? $paymentCommissions['payment_type']:__("Not Set");
				if(isset($paymentCommissions['payment_type']) && $paymentCommissions['payment_type']=="fixed")
					$payment_amount = $paymentCommissions['fixed_amount'];
				else if(isset($paymentCommissions['payment_type']) && $paymentCommissions['payment_type']=="percentage")
					$payment_amount = $paymentCommissions['percentage'];
			}
		}

		$data['affiliate_info'] = $info;
		$data['paymentCommissions'] = $paymentCommissions;
		$data['signup_amount'] = $signup_amount;
		$data['payment_commission_type'] = $payment_type;
		$data['payment_commission_amount'] = $payment_amount;

		return $this->viewcontroller($data);
	}

	public function withdrawal_methods()
	{
		if(!in_array($this->parent_user_id,[1])) abort('403');

		$data['body'] = "affiliate.affiliate_user.withdrawal-methods";
		$data['load_datatable'] = true;
		return $this->viewcontroller($data);
	}

	public function withdrawal_methods_data(Request $request)
	{
		$search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
		$display_columns = array("#",'id','payment_type','created_at','actions');
		$search_columns = array('payment_type', 'paypal_email','bank_acc_no');

		$page = isset($request->page) ? intval($request->page) : 1;
		$start = isset($request->start) ? intval($request->start) : 0;
		$limit = isset($request->length) ? intval($request->length) : 10;
		$sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 1;
		$sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'user_id';
		$order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
		$order_by=$sort." ".$order;

		$table="affiliate_withdrawal_methods";
		$query = DB::table($table)->where('user_id',$this->user_id);
		if ($search_value != '')
		{
		    $query->where(function($query) use ($search_columns,$search_value){
		        foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
		    });
		}
		$info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

		$query = DB::table($table)->where('user_id',$this->user_id);
		if ($search_value != '')
		{
		    $query->where(function($query) use ($search_columns,$search_value){
		        foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
		    });
		}
		$total_result = $query->count();


		for ($i=0; $i < count($info) ; $i++)
		{
		    $payment_type = $info[$i]->payment_type;
		    if($info[$i]->created_at != null)
		        $info[$i]->created_at = "<div style='min-width:100px !important;'>".date("M j, Y",strtotime($info[$i]->created_at))."</div>";

		    if($info[$i]->payment_type == 'paypal') {
		        $info[$i]->payment_type = "<div class='text-center'>".__('PayPal')."</div>";
		        $details = $info[$i]->paypal_email;
		    }

		    if($info[$i]->payment_type == 'bank_account') {
		        $info[$i]->payment_type = "<div class='text-center'>".__('bKash')."</div>";
		        $details = nl2br(htmlspecialchars($info[$i]->bank_acc_no,ENT_QUOTES));
		    }

		    $info[$i]->actions = "<div style='min-width:150px'><a href='#' title='".__("See details")."' class='btn btn-circle btn-outline-primary method_details' table_id='".$info[$i]->id."' method_name='".$payment_type."' details='".$details."'><i class='fas fa-eye'></i></a>&nbsp;&nbsp;";

		    $info[$i]->actions .= "<a href='#' title='".__("Edit Method")."' class='btn btn-circle btn-outline-warning edit_method' table_id='".$info[$i]->id."'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;";

		    $info[$i]->actions .= "<a href='#' title='".__("Delete Method")."' class='btn btn-circle btn-outline-danger delete_method' table_id='".$info[$i]->id."'><i class='fa fa-trash-alt'></i></a></div>
		        <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";


		}

		$data['draw'] = (int)$_POST['draw'] + 1;
		$data['recordsTotal'] = $total_result;
		$data['recordsFiltered'] = $total_result;
		$data['data'] = array_format_datatable_data($info, $display_columns ,$start);
		echo json_encode($data);
	}


	public function new_method(Request $request)
	{
	    $method_type = $request->input('method_type');
	    $paypal_email = trim(strip_tags($request->input('paypal_email')));
	    $bank_acc_no = $method_type=="bank_account" ? trim(strip_tags($request->input('bank_acc_no'))):"";
	    if (!filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
	            return Response::json(['error'=>true,'message'=>__('Please enter a valid PayPal email.')]);
	        }
	    $affiliate_id = $this->user_id;

	    $existing_data = DB::table('affiliate_withdrawal_methods')->where(['user_id'=>$affiliate_id,'payment_type'=>$method_type])->select('id')->first();
	    if(isset($existing_data->id) && $existing_data->id != 0)
	    {
	    	return Response::json(['error'=>true,'message'=>__('You already have this payment method. Please update existing payment method instead of adding same payment method')]);
	    }

	    $insert_data = [
	        'user_id' => $affiliate_id,
	        'payment_type' => $method_type,
	        'paypal_email' => $paypal_email,
	        'bank_acc_no' => $bank_acc_no,
	        'created_at' => date("Y-m-d H:i:s")
	    ];

	    if(DB::table("affiliate_withdrawal_methods")->insert($insert_data)) {
	        return Response::json(['error'=>false,'message'=>__('Method has been created successfully.')]);
	    } else {
	        return Response::json(['error'=>true,'message'=>__('Something went wrong during create method, please try once again.')]);
	    }
	}

	public function get_method_info(Request $request)
	{
	    $table_id = $request->input('table_id');
	    $affiliate_id = $this->user_id;

	    if($table_id == '' || $table_id == 0) exit;

	    $get_method_info = DB::table("affiliate_withdrawal_methods")->where(["id"=>$table_id,"user_id"=>$affiliate_id])->first();

	    $method_type = $get_method_info->payment_type;
	    $paypal_email = $get_method_info->paypal_email;
	    $bank_acc_no = $get_method_info->bank_acc_no;

	    $edit_paypal_div = $edit_bank_div = 'none';
	    $paypal_selected = $bank_selected = '';

	    if($method_type == 'paypal') {
	        $edit_paypal_div = "block";
	        $paypal_selected = 'selected';
	    }

	    if($method_type == 'bank_account') {
	        $edit_bank_div = "block";
	        $bank_selected = 'selected';
	    }

	    echo $html = '
	        <div class="row">
	            <div class="col-12">
	                <form action="#" enctype="multipart/form-data" id="witdrawalMethod_edit_form" method="post">
	                    <input type="hidden" name="table_id" id="table_id" value='.$table_id.'>
	                    <div class="row">

	                        <div class="col-12">
	                            <div class="form-group">
	                                <label>'.__('Method').'</label>
	                                <select name="method_type" id="edit_method_type" class="form-control select2" style="width:100%;">
	                                    <option value="">'.__('Select Method').'</option>
	                                    <option value="paypal" '.$paypal_selected.'>'.__('PayPal').'</option>
	                                    <option value="bank_account" '.$bank_selected.'>'.__('bKash').'</option>
	                                </select>
	                            </div>
	                        </div>

	                        <div class="col-12" id="edit_paypal_email_div" style="display: '.$edit_paypal_div.';">
	                            <div class="form-group">
	                                <label>'.__('PayPal Email').'</label>
	                                <input type="email" class="form-control" name="paypal_email" id="edit_paypal_email" value='.$paypal_email.'>

	                            </div>
	                        </div>

	                        <div class="col-12" id="edit_bank_acc_div" style="display: '.$edit_bank_div.';">
	                            <div class="form-group">
	                                <label>'.__('Details').'</label>
	                                <textarea class="form-control" name="bank_acc_no" id="edit_bank_acc_no" style="height: 100px !important;">'.$bank_acc_no.'</textarea>
	                            </div>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
	        <script>$("#edit_method_type").select2();</script>';
	}

	public function update_method_info(Request $request)
	{
	    $table_id = $request->input('table_id');
	    $method_type = $request->input('method_type');
	    $paypal_email = $method_type=="paypal" ? trim(strip_tags($request->input('paypal_email'))):"";

	    if (!filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
	            return Response::json(['error'=>true,'message'=>__('Please enter a valid PayPal email.')]);
	    }
	    $bank_acc_no = $method_type=="bank_account" ? trim(strip_tags($request->input('bank_acc_no'))):"";
	    $affiliate_id = $this->user_id;

	    $update_data = [
	        'payment_type' => $method_type,
	        'paypal_email' => $paypal_email,
	        'bank_acc_no' => $bank_acc_no,
	        'updated_at' => date("Y-m-d H:i:s")
	    ];

	    if(DB::table("affiliate_withdrawal_methods")->where(['id'=>$table_id,'user_id'=>$affiliate_id])->update($update_data)) {
	        return Response::json(['error'=>false,'message'=>__('Method has been updated successfully.')]);
	    } else {
	        return Response::json(['error'=>true,'message'=>__('Something went wrong during create method, please try once again.')]);
	    }
	}

	public function delete_withdrawal_method(Request $request)
	{
	    $table_id = $request->input("id",true);
	    if($table_id == '' || $table_id == 0) exit;

	    if(DB::table("affiliate_withdrawal_methods")->where(['id'=>$table_id,'user_id'=>$this->user_id])->delete()) {
	        echo "1";
	    } else {
	        echo "0";
	    }
	}


	public function withdrawal_requests(Request $request)
	{
		if(!in_array($this->parent_user_id,[1])) abort('403');

	    $per_page = 5;
	    $search_value='';
	    // set per_page and search_value from user_submission
	    if (isset($request->rows_number) || isset($request->search_value)) {

	        $per_page = $request->rows_number;
	        $search_value = $request->search_value;

	        session(['request_per_page'=>$per_page]);
	        session(['request_search_value'=>$search_value]);
	    }

	    // set session so that pagination can get proper per_page & search_value
	    if (session('request_per_page'))
	        $per_page = session('request_per_page');

	    if (session('request_search_value'))
	        $search_value = session('request_search_value');
	    if(isset($search_value))
	        $where['where'] = array('affiliate_id' => session("affiliate_userid"),'request_status'=>$search_value);

	    $total_withdrawal_requests = DB::table('affiliate_withdrawal_requests')->select('*')->where('user_id',$this->user_id)->get();

	    if ($per_page == 'all')
	        $per_page = count($total_withdrawal_requests);

	    /* set cinfiguration for pagination */

	    $limit = $per_page;

	    $new_all = [];
	    $where3 = ['user_id' => $this->user_id,'status'=>'0'];
	    $where4 = ['user_id' => $this->user_id,'status'=>'1'];
	    if(isset($search_value))
	        $where2 = ['user_id' => session("affiliate_userid"),'status'=>$search_value];
	    $withdrawal_request_lists = DB::table('affiliate_withdrawal_requests')->select('affiliate_withdrawal_requests.*','affiliate_withdrawal_methods.payment_type','affiliate_withdrawal_methods.paypal_email','affiliate_withdrawal_methods.bank_acc_no')->leftJoin('affiliate_withdrawal_methods','affiliate_withdrawal_requests.method_id','=','affiliate_withdrawal_methods.id');


	    if($search_value != ''){
	    	$withdrawal_request_lists= $withdrawal_request_lists->where('status',$search_value);
	    }

	    $withdrawal_request_lists= $withdrawal_request_lists->where('affiliate_withdrawal_requests.user_id',$this->user_id)->limit($limit)->orderBy('affiliate_withdrawal_requests.id', 'DESC')->paginate($per_page);
	    $withdrawal_request_pending = DB::table('affiliate_withdrawal_requests')->select("*")->where($where3)->get()->toArray();
	    $withdrawal_request_completed = DB::table('affiliate_withdrawal_requests')->select("*")->where($where4)->get()->toArray();

	    // calculating total pending money of affiliator
	    $pendingData = array_map(function ($value) {
	        return $value->requested_amount;
	    }, $withdrawal_request_pending);
	    $finalData = array_sum($pendingData);;
	    // calculating total pending money of affiliator
	    $completeData = array_map(function ($value) {
	        return $value->requested_amount;
	    },$withdrawal_request_completed);
	    $finalData2 = array_sum($completeData);
	    $total_earn = DB::table("users")->select('name','profile_pic','total_earn')->where('id',$this->user_id)->get();
	    for ($i=0; $i < count($withdrawal_request_lists); $i++) {
	        $status = $withdrawal_request_lists[$i]->status;
	        if($status == '0') {
	            $withdrawal_request_lists[$i]->request_status_icon = '<small class="text-warning">'.__('Pending').'</small>';

	        } else if($status == '1') {
	            $withdrawal_request_lists[$i]->request_status_icon = '<small class="text-success">'.__('Approved').'</small>';

	        } else if($status == '2') {
	            $withdrawal_request_lists[$i]->request_status_icon = '<small class="text-danger">'.__('Canceled').'</small>';
	        }

	        if($withdrawal_request_lists[$i]->payment_type == 'paypal') {
	            $withdrawal_request_lists[$i]->method_id = "PayPal <span data-toggle='tooltip' title='".__('See details')."' class='text-primary pointer method_details' method_name='PayPal' details='".nl2br(htmlspecialchars($withdrawal_request_lists[$i]->paypal_email,ENT_QUOTES))."'><i class='fas fa-info-circle'></i></span>";
	            $withdrawal_request_lists[$i]->icon = '<i class="fab fa-paypal text-primary"></i> ';
	            $withdrawal_request_lists[$i]->background = 'var(--blue)';
	            $withdrawal_request_lists[$i]->payment_type = 'PayPal';
	        } else if($withdrawal_request_lists[$i]->payment_type == 'bank_account') {
	            $withdrawal_request_lists[$i]->method_id = "bKash <span data-toggle='tooltip' title='".__('See details')."' class='text-primary pointer method_details' method_name='bKash' details='".nl2br(htmlspecialchars($withdrawal_request_lists[$i]->bank_acc_no,ENT_QUOTES))."'><i class='fas fa-info-circle'></i></span>";
	            $withdrawal_request_lists[$i]->icon = '<i class="fas fa-university text-primary"></i>';
	            $withdrawal_request_lists[$i]->background = '#c765ab';
	            $withdrawal_request_lists[$i]->payment_type = "bKash";
	        }

	        if($withdrawal_request_lists[$i]->created_at != null) {
	            $withdrawal_request_lists[$i]->created_at = $withdrawal_request_lists[$i]->created_at;
	            $withdrawal_request_lists[$i]->created_at_ago = convert_datetime_to_phrase($withdrawal_request_lists[$i]->created_at,true);
	        }

	        if($withdrawal_request_lists[$i]->completed_at != null) {
	            $withdrawal_request_lists[$i]->completed_at = date("M j, Y H:i:s",strtotime($withdrawal_request_lists[$i]->completed_at));
	            $withdrawal_request_lists[$i]->completed_at_ago = convert_datetime_to_phrase($withdrawal_request_lists[$i]->completed_at,true);
	        }
	        else {
	            $withdrawal_request_lists[$i]->completed_at_ago = __('0 sec Ago');
	            $withdrawal_request_lists[$i]->completed_at = __('Not yet');
	        }

	    }
	    $config_data=DB::table("settings_payments")->select('*')->get();
	    $currency=isset($config_data[0]->currency)?$config_data[0]->currency:"USD";
	    $data["currency"] = $currency;
	    $data['method_info'] =DB::table("affiliate_withdrawal_methods")->select('*')->where('user_id',$this->user_id)->get();

	    $data['count_method_info'] = count($data['method_info']);
	    $data['page_title'] = __("Withdrawal Requests");
	    $data['withdrawal_requests'] = $withdrawal_request_lists;
	    $data['per_page'] = ($per_page == count($total_withdrawal_requests)) ? 'all' : $per_page;
	    $data['search_value'] = $search_value;
	    $data['total_earned'] = $total_earn[0]->total_earn;
	    $data['profile_img'] = $total_earn[0]->profile_pic;
	    $data['pending_money'] = $finalData;
	    $data['transfered_money'] = $finalData2;
	    $data['body'] = "affiliate/affiliate_user/withdrawal-requests";
	    return $this->viewcontroller($data);
	}

	public function get_requests_info(Request $request){

		$table_id = $request->table_id;
		$affiliate_id =$this->user_id;

		if($table_id == "" || $table_id == 0) exit;

		$requests_info = DB::table('affiliate_withdrawal_requests')->select("*")->where('id',$table_id)->first();
		$get_method = DB::table("affiliate_withdrawal_methods")->select('*')->where('user_id',$affiliate_id)->get();

		$str ='';
		foreach ($get_method as $value) {
			$selected = $requests_info->method_id == $value->id ? 'selected' : '';
            if($value->payment_type == 'paypal') {
                    $str=$str.'<option '.$selected.'  value="'.$value->id.'"> PayPal : '.$value->paypal_email.'</option>';
                }

            else if($value->payment_type == 'bank_account') {
               $str=$str.'<option '.$selected.' select2 value="'.$value->id.'"> bKash : '.$value->bank_acc_no.'</option>';
            }
        }
		$requests_info->option_str = $str;

		echo json_encode($requests_info);
	}

	public function issue_new_request(Request $request)
	{
		if(!in_array($this->parent_user_id,[1])) abort('403');

		$responses = [];
		$withdrawal_requests =$request->withdrawal_account;
		$requested_amount =$request->requested_amount;
		$submit_action =$request->submit_action;
		$tableId =$request->tableId;
		$previous_amount =$request->previous_amount;
		$affiliate_id = $this->user_id;
		$affiliate_total_money = DB::table("users")->select('total_earn')->where('id',$this->user_id)->first();
		$affiliator_total_earn = $affiliate_total_money->total_earn;

		$config_data=DB::table("settings_payments")->select('*')->get();
		$currency=isset($config_data[0]->currency)?$config_data[0]->currency:"USD";

		if($affiliator_total_earn == 0) {
		    $responses['status'] = '0';
		    $responses['response_error'] = __("Sorry, you can not issue new request because you have 0 balance.");
		    echo json_encode($responses);exit;
		}

		if($requested_amount < 50) {
		    $responses['status'] = '0';
		    $responses['response_error'] = __("Please provide a valid amount. You are allowed to withdraw minimum $50");
		    echo json_encode($responses); exit;
		}

		if($requested_amount > $affiliator_total_earn) {
		    $responses['status'] = '0';
		    $responses['response_error'] = __("You can not make request more than your total earn.");
		    echo json_encode($responses);exit;
		}


		if($submit_action == 'add') {

		    $insert_data = [
		        'user_id' => $affiliate_id,
		        'method_id' => $withdrawal_requests,
		        'requested_amount' => $requested_amount,
		        'status' => '0',
		        'created_at' => date("Y-m-d H:i:s")
		    ];

		    if(DB::table('affiliate_withdrawal_requests')->insert($insert_data)) {

		    	$update_data = ['total_earn'=>DB::raw('total_earn-'.$requested_amount)];
	            DB::table('users')->where(['id'=>$affiliate_id])->update($update_data);

		        $responses['status'] = '1';
		        $responses['response_success'] = __("Your Request has been issued successfully.");
		        $affliate_data = DB::table('users')->select('email','name')->where('id',$affiliate_id)->first();
		        $affiliate_email = $affliate_data->email;
		        $affiliate_name = $affliate_data->name;

		        // sending email to admin to notify

		        $to_email = DB::table('users')->select('email')->where('id',$this->parent_user_id)->first();
		        $to = $to_email->email;
		        $from = $affiliate_email;
		        $mask = config('app.name');
		        $html = 1;
		        $productname = config('app.name');

		        $name = __('New Withdrawal Request');
		        $sub = __('#APP_NAME# | Affiliate Withdrawal Request');
		        $subject = str_replace('#APP_NAME#',$productname,$sub);
		        $message_text = __('<p>Dear Admin,<br>
									A withdrawal request has been made by an affiliate. Please check the below information of the request.</p>
									<ul>
									<li>Affiliator Name : #AFFILIATOR_NAME#</li>
									<li>Affiliator Email : #AFFILIATOR_EMAIL#</li>
									<li>Requested Amount : #REQUESTED_AMOUNT#</li>
									</ul>
							');
		        $message =  str_replace(array("#AFFILIATOR_NAME#","#AFFILIATOR_EMAIL#","#REQUESTED_AMOUNT#"),array($affiliate_name,$affiliate_email,$requested_amount),$message_text);
		        $title = __("Hello").' '.$name;

		        $this->send_email($to, $message, $subject, $mask);

		        echo json_encode($responses);exit;
		    }
		}
		else if($submit_action == 'edit') {

		    if($requested_amount > $affiliator_total_earn) {
		        $responses['status'] = '0';
		        $responses['response_error'] = __("You can not make request more than your total earn.");
		        echo json_encode($responses);exit;
		    }

		    $update_data = [
		        'user_id' => $affiliate_id,
		        'method_id' => $withdrawal_requests,
		        'requested_amount' => $requested_amount,
		        'status' => '0',
		    ];

		    $where2 = ['id'=>$tableId,'user_id'=>$affiliate_id];
		    if(DB::table("affiliate_withdrawal_requests")->where($where2)->update($update_data)) {

		    	$update_data = ['total_earn'=>DB::raw('total_earn+'.$previous_amount)];
	            DB::table('users')->where(['id'=>$affiliate_id])->update($update_data);
	            $update_data = ['total_earn'=>DB::raw('total_earn-'.$requested_amount)];
	            DB::table('users')->where(['id'=>$affiliate_id])->update($update_data);

		        $responses['status'] = '1';
		        $responses['response_success'] = __("Your Request has been updated successfully.");

		        echo json_encode($responses);exit;
		    }
		    else{
		    	$responses['status'] = '2';
		    	$responses['response_edit_fail'] = __("Don't need to resubmit the request with same value");
		    	echo json_encode($responses);exit;
		    }

		}
	}

	public function delete_withdrawal_request(Request $request){
		$id = $request->id;
		$error_message = '';
        try {
            DB::beginTransaction();
            $request_info = DB::table('affiliate_withdrawal_requests')->where('id',$id)->first();
            $user_id = $request_info->user_id;
            $requested_amount = $request_info->requested_amount;
            $status = $request_info->status;

            DB::table('affiliate_withdrawal_requests')->where('id',$id)->delete();

            if($status == '0')
            {
	            $update_data = ['total_earn'=>DB::raw('total_earn+'.$requested_amount)];
	            DB::table('users')->where(['id'=>$user_id])->update($update_data);
            }

            DB::commit();
            $success = true;
        }
        catch (\Throwable $e){
            DB::rollBack();
            $error_message = $e->getMessage();
        }
        if($success)
        {
            return response()->json(['error' => false,'message' => __('Withdrawal Request has been deleted successfully.')]);
        }
        else return response()->json(['error' => true,'message' => __('Database error : ').$error_message]);
	}
}
