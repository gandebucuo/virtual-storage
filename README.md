# 前言
###### 由于个人实际开发中使用官方SDK云存储，大部分扩展功能都未使用到，因此开发此简约版文件上传、删除功能，方面后续使用；若后续需要其他功能，将继续扩展。
# 安装
#### 使用composer命令直接安装
```
composer require virtual-cloud/storage
```
#### 或在composer.json添加包名后，执行composer install安装
```
{
    "require": {
        "virtual-cloud/storage": "dev-main"
    }
}
```

# 使用方法
#### 文件上传/删除
```
use VirtualCloud\Init;
...
    //云存储生成文件名称
    $fileName = 'cloud/test.png';    
    //本地上传文件    
    $filePath = 'D:\logo.png';  
    $config   =  [
        'access_key_id'     => 'LTAI5t****EL4i9R6',
        'access_key_secret' => '2Ta711***mYXwF333',
        'bucket'            => 'xi**un',
        'domain'            => 'oss***.aliyuncs.com',
    ];
    // Ali      阿里云
    // Tencent  腾讯云
    // QiNiu    七牛云
    $new    = Init::make(Ali,$config,$fileName,$filePath);
    //上传文件
    $result = $new->uploadFile();
    //删除文件
    $result = $new->deleteFile();
...
```
#### $config 配置参数说明
```
//阿里云
$congig = [
    'access_key_id'     => 'LTAI5t****EL4i9R6',
    'access_key_secret' => '2Ta711***mYXwF333',
    'bucket'            => 'xi**un',
    'domain'            => 'oss***.aliyuncs.com',
]

//七牛云
$config = [
    'ak'       => '3Kt9gz****z7KnM',
    'sk'       => 'WjQlby****gzDBAmn',
    'bucket'   => 'vi***oud',
    'domain'   => 'ry6by***uddn.com',
    'area'     => 'http://up-z2.qiniup.com'
]

//腾讯云
$config = [
   'secret_id'    => 'AKIDP9***FcHwP',
   'secret_key'   => 'YCg1zq***Sp0fQ',
   'bucket'       => 'vi***-1****200',
   'domain'       => 'cos.****loud.com',
]
```
# 联系我们
如需丰富扩展/其他业务/建议，请直接向1059636119@qq.com 发送邮箱；或添加微信，请说明来意奥




![个人微信](http://xiaonarun.oss-cn-beijing.aliyuncs.com/wx.jpg?x-oss-process=image/resize,m_fixed,h_340,w_300)
