<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 use App\Services\OpenAiServiceInterface;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use CURLFILE;
use Owenoj\LaravelGetId3\GetId3;

class Template extends Home{
    public function __construct(OpenAiServiceInterface $openai_service){
        $this->set_global_userdata(true);
        $this->openai = $openai_service;
    }

    public function tools($group_slug,$template_slug,$search_id=null){
        if($this->is_member && !$this->is_manager && config('app.force_email_verify')=='1' && !auth()->user()->email_verified_at){
            return redirect(route('verification.notice'));
        }
        
        $template_group_data = DB::table("ai_template_groups")->where(['group_slug'=>$group_slug,"status"=>"1"])->first();
        $template_data = DB::table("ai_templates")->where(['template_slug'=>$template_slug,"status"=>"1"])->first();
        if(empty($template_group_data) || empty($template_data)) abort(404);

        if($template_data->api_group=='image') $module_id = $this->module_id_image;
        else if($template_data->api_group=='audio') $module_id = $this->module_id_audio;
        else $module_id = $this->module_id_token;
        if(!has_module_access($module_id,$this->module_ids,$this->is_admin,$this->is_manager)) return redirect(route('pricing-plan'));

        if(!has_module_action_access($module_id,[1,2],$this->team_access,$this->is_manager)) return redirect(route('pricing-plan'));

        $data = array('body' => 'openai/tools/search-panel','load_datatable'=>true);
        if(!empty($search_id)){
            $data['search_data'] = DB::table("ai_search_contents")->where('id',$search_id)->first();
        }
        else $data['search_data'] = null;
        $data['template_group_data']=$template_group_data;
        $data['template_data']=$template_data;
        $data['load_footer']=false;       
        return $this->viewcontroller($data);
    }

