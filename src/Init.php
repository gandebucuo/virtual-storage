<?php
namespace VirtualCloud;

define ('Ali', 'AliyunProvider');
define ('QiNiu', 'QiNiuyunProvider');
define ('Tencent', 'TencentyunProvider');

class Init
{
    public static function make($name,array $config,$oss_file_path,$upload_file_path='')
    {
        $namespace = ucfirst($name);

        $application = "\\Composer\\Providers\\{$namespace}";

        return new $application($config,$oss_file_path,$upload_file_path);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::make($name,...$arguments);
    }
}