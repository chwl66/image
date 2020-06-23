<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月24日18:07:45
// +----------------------------------------------------------------------

use app\exception\Http;
use app\Request;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
//    'think\exception\Handle' => ExceptionHandle::class,
    'think\exception\Handle'       => Http::class,
];
