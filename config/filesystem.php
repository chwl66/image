<?php

return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'public'),
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/images',
            // 磁盘路径对应的外部URL路径
            'url'        => '/images',
            // 可见性
            'visibility' => 'public',
        ],
        // 更多的磁盘配置信息
        'temp' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRuntimePath() . 'file',
        ],
    ],
];