    public function tools_action(){

        if(config('app.is_demo')=='1' && config('app.is_restricted')=='1' && $this->is_admin)
        return response()->json(['status' => '0','message' => __('Due to high traffic, content generation has been temporarily disabled for this demo admin account. However, you can still access previously generated content through the History menu. Alternatively, you may sign up as a member user to generate your desired content and explore the features of your own account without any limitations.')]);

        if($this->is_member && !$this->is_manager && config('app.force_email_verify')=='1' && !auth()->user()->email_verified_at){
            return redirect(route('verification.notice'));
        }

        $id = request()->id;
        $ai_template_id = request()->ai_template_id;
        $group_slug = htmlspecialchars_decode(request()->group_slug);
        $template_slug = htmlspecialchars_decode(request()->template_slug);
        $document_name = request()->document_name;
        $media_url = request()->media_url;
        $media_duration = request()->media_duration;
        $language = request()->language;
        $temperature = request()->temperature;
        $frequency_penalty = request()->frequency_penalty;
        $presence_penalty = request()->presence_penalty;
        $output_size = request()->output_size;
        $max_tokens = request()->max_tokens;
        $variation = request()->variation;
        $param_name = request()->paramName;
        $param_val = request()->paramValue;

        $template_data = DB::table("ai_templates")->where(['id'=>$ai_template_id])->first();
        $success_message = __('Generation process has been completed successfully.');
        $req = 1;
        $insert_log = 1;
        if($template_data->api_group=='image') {
            $module_id = $this->module_id_image;
            $req = $insert_log = $variation;
            if($req=='' || $req==0) $req = 1;
        }
        else if($template_data->api_group=='audio') {
            $module_id = $this->module_id_audio;
            $req = $insert_log = $media_duration;
        }
        else {
            $module_id = $this->module_id_token;
            $req = $variation;
            if($req=='' || $req==0) $req = 1;
            $req = $req*16;
            $insert_log = $req;
        }

        $status=$this->check_usage($module_id,$req);
        if(!has_module_access($module_id,$this->module_ids,$this->is_admin,$this->is_manager)) {
            return response()->json(['status'=>'0','message'=>__('Access Forbidden')]);
        }
        else if(!has_module_action_access($module_id,[1,2],$this->team_access,$this->is_manager)) {
            return response()->json(['status'=>'0','message'=>__('Access Forbidden')]);
        }
        else if($status=="3") {
            return response()->json(['status'=>'0','message'=>__('Module limit has been exceeded.')]);
        }

        $media_path = '';
        if(!empty($media_url)){
            $filename = pathinfo($media_url, PATHINFO_BASENAME);
            $media_path =storage_path().'/app/public/tools/'.$this->user_id.'/'.$filename;
        }

        $prompt_fields_data = [];
        $prompt_fields_string = "";
        $missing_param = false;


        $about_text = rtrim($template_data->about_text,'.');
        if(!empty($language)) $about_text.=' in '.$language.' language';

        if(!empty($param_name) && !empty($param_val)){
            foreach ($param_name as $key=>$value){
                if(empty($value) || empty($param_val[$key])){
                    $missing_param = true;
                    break;
                }
                $prompt_fields_data[$value] = $param_val[$key];
                $prompt_variable="{{".str_replace(" ", "-", $value)."}}";
                $final_prompt=str_replace($prompt_variable, $param_val[$key], $about_text);
                $prompt_fields_string .= '\n'.$value.' : '.$param_val[$key];
            }
        }
 
        if($missing_param) return response()->json(['status'=>'0','message'=>__('Please fill up all the required fields')]);

      
        $api_data_set = [];
        if(!in_array($template_data->api_type,['images/variations','audio/transcriptions','audio/translations','chat/completions'])){

            if(strpos($about_text, "{{") !== false){
               $api_data_set["prompt"] = $final_prompt;
            }

            else
            $api_data_set["prompt"] = $about_text.$prompt_fields_string;
        }

        if(!blank($template_data->model)) $api_data_set['model'] = (string) $template_data->model;
        if(!blank($max_tokens)) $api_data_set['max_tokens'] = (int) $max_tokens;
        if(!blank($temperature)) $api_data_set['temperature'] = (float) $temperature;
        if(!blank($frequency_penalty)) $api_data_set['frequency_penalty'] = (float) $frequency_penalty;
        if(!blank($presence_penalty)) $api_data_set['presence_penalty'] = (float) $presence_penalty;
        if(!blank($variation)) $api_data_set['n'] = (int) $variation;
        if(!blank($output_size)) $api_data_set['size'] = (string) $output_size;
        $api_data_set['user'] = 'user-'.$this->user_id;

        if($template_data->api_group=='chat'){
            $api_data_set['messages']=array(array("role"=>"system","content"=>$about_text),array("role"=>"user","content"=>$prompt_fields_string));
        }

        $json_param = true;
        $content_type = 'application/json';
        if(!empty($media_path)){
            $content_type = 'multipart/form-data';
            $type = $template_data->api_group =='image' ? 'image' : 'file';
            $api_data_set[$type] = new CURLFILE($media_path);
            $json_param = false;
        }

        $this->openai->api_key = config('api.openai-key');
        $response = $this->openai->call_api($template_data->api_type,$api_data_set,$content_type,$json_param);
        $response_decoded = json_decode($response);
        $usage_log = $response_decoded->usage->total_tokens ?? $insert_log;

        $insert_data = [
            'ai_template_id' => $ai_template_id,
            'user_id' => $this->user_id,
            'document_name' => $document_name,
            'media_url' => $media_url,
            'media_duration' => $media_duration,
            'language' => $language,
            'temperature' => $temperature,
            'frequency_penalty' => $frequency_penalty,
            'presence_penalty' => $presence_penalty,
            'output_size' => $output_size,
            'max_tokens' => $max_tokens,
            'variation' => $variation,
            'prompt_fields_data' => json_encode($prompt_fields_data),
            'response' => $response,
            'tokens' => $usage_log,
            'searched_at' => date('Y-m-d H:i:s'),
        ];

        if(DB::table('ai_search_contents')->insert($insert_data)){
            $id = DB::getPdo()->lastInsertId();
            $this->insert_usage_log($module_id,$usage_log,$this->user_id);
            if(!empty($media_path)) File::delete($media_path);
            return response()->json(['status'=>'1','message'=>$success_message,'redirect'=>route('tools',['template_slug'=>$template_slug,'group_slug'=>$group_slug,'search_id'=>$id])]);
        }
        else return response()->json(['status'=>'0','message'=>__('Something went wrong.')]);
    }

