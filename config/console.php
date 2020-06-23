<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'hello' => 'app\command\Hello',
        'ImageUpdate' => 'app\command\ImageUpdate',
        'UpdateToV2FromV1' => 'app\command\UpdateToV2FromV1',
    ],
];
