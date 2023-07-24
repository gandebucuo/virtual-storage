<?php
namespace VirtualCloud\Providers;

use VirtualCloud\Helper;

/**
 * 腾讯云
 * Class TencentProvider
 * @package VirtualCloud\Providers
 */
class TencentProvider extends Helper
{
    private $config;                //配置项
    private $timestamp;             //GMT时间
    private $cloud_file_name;       //云存储文件路径地址名称
    private $upload_file_path;      //上传文件临时路径地址

    public function __construct(array $config,$cloud_file_name,$upload_file_path)
    {
        $this->config           = $config;
        $this->timestamp        = gmdate('D, d M Y H:i:s \G\M\T');
        $this->cloud_file_name  = ltrim($cloud_file_name,'/');
        $this->upload_file_path = $upload_file_path;
    }

    /**
     * 上传文件
     * @return array
     */
    public function uploadFile()
    {
        $options = [
            CURLOPT_URL        => "http://{$this->config['bucket']}.{$this->config['domain']}/{$this->cloud_file_name}",
            CURLOPT_PUT        => true,
            CURLOPT_INFILE     => fopen($this->upload_file_path,'rb'),
            CURLOPT_INFILESIZE => filesize($this->upload_file_path),
            CURLOPT_HTTPHEADER => $this->createsUploadSignature(),
            CURLOPT_RETURNTRANSFER => true,
        ];
        return helper::curl_response_xml($options,HTTP_PUT);
    }

    /**
     * 刪除文件
     * @return array
     */
    public function deleteFile()
    {
        $options = [
            CURLOPT_URL             => "http://{$this->config['bucket']}.{$this->config['domain']}/{$this->cloud_file_name}",
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CUSTOMREQUEST   => HTTP_DELETE,
            CURLOPT_HTTPHEADER      => $this->createsDeleteSignature(),
        ];
        return helper::curl_response_xml($options,HTTP_DELETE);
    }

    /**
     * 生成文件上传签名
     * @return array
     */
    private function createsUploadSignature()
    {
        $signStr    = "PUT\n\n\n{$this->timestamp}\n/{$this->config['bucket']}/{$this->cloud_file_name}";
        $sign       = base64_encode(hash_hmac('sha1',$signStr,$this->config['secret_key'],true));
        $authorization = "q-sign-algorithm=sha1&q-ak={$this->config['secret_id']}&q-sign-time={$this->timestamp}&q-key-time={$this->timestamp}&q-header-list=host&q-url-param-list=&q-signature=$sign";
        return [
            "Authorization:".$authorization,
            "Date: {$this->timestamp}",
        ];

    }

    /**
     * 生成文件删除签名
     * @return array
     */
    private function createsDeleteSignature()
    {
        $currentTime = time();
        $expiredTime = 10; //签名的有效期，单位秒
        $signTime    = $currentTime . ";" . ($currentTime + $expiredTime);

        $httpString  = "delete\n/{$this->cloud_file_name}\n\nhost={$this->config['bucket']}.{$this->config['domain']}\n";

        $sha1HttpString = sha1($httpString);
        $stringToSign   = "sha1\n{$signTime}\n{$sha1HttpString}\n";
        $signKey        = hash_hmac('sha1', $signTime, $this->config['secret_key']);
        $signature      = hash_hmac('sha1', $stringToSign, $signKey);
        $authorization  = "q-sign-algorithm=sha1&q-ak={$this->config['secret_id']}&q-sign-time={$signTime}&q-key-time={$signTime}&q-header-list=host&q-url-param-list=&q-signature={$signature}";
        return [
            "Authorization: {$authorization}",
            "Host: {$this->config['bucket']}.{$this->config['domain']}"
        ];
    }
}