    public function download_text(){
        $filename = request()->filename ?? 'download';
        $filename .= '.txt';
        $text = request()->download_text;
        if(empty($text)) abort('500');
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-type: application/txt');
        echo $text;
        exit;
    }

    public function download_file(){
        $filename = request()->filename ?? 'download';
        $url = request()->file_url;
        $type = request()->type ?? 'image';
        if(empty($url)) abort(404);
        if(@getimagesize($url)){            
            $data = file_get_contents($url);
            $file_extension = pathinfo($url, PATHINFO_EXTENSION);
            if(empty($file_extension)) $file_extension = 'png';
            $file_name = $filename .'.'.$file_extension;
            return response($data, 200)
                ->header('Content-Type', $type.'/' . $file_extension)
                ->header('Content-Disposition', 'attachment; filename="' . $file_name . '"');
        }
        else abort(404);
    }

    public function upload_input_media(Request $request) {
        $rules = (['file' => 'mimes:png,mp3,mp4,mpeg,mpga,m4a,wav,webm']);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ]);
        }

        $upload_dir_subpath = 'tools/'.$this->user_id;

        $file = $request->file('file');
        $extension = $request->file('file')->extension();
        $playtime = '';
        if(strtolower($extension)!='png'){
            $track = GetId3::fromUploadedFile(request()->file('file'));
            $playtime = convert_duration_to_minutes($track->getPlaytime());
        }

        $filename = time().'.'.$extension;
        // we are not uploading this to aws as we need path
        if(false){
            try {
                $upload2S3 = Storage::disk('s3')->putFileAs($upload_dir_subpath, $file,$filename);
                return response()->json([
                    'error' => false,
                    'filename' =>  Storage::disk('s3')->url($upload2S3),
                    'playtime'=>$playtime
                ]);
            }
            catch (\Exception $e){
                $error_message = $e->getMessage();
                if(empty($error_message)) $error_message =  __('Something went wrong.');
                return response()->json([
                    'error' => true,
                    'message' => $error_message
                ]);
            }
        }
        else{

            if ($request->file('file')->storeAs('public/'.$upload_dir_subpath, $filename)) {
                return Response::json([
                    'error' => false,
                    'filename' =>  asset('storage').'/'.$upload_dir_subpath.'/'.$filename,
                    'playtime'=>$playtime
                ]);
            } else {
                return Response::json([
                    'error' => true,
                    'message' => __('Something went wrong.'),
                ]);
            }
        }
    }

    public function search_history() {
        $data = array('body' => 'openai/tools/search-history','load_datatable'=>true);
        return $this->viewcontroller($data);
    }

    public function search_history_data(Request $request)
    {
        $search_value = $request->search_value;
        $search_ai_template_id = $request->search_ai_template_id;
        $display_columns = array("#",'id', 'document_name','tokens', 'language', 'actions','group_name','api_type', 'template_name', 'model', 'searched_at');
        $search_columns = array('document_name');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 1;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'ai_search_contents.id';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'desc';
        $order_by=$sort." ".$order;

        $table="ai_search_contents";
        $select= [$table.".*","template_name","model","api_group","api_type","group_name","group_slug","template_slug"];
        $query = DB::table($table)->select($select)->where('user_id',$this->user_id)
            ->leftJoin('ai_templates', 'ai_templates.id', '=', $table.'.ai_template_id')
            ->leftJoin('ai_template_groups', 'ai_template_groups.id', '=', 'ai_templates.ai_template_group_id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_ai_template_id)) {
            $query->where('ai_template_id','=',$search_ai_template_id);
        }

        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($select)->where('user_id',$this->user_id)
            ->leftJoin('ai_templates', 'ai_templates.id', '=', $table.'.ai_template_id')
            ->leftJoin('ai_template_groups', 'ai_template_groups.id', '=', 'ai_templates.ai_template_group_id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        if (!empty($search_ai_template_id)) {
            $query->where('ai_template_id','=',$search_ai_template_id);
        }
        $total_result = $query->count();

        $i=0;
        foreach ($info as $key => $value)
        {
            if($value->api_group=='image') $module_id = $this->module_id_image;
            else if($value->api_group=='audio') $module_id = $this->module_id_audio;
            else $module_id = $this->module_id_token;

            if(empty($value->document_name)) $value->document_name = __('Untitled Document');
            if(empty($value->language)) $value->language = '-';
            if(empty($value->api_type)) $value->api_type = __($value->api_type);
            $value->document_name = htmlspecialchars($value->document_name);

            $value->searched_at = convert_datetime_to_timezone($value->searched_at,'',false,'jS M y H:i:s');

            $str='';
            $search_page = route('tools',['group_slug'=>$value->group_slug,'template_slug'=>$value->template_slug,'search_id'=>$value->id]);
            if(has_module_action_access($module_id,2,$this->team_access,$this->is_manager))
            $str.="<a class='btn btn-circle btn-outline-primary' target='_BLANK' href='".$search_page."' title='".__('View')."'>".'<i class="fas fa-eye"></i>'."</a>";
            if(has_module_action_access($module_id,3,$this->team_access,$this->is_manager))
            $str=$str."&nbsp;&nbsp;<a href='".route('tools-delete-search',$value->api_group)."' data-table-name='table' data-id='".$value->id."' class='delete-row btn btn-circle btn-outline-danger' title='".__('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->actions = "<div class='w-min-80px'>".$str."</div>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function delete_search($api_group='text'){
        if(config('app.is_demo')=='1' && $this->is_admin)
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);


        if($api_group=='image') $module_id = $this->module_id_image;
        else if($api_group=='audio') $module_id = $this->module_id_audio;
        else $module_id = $this->module_id_token;

        if(!has_module_action_access($module_id,3,$this->team_access,$this->is_manager)) {
            return response()->json(['error' => true,'message' => __('You are not allowed to perform this action.')]);
        }

        $id = request()->id;
        $table = 'ai_search_contents';
        $where = ['id'=>$id];
        if(DB::table($table)->where($where)->delete())
            return response()->json(['error'=>false,'message'=>__('Content has been deleted successfully.')]);
        else return response()->json(['error'=>true,'message'=>__('Something went wrong.')]);
    }


    public function template_manager()
    {   
        if(has_module_access($this->module_id_template_manager,$this->module_ids,$this->is_admin,$this->is_manager)){            
            $data = array('body' => 'openai/template/template-manager','load_datatable'=>true);
            return $this->viewcontroller($data);
        }
        else abort('403');
    }

    public function list_template_data(Request $request)
    {
        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $display_columns = array("#",'id','template_thumb','template_name','about_text','group_name','status','actions');
        $search_columns = array('template_name');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 2;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'template_name';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'asc';
        $order_by=$sort." ".$order;

        $table="ai_templates";
        $where = ['ai_template_groups.status'=>'1'];
        $query = DB::table($table)->select($table.'.*','group_name')
            ->leftJoin('ai_template_groups','ai_template_groups.id','=','ai_templates.ai_template_group_id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $info = $query->where($where)->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($table.'id')
            ->leftJoin('ai_template_groups','ai_template_groups.id','=','ai_templates.ai_template_group_id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $total_result = $query->where($where)->count();

        foreach ($info as $key => $value)
        {
            $delete_url = route('delete-template');
            $str="";
            if(has_module_action_access($this->module_id_template_manager,2,$this->team_access,$this->is_manager))
                $str.="<a class='btn btn-circle btn-outline-warning edit-template' data-id='".$value->id."' href='#' title='".__('Edit')."'>".'<i class="fas fa-edit"></i>'."</a>";
            if(has_module_action_access($this->module_id_template_manager,3,$this->team_access,$this->is_manager))
                $str=$str."&nbsp;<a href='".$delete_url."' data-id='".$value->id."' data-table-name='table10' class='delete-row btn btn-circle btn-outline-danger' title='".__('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->template_thumb = '<div class="border rounded py-2 text-center w-70px"><i class="icon-lg '.$value->template_thumb.'"></i></div>';
            $value->actions = "<div class='w-min-100px'>".$str."</div>";
            $status_checked = ($value->status=='1') ? 'checked' : '';
            $value->status = '<div class="pt-2 form-check form-switch update-status-switch d-flex justify-content-center"><input data-url="'.route('update-template-status').'" data-id="'.$value->id.'" class="form-check-input update-status" type="checkbox" '.$status_checked.' value="'.$value->status.'"></div>';
            $value->template_name = __($value->template_name);
            $value->about_text = __($value->about_text);
            $value->group_name = __($value->group_name);
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function edit_template(){
        if(!has_module_action_access($this->module_id_template_manager,2,$this->team_access,$this->is_manager)) {
            return response()->json(['status' => '0','message' => __('You are not allowed to perform this action.')]);
        }
        $id = request()->id;
        $template_data = DB::table('ai_templates')->where('id',$id)->first();
        // dd($template_data);
        return response()->json(['status' => '1','data' => $template_data]);
    }

    public function save_template(Request $request)
    {
        if(config('app.is_demo')=='1')
        return response()->json(['status' => '0','message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        if(!has_module_action_access($this->module_id_template_manager,[1,2],$this->team_access,$this->is_manager)) {
            return response()->json(['status' => '0','message' => __('You are not allowed to perform this action.')]);
        }
        $id = $request->id;
        $template_name = $this->make_trans_index($request->template_name);
        $template_slug = Str::slug($template_name);
        if($template_slug==""){
           return response()->json(['status' => '0','message' => __('Only English letters are allowed. To localize a content you add the content and tranlsate using mult-language editor.')]);

        }
        $about_text = $this->make_trans_index($request->about_text);
        $template_description = $this->make_trans_index($request->template_description);
        $ai_template_group_id = $request->ai_template_group_id;
        $template_thumb = $request->template_thumb;
        $param_name = $request->paramName ?? [];
        $param_type = $request->paramType ?? [];
        $paramType_drop_down = $request->paramType_drop_down_values ?? [];
        $api_type = $request->api_type ?? '';
        $api_group = $request->api_group ?? 'text';
        $api_group = strtolower($api_group);
        $output_display = $request->output_display ?? '';
        $model = $request->model ?? '';
        $default_tokens = $request->default_tokens;
        if(empty($default_tokens) && ( $api_group=='text' ||  $api_group=='code' || $api_group=='chat')) $default_tokens = 16;
        $prompt_fields = [];
        $paramType_drop_down_values= array();
        $i=0;
        // dd($param_name);
        if(!empty($param_name)){
            foreach ($param_name as $key=>$value){
                if(empty($value)) continue;
                $value = $this->make_trans_index($value);
                if(empty($param_type[$key])) $param_type[$key] = 'text';
                if($param_type[$key] == 'dropdown'){
                     $paramType_drop_down_values[$value] = $paramType_drop_down[$i];
                }
                $prompt_fields[$value] = $param_type[$key];
              $i++;  
            }
        }
        if(empty($template_thumb)) $template_thumb = 'mdi mdi-robot text-primary';

        $insert_data = [
            'template_name' => $template_name,
            'template_slug' => $template_slug,
            'about_text' => $about_text,
            'template_description' => $template_description,
            'prompt_fields' => json_encode($prompt_fields),
            'paramType_drop_down_values'=>json_encode($paramType_drop_down_values),
            'ai_template_group_id' => $ai_template_group_id,
            'api_group' => $api_group,
            'api_type' => $api_type,
            'output_display' => $output_display,
            'default_tokens' => $default_tokens,
            'model' => $model,
            'template_thumb' => $template_thumb
        ];
        if(!empty($id)){
            DB::table('ai_templates')->where(['id'=>$id])->update($insert_data);
            return response()->json(['status'=>'1','message'=>__('Template has been saved successfully.')]);
        }
        else{
            DB::table('ai_templates')->upsert($insert_data, ['template_slug'], $insert_data);
            return response()->json(['status'=>'1','message'=>__('Template has been saved successfully.')]);
        }


        return response()->json(['status'=>'0','message'=>__('Something went wrong.')]);
    }

    public function delete_template(Request $request){
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        if(!has_module_action_access($this->module_id_template_manager,3,$this->team_access,$this->is_manager)) {
            return response()->json(['error' => true,'message' => __('You are not allowed to perform this action.')]);
        }

        $id = $request->id;
        $table = 'ai_templates';
        $where = ['id'=>$id];
        if(DB::table($table)->where($where)->delete())
            return response()->json(['error'=>false,'message'=>__('Template has been deleted successfully.')]);
        else return response()->json(['error'=>true,'message'=>__('Something went wrong.')]);
    }

    public function update_template_status(Request $request)
    {
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        if(!has_module_action_access($this->module_id_template_manager,3,$this->team_access,$this->is_manager)) {
            return response()->json(['error' => true,'message' => __('You are not allowed to perform this action.')]);
        }
        $id = $request->id;
        $status = $request->status;
        $where = ['id'=> $id];
        $query = DB::table('ai_templates')->where($where)->update(['status' => $status]);
        if($query) return response()->json(['error' => false,'message' => __('Template status has been updated successfully')]);
        else return response()->json(['error' => true,'message' => __('Something went wrong')]);
    }

    public function list_template_group_data(Request $request)
    {
        $search_value = !is_null($request->input('search.value')) ? $request->input('search.value') : '';
        $display_columns = array("#","id","serial","icon","group_name","status","actions");
        $search_columns = array('group_name');

        $page = isset($request->page) ? intval($request->page) : 1;
        $start = isset($request->start) ? intval($request->start) : 0;
        $limit = isset($request->length) ? intval($request->length) : 10;
        $sort_index = !is_null($request->input('order.column')) ? strval($request->input('order.column')) : 2;
        $sort = !is_null($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'serial';
        $order = !is_null($request->input('order.0.dir')) ? strval($request->input('order.0.dir')) : 'asc';
        $order_by=$sort." ".$order;

        $table="ai_template_groups";
        $query = DB::table($table);
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $info = $query->orderByRaw($order_by)->offset($start)->limit($limit)->get();

        $query = DB::table($table)->select($table.'id');
        if ($search_value != '')
        {
            $query->where(function($query) use ($search_columns,$search_value){
                foreach ($search_columns as $key => $value) $query->orWhere($value, 'like',  "%$search_value%");
            });
        }
        $total_result = $query->count();

        foreach ($info as $key => $value)
        {
            $value->icon = '<div class="border rounded py-2 text-center w-70px"><i class="icon-lg '.$value->icon_class.'"></i></div>';
            $str = '';
            $delete_url = route('delete-template-group');
            if(has_module_action_access($this->module_id_template_manager,2,$this->team_access,$this->is_manager))
            $str.= "<a href='' title='".__('Edit')."' data-group-name='".htmlspecialchars($value->group_name)."' data-serial='".$value->serial."' data-id='".$value->id."' data-icon-class='".$value->icon_class."' class='edit-template-group btn btn-circle btn-outline-warning'>".'<i class="fa fa-edit"></i>'."</a>";
            if(has_module_action_access($this->module_id_template_manager,3,$this->team_access,$this->is_manager))
            $str.= "<a href='".$delete_url."' title='".__('Delete')."' data-table-name='table11' data-id='".$value->id."' class='delete-row btn btn-circle btn-outline-danger'>".'<i class="fa fa-trash"></i>'."</a>";
            $value->actions = "<div class='w-min-100px'>".$str."</div>";
            $status_checked = ($value->status=='1') ? 'checked' : '';
            $value->status = '<div class="pt-2 form-check form-switch update-status-switch d-flex justify-content-center"><input data-url="'.route('update-template-group-status').'" data-id="'.$value->id.'" class="form-check-input update-status" type="checkbox" '.$status_checked.' value="'.$value->status.'"></div>';
            $value->group_name = __($value->group_name);
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = array_format_datatable_data($info, $display_columns ,$start);
        echo json_encode($data);
    }

    public function save_template_group(Request $request)
    {
        if(config('app.is_demo')=='1')
            return response()->json(['status' => '0','message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        if(!has_module_action_access($this->module_id_template_manager,[1,2],$this->team_access,$this->is_manager)) {
            return response()->json(['status' => '0','message' => __('You are not allowed to perform this action.')]);
        }
        $id = $request->id;
        $group_name = $request->group_name;
        $group_slug = Str::slug($group_name);
        if($group_slug==""){
           return response()->json(['status' => '0','message' => __('Only English letters are allowed. To localize a content you add the content and tranalate using mult-language editor.')]);

        }
        $serial = $request->serial;
        $icon_class = $request->icon_class;
        if(empty($icon_class)) $icon_class = 'mdi mdi-heart text-primary';

        $insert_data = [
            'group_name' => $group_name,
            'group_slug' => $group_slug,
        ];
        if(!empty($serial)) $insert_data['serial'] = $serial;
        if(!empty($icon_class)) $insert_data['icon_class'] = $icon_class;

        if(!empty($id)){
            DB::table('ai_template_groups')->where(['id'=>$id])->update($insert_data);
            return response()->json(['status'=>'1','message'=>__('Template group has been saved successfully.')]);
        }
        else{
           DB::table('ai_template_groups')->upsert($insert_data, ['group_slug'], $insert_data);
           return response()->json(['status'=>'1','message'=>__('Template group has been saved successfully.')]);
        }
        return response()->json(['status'=>'0','message'=>__('Something went wrong.')]);
    }

    public function delete_template_group(Request $request)
    {
        if(config('app.is_demo')=='1')
        return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);


        if(!has_module_action_access($this->module_id_template_manager,3,$this->team_access,$this->is_manager)) {
            return response()->json(['error' => true,'message' => __('You are not allowed to perform this action.')]);
        }

        $id = $request->id;

        $table = 'ai_template_groups';
        $where = ['id'=>$id];
        if(DB::table($table)->where($where)->delete())
            return response()->json(['error'=>false,'message'=>__('Template group has been deleted successfully.')]);
        else return response()->json(['error'=>true,'message'=>__('Something went wrong.')]);
    }

    public function update_template_group_status(Request $request)
    {
        if(config('app.is_demo')=='1')
            return response()->json(['error' => true,'message' => __('This feature has been disabled in this demo version. We recommend to sign up as user and check.')]);

        if(!has_module_action_access($this->module_id_template_manager,3,$this->team_access,$this->is_manager)) {
            return response()->json(['error' => true,'message' => __('You are not allowed to perform this action.')]);
        }
        $id = $request->id;
        $status = $request->status;
        $where = ['id'=> $id];
        $query = DB::table('ai_template_groups')->where($where)->update(['status' => $status]);
        if($query) return response()->json(['error' => false,'message' => __('Template group status has been updated successfully')]);
        else return response()->json(['error' => true,'message' => __('Something went wrong')]);
    }

    private function make_trans_index($str=''){
        if(empty($str)) return '';
        $str = trim($str);
        $str = str_replace(array("\r", "\n", "\t","'",'"','\\'), [' ',' ',' ','`','`',''], $str);
        return trim(ucfirst($str));
    }

    public function generate_dynamic_lang(){
       if(!$this->is_admin) abort(404);
        $groups = DB::table("ai_template_groups")->select('group_name')->get();
        $templates = DB::table("ai_templates")->get();
        $dynamic_array = [];
        foreach ($groups as $group){
            if(!empty($group->group_name)) array_push($dynamic_array,$group->group_name);
        }
        foreach ($templates as $template){
            if(!empty($template->template_name)) array_push($dynamic_array,$template->template_name);
            if(!empty($template->template_description)) array_push($dynamic_array,$template->template_description);
            if(!empty($template->about_text)) array_push($dynamic_array,$template->about_text);
            $prompt_fields = !empty($template->prompt_fields) ? json_decode($template->prompt_fields) : [];
            foreach ($prompt_fields as $field_name=>$field_type){
                if(!empty($field_name))
                    array_push($dynamic_array,$field_name);
            }
        }
        if(!empty($dynamic_array)){
            $str = "";
            foreach ($dynamic_array as $lang){
                $str .= '{{__("'.$lang.'")}}'.PHP_EOL;
            }
            @file_put_contents(resource_path('views'.DIRECTORY_SEPARATOR.'openai'.DIRECTORY_SEPARATOR.'template-lang.blade.php'),$str);
        }
    }
}
