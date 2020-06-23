<?php
// +----------------------------------------------------------------------
// | 节流设置
// |  Author：Ivey
// |  Date：2020年5月21日16:27:40
// +----------------------------------------------------------------------
return [
    //默认模板
    'default' => [
        // 缓存键前缀，防止键值与其他应用冲突
        'prefix' => 'throttle_',
        // 缓存的键，true 表示使用来源ip
        'key' => "__CONTROLLER__@__ACTION__@__IP__",
        // 设置访问次数。单位秒，默认值 null 表示不限制
        'visit_frequency' => 10,
        // 设置访问时间间隔。单位秒，默认值 null 表示不限制
        'visit_time_interval' => 86400,
        // 访问受限时返回的http状态码
        'visit_fail_code' => 429,
        // 访问受限时访问的文本信息 __WAIT__ 等待时间秒数
        'visit_fail_text' => '访问频率受到限制，请稍等__WAIT__秒再试',
    ],
    'ajax_UserAuth@forget'=>[
        // 设置访问次数。单位秒，默认值 null 表示不限制
        'visit_frequency' => 8,
        // 设置访问时间间隔。单位秒，默认值 null 表示不限制
        'visit_time_interval' => 86400,
    ],
    'ajax_UserAuth@resetPassword'=>[
        // 设置访问次数。单位秒，默认值 null 表示不限制
        'visit_frequency' => 8,
        // 设置访问时间间隔。单位秒，默认值 null 表示不限制
        'visit_time_interval' => 86400,
    ],
    'ajax_Index@imagePreview'=>[
        // 设置访问次数。单位秒，默认值 null 表示不限制
        'visit_frequency' => 10,
        // 设置访问时间间隔。单位秒，默认值 null 表示不限制
        'visit_time_interval' => 60,
    ]
];
