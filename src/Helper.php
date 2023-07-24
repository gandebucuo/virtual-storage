<?php
namespace VirtualCloud;

define ('HTTP_PUT', 'PUT');
define ('HTTP_POST', 'POST');
define ('HTTP_DELETE', 'DELETE');

class Helper{

    //处理curl xml响应格式
    public static function  curl_response_xml($options,$type)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);
        //处理xml格式响应
        if($response){
            $error_response = simplexml_load_string($response);
            return ['status'=>400,'msg'=>$error_response->Message];
        }
        if($response === false){
            return ['status'=>400,'msg'=>$type==HTTP_DELETE?'刪除失败':'上传失败'];
        }
        return ['status'=>200,'msg'=>$type==HTTP_DELETE?'刪除成功':'上传成功'];
    }

    //处理curl json响应格式
    public static function  curl_response_json($options,$type)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response,1);
        if(isset($response['error'])){
            return ['status'=>400,'msg'=>$response['error']];
        }
        return ['status'=>200,'msg'=>$type==HTTP_DELETE?'刪除成功':'上传成功'];
    }
}
