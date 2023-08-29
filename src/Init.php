<?php
namespace VirtualCloud;

define ('Ali', 'AliProvider');
define ('QiNiu', 'QiNiuProvider');
define ('Tencent', 'TencentProvider');

class Init
{
    public static function make($name,array $config,$oss_file_path,$upload_file_path='')
    {
        $namespace = ucfirst($name);

        $application = "\\VirtualCloud\\Providers\\{$namespace}";

        return new $application($config,$oss_file_path,$upload_file_path);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::make($name,...$arguments);
    }
}
