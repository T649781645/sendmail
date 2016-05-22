<?php

// 应用入口文件

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','./Application/');
// 绑定入口文件到Home模块
define('BIND_MODULE','Home');
//阻止自动生成安全文件
define('DIR_SECURE_FILENAME',false);

require '../ThinkPHP3.1.3/ThinkPHP.php';