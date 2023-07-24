<?php
namespace VirtualCloud\Providers;

use VirtualCloud\Helper;

/**
 * 七牛云
 * Class QiNiuProvider
 * @package VirtualCloud\Providers
 */
class QiNiuProvider extends Helper
{
    private $config;                //配置项
    private $cloud_file_name;       //云存储文件路径地址名称
    private $upload_file_path;      //上传文件临时路径地址

    public function __construct(array $config,$cloud_file_name,$upload_file_path)
    {
        $this->config           = $config;
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
            CURLOPT_URL             => $this->config['area'],
            CURLOPT_POST            => TRUE,
            CURLOPT_RETURNTRANSFER  => TRUE ,
            CURLOPT_POSTFIELDS      => [
                'token' => $this->createUploadToken(),
                'key'   => $this->cloud_file_name,
                'file'  => file_get_contents($this->upload_file_path),
            ]
        ];
        return helper::curl_response_json($options,HTTP_POST);
    }

    /**
     * 刪除文件
     * @return array
     */
    public function deleteFile()
    {
        $path = '/delete/' . $this->urlsafe_base64_encode("{$this->config['bucket']}:{$this->cloud_file_name}");
        $options = [
            CURLOPT_URL             => "http://rs.qiniu.com{$path}",
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CUSTOMREQUEST   => HTTP_DELETE,
            CURLOPT_HTTPHEADER      => $this->createsDeleteToken($path),
        ];
        return helper::curl_response_json($options,HTTP_DELETE);
    }

    /**
     * 生成上传token
     * @return string
     */
    private function createUploadToken()
    {
        //可设置上传凭证有效期；例如：time()+3600 有效期为1个小时，期间只能只有此token进行验证
        $encodedPolicy = base64_encode(json_encode([
            'scope'     => $this->config['bucket'],
            'deadline'  => time() + 20
        ]));
        $sign = hash_hmac('sha1', $encodedPolicy, $this->config['sk'], TRUE);
        return $this->config['ak'] . ':' . $this->urlsafe_base64_encode($sign) . ':' . $encodedPolicy;
    }


    private function createsDeleteToken($path)
    {
        $hmac = hash_hmac('sha1', $path."\n", $this->config['sk'], TRUE);
        $sign  = $this->config['ak'] .':'.$this->urlsafe_base64_encode($hmac);
        return  [
            "Authorization:QBox " . $sign
        ];
    }


    /**
     * 生成URL安全的base64编码
     * @param $str
     * @return mixed
     */
    private function urlsafe_base64_encode($str)
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($str));
    }

}