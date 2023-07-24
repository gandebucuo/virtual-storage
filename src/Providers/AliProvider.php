<?php
namespace VirtualCloud\Providers;

use VirtualCloud\Helper;

/**
 * 阿里云
 * Class AliProvider
 * @package VirtualCloud\Providers
 */
class AliProvider extends Helper
{
    private $config;            //配置项
    private $timestamp;         //GMT时间
    private $cloud_file_name;   //云存储文件路径地址名称
    private $upload_file_path;  //上传文件临时路径地址

    public function __construct(array $config,$cloud_file_name,$upload_file_path)
    {
        $this->config           = $config;
        $this->timestamp        = gmdate('D, d M Y H:i:s T');
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
            CURLOPT_URL         => "http://{$this->config['bucket']}.{$this->config['domain']}/{$this->cloud_file_name}",
            CURLOPT_PUT         => TRUE,
            CURLOPT_INFILE      => fopen($this->upload_file_path, 'r'),
            CURLOPT_INFILESIZE  => filesize($this->upload_file_path),
            CURLOPT_HTTPHEADER  => $this->createSignatureHeader(HTTP_PUT),
            CURLOPT_RETURNTRANSFER => TRUE,
        ];
        return helper::curl_response_xml($options,HTTP_PUT);
    }

    /**
     * 删除文件
     * @return array
     */
    function deleteFile()
    {
        $options = [
            CURLOPT_URL             => "http://{$this->config['bucket']}.{$this->config['domain']}/{$this->cloud_file_name}",
            CURLOPT_HTTPHEADER      => $this->createSignatureHeader(HTTP_DELETE),
            CURLOPT_CUSTOMREQUEST   => HTTP_DELETE,
            CURLOPT_RETURNTRANSFER  => TRUE,
        ];
        return helper::curl_response_xml($options,HTTP_DELETE);
    }

    /**
     * 生成签名
     * @param $http_mehtod
     * @return array
     */
    private function createSignatureHeader($http_mehtod)
    {
        $stringToSign = $http_mehtod . "\n\n\n{$this->timestamp}\n/{$this->config['bucket']}/{$this->cloud_file_name}";
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->config['access_key_secret'], true));
        return [
            "Date: {$this->timestamp}",
            "Authorization: OSS {$this->config['access_key_id']}:{$signature}"
        ];
    }
}