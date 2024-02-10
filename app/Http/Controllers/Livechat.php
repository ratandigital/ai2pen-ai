<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Home;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\OpenAiServiceInterface;
use CURLFILE;

use Psy\Command\WhereamiCommand;

class Livechat extends Home
{

    public function __construct(OpenAiServiceInterface $openai_service)
    {
        $this->set_global_userdata();
        $this->openai = $openai_service;
    }
 

    public function custom_livechat($id=0)
    {
        $data['body'] = 'openai.custom-chat';
        $prompt_id = $id;
        $data['prompt_id'] = $prompt_id;
        $data['custom_prompts'] =DB::table('ai_chat_settings')->where('id',$prompt_id)->first(); 
        $data['info'] =DB::table('conversation_list')->where('user_id',$this->user_id)->where('prompt_id',$prompt_id)->orderByDesc('id')->limit(20)->get();        
        $data['system_prompt_config'] =DB::table('conversation_user_choice')->where('user_id',$this->user_id)->first();
        $data['ai_first_reply'] = DB::table('conversation_details')
                                ->where('sender', 'assistant')
                                ->orderByDesc('id')
                                ->limit(20)
                                ->groupBy('conversation_list_id')
                                ->get();                               
        return $this->viewcontroller($data);       

    }

    public function load_livechat()
    {
        if($this->is_member && !$this->is_manager && config('app.force_email_verify')=='1' && !auth()->user()->email_verified_at){
            return redirect(route('verification.notice'));
        }
        $data['chat_model']= DB::table('settings_thirdparty_apis')->where('user_id',$this->user_id)->select('chat_model')->first();
        $data['body'] = 'openai.livechat';
        $data['info'] =DB::table('conversation_list')->where('prompt_id',0)->where('user_id',$this->user_id)->orderByDesc('id')->limit(20)->get();        
        $data['custom_prompts'] =DB::table('ai_chat_settings')->select('id','profile_name','custom_prompt','chat_model','custom_prompt_img')->limit(20)->get();        
        $data['system_prompt_config'] =DB::table('conversation_user_choice')->where('user_id',$this->user_id)->first();
        $data['ai_first_reply'] = DB::table('conversation_details')
                                ->where('sender', 'assistant')
                                ->orderByDesc('id')
                                ->limit(20)
                                ->groupBy('conversation_list_id')
                                ->get();                               
        $data['system_prompt_info'] =config('api.openai-system-prompt');
        return $this->viewcontroller($data);       

    }


