<?php
namespace App\Services;

class OpenAiService implements OpenAiServiceInterface {

    public $api_key='';

    function __construct(){
    }

    function call_api($endpoint=null,$post_data=[],$content_type='application/json',$json_param=true,$version='v1'){
        $url = "https://api.openai.com/".$version."/".$endpoint;
        $header=array("Content-Type: ".$content_type,"Authorization: Bearer {$this->api_key}");
        $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0';
        $post_data = $json_param ? json_encode($post_data) : $post_data;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $st = curl_exec($curl);
        curl_close($curl);
        if($endpoint=="chat/completions"){
            $result_array=json_decode($st,true);
            if(isset($result_array['choices']))
            for($i=0;$i<count($result_array['choices']);$i++){
                $response = $result_array['choices'][$i]['message']['content'] ?? '';
                $result_array['choices'][$i]['text']=$response;
            }
            $result=json_encode($result_array);
            return $result;
        }
        return $st;
    }

}