    public function livechat_conversation()
    {       
        $send_message = request()->send_message;
        $conversation_id = request()->conversation_id;
        $custom_prompt_id = request()->custom_prompt_id ?? '0';
        $system_prompt_value='';
        $system_prompt_model='';
        if(!isset(request()->system_prompt_value)){
            $system_prompt_value =config('api.openai-system-prompt');
        }
        else $system_prompt_value = request()->system_prompt_value;

        if(!isset(request()->system_prompt_model)){
            $system_prompt_model = 'gpt-3.5-turbo';
        }
        else $system_prompt_model = request()->system_prompt_model;

        $time= date('Y-m-d H:i:s');
        $user_id= request()->from_user_id;

        $module_id=$this->module_id_token;
        $req = 16;
        $status=$this->check_usage($module_id,$req);
        if(!has_module_access($module_id,$this->module_ids,$this->is_admin,$this->is_manager)) {
            $response=['status'=>'0','message'=>__('Access Forbidden')];
            echo json_encode($response);
            return;
        }
        else if(!has_module_action_access($module_id,[1,2],$this->team_access,$this->is_manager)) {
            $response=['status'=>'0','message'=>__('Access Forbidden')];
            echo json_encode($response);
            return;
        }
        else if($status=="3") {
            $response=['status'=>'0','message'=>__('Module limit has been exceeded.')];
            echo json_encode($response);
            return;
        }


        $list_data=array
        (
            'user_id'=>$user_id ,
            'conversation_start_content'=>$send_message,
            'prompt_id'=>$custom_prompt_id,
            'time'=>$time,
        );
        $chat_icon = asset("assets/images/livechat/chat-bubble.png");
        $edit_icon = asset("assets/images/livechat/edit.png");
        $delete_icon = asset("assets/images/livechat/delete.png");

        $count=0;


        if($conversation_id== '0'){
            DB::table('conversation_list')->insert($list_data);
            
            $message_id = DB::getPdo()->lastInsertId();
            $conversation_id= $message_id; ; 
            $count = $count + 1;
      
        }

           
        DB::table('conversation_details')
            ->insert(['conversation_list_id'=>$conversation_id ,'time'=>$time,'sender'=>'user','content'=>$send_message,]);
        
        $api_data_set['model'] = $system_prompt_model ?? 'gpt-3.5-turbo';
        $api_data_set['user'] = 'user-'.$this->user_id;
        $api_data_set['max_tokens'] = 1500;
        $json_param = true;
        $content_type = 'application/json';
        $ai_img = config('app.ai_chat_icon');
        
        $api_data_set['messages'][0]=array('role'=>'system','content'=>$system_prompt_value);

        $msg =DB::table('conversation_details')->where('conversation_list_id',$conversation_id)->get();

        foreach($msg as $value){
            $content = $value->content;
            if (mb_strlen($content) > 150) {
                $content = mb_substr($content, 0, 150) . '...';
            }
            $api_data_set['messages'][]=array('role'=>$value->sender,'content'=>$content);
        }


        if(config('app.is_demo')=='1' && config('app.is_restricted')=='1' && $this->is_admin){
            echo json_encode(['status' => '0','message' => 'Due to high traffic, content generation has been temporarily disabled for this demo admin account. You may sign up as a member user to generate your desired content and explore the features of your own account without any limitations.']);
            exit();
        }


        $this->openai->api_key = config('api.openai-key');
        $response = $this->openai->call_api('chat/completions',$api_data_set,$content_type,$json_param);
        $response_decoded = json_decode($response);
        if(isset($response_decoded->error)){
            $error_msg = $response_decoded->error->message ?? __('Something went wrong.');
            $response= ['status'=>'0','message'=>$error_msg];
            echo json_encode($response);
            return ;
        }
        
       

        $answer_by_AI= $response_decoded->choices[0]->text;
        $usage_log = $response_decoded->usage->total_tokens;
        $this->insert_usage_log($module_id,$usage_log,$this->user_id);

        DB::table('conversation_details')
            ->insert(['conversation_list_id'=>$conversation_id ,'time'=>$time,'sender'=>'assistant','content'=>$answer_by_AI,]);


        if($count==1){

            
            
            $side_content= '<ul class="list-group list-group-flush" id="chat_'.$conversation_id.'">
                            <li class="contact">
                                <div class="row">                           
                                    <div class="col-9" id="side_chat_content">
                                        <div class="wrap">
                                            <img src="'.$chat_icon.'" alt="" />
                                            <input class="edit-hidden-input" type="hidden" id="chat_id_input_'.$conversation_id.'"  value="'.$conversation_id.'">    
                                            <div class="meta">
                                                <div class="name" id="side_chat_'.$conversation_id.'">'.ucfirst($send_message).'</div>
                                                <div class="preview">'.htmlspecialchars($answer_by_AI).'</div>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-3"  id="edit_btn_div">                            
                                        <span class="" style="padding-right: 8px"  id="edit_btn"><img class="img_custom" src="'.$edit_icon.'"/></span>
                                        <span  id="delete_btn"><img class="img_custom" src="'.$delete_icon.'"/></span>                                                     
                                    </div>
                                </div>
                            </li>
                        </ul>';
        }


       $content='<li class="replies">
                <img src="'.$ai_img.'" alt="" />
                <p class="lh-base">'.$this->convert_code_block($answer_by_AI).'</p>
                </li>';

        if(!isset($side_content)) $side_content='';


        $response=['status'=>'1','conversation_id'=>$conversation_id,'content'=>$content,'side_content'=>$side_content ];
        echo json_encode($response); 
    }

    public function convert_code_block($text) {
        $pattern = '/```(.+?)```/s'; // pattern to match the code block enclosed in triple backticks
        $has_code=0;

        $converted_text = preg_replace_callback($pattern, function($matches) use (&$has_code) {
        $has_code=1;
        $code = $matches[1];
        $code = htmlspecialchars($code); // convert special characters to HTML entities
        $code = nl2br($code); // replace newlines with <br> tags
        $embedded_code = "</p></li><pre><code>" . $code . "</code></pre><li class='replies'><p>";
          return $embedded_code;
        }, $text);

        if($has_code==0){
            $converted_text = htmlspecialchars($converted_text); // convert special characters to HTML entities
            $converted_text = nl2br($converted_text); // replace newlines with <br> tags
        }

        return $converted_text."<script>hljs.highlightAll();</script>";
  }

    public function livechat_side_conversation()
    {
        $chat_id = request()->chat_id;
        $conversation_id = request()->conversation_id;

        $msg =DB::table('conversation_details')->where('conversation_list_id',$chat_id)->get();

        $content= '';
        $user_img =!empty(Auth::user()->profile_pic) ? Auth::user()->profile_pic : asset('assets/images/avatar/avatar-6.png');
        $ai_img = config('app.ai_chat_icon') ;
        
            foreach ($msg as $key=>$value)
                {
                    
                        if($value->sender == 'user'){
                        $content.= '<li class="sent">
                                <img src="'.$user_img.'" alt="" />
                                <p class="lh-base">'.$this->convert_code_block($value->content).'</p>
                            </li>';
                        }
                        else{
                                $content.= '<li class="replies">
                                            <img src="'.$ai_img.'" alt="" />
                                            <p class="lh-base">'.$this->convert_code_block($value->content).'</p>
                                            </li>';
                            }
                    
                }
        

        

        $response=['content'=>$content];
        echo json_encode($response); 

    }


    public function livechat_sidechat_edit()
    {
        $sidechat_value = request()->sidechat_value;
        $chat_id = request()->chatID;

        DB::table('conversation_list')->where('user_id',$this->user_id)->where('id',$chat_id)->update(['conversation_start_content'=>$sidechat_value]);

        $response = ['sidechat_value'=> $sidechat_value,'chat_id'=>$chat_id];
        echo json_encode($response); 
    }

    public function livechat_side_conversation_delete()
    {
        if(config('app.is_demo')=='1' && $this->is_admin){
            echo 0;
            exit();
        }
        $chat_id = request()->chat_id;
        DB::table('conversation_list')->where('user_id',$this->user_id)->where('id',$chat_id)->delete();
        echo  $chat_id;
    }
    
    public function user_choice_system_prompt()
    {
       if(config('app.is_demo')=='1' && $this->is_admin){
            $response = ['system_prmpt_value'=> "You are a helpful assistant."];
            echo json_encode($response);
            exit;
        }
        $system_prmpt_value = request()->system_prmpt_value;
        $system_prmpt_model = request()->system_prmpt_model;
        $user_id= request()->from_user_id;
        
        DB::table('conversation_user_choice')->updateOrInsert(['user_id'=>$user_id],['user_id'=>$user_id,'system_prompt'=>$system_prmpt_value,'model'=>$system_prmpt_model]);
        
        $response = ['system_prmpt_value'=> $system_prmpt_value,'system_prmpt_model'=> $system_prmpt_model];
        echo json_encode($response); 

    }

    public function livechat_conversation_download()
    {
        $chat_id = request()->chat_id;
        $chats = DB::table('conversation_details')->where('conversation_list_id', $chat_id)->get();
        
        $content = '';
        foreach ($chats as $chat) {
            $content .=$chat->sender .' : '.$chat->content . "\n";
        }
        
        $file_name = 'chat_' . $chat_id . '_' . date('YmdHis') . '.txt';
        
        $file_path = storage_path('app/public' . $file_name);
        file_put_contents($file_path, $content);
        
        return response()->json([
            'success' => true,
            'file_url' => asset('storage/app/public' . $file_name),
            'file_name' => $file_name
        ]);
        

    }




}
