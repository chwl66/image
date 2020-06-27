<?php


namespace app\controller\ajax;


use app\model\Set;
use app\model\SetGroup;

class AdminMenu
{
    public function get()
    {
        $setList = SetGroup::order('id', 'asc')
            ->select()->toArray();
        array_walk($setList, function (&$value) {
            $value = [
                'name' => $value['mark'],
                'title' => $value['mark'],
                'jump' => '/set/' . str_replace('.','/',$value['name']),
            ];
        });
        /*
    "name": "pictureprocess"
    ,"title": "图片处理"
    ,"jump": "set/pictureprocess"
         */
        $data = [
            [
                'title' => '主页',
                'icon' => 'layui-icon-home',
                'list' =>
                    [
                        [
                            'title' => '控制台',
                            'jump' => '/',
                        ],
                    ],
            ],
            [
                'name' => 'app',
                'title' => '应用',
                'icon' => 'layui-icon-app',
                'list' =>
                    [

                        [
                            'name' => 'images',
                            'title' => '图片管理',
                            'jump' => 'app/images',
                        ],

                        [
                            'name' => 'suspiciousImages',
                            'title' => '可疑图片管理',
                            'jump' => 'app/suspiciousImages',
                        ],

                        [
                            'name' => 'api',
                            'title' => '图床Api',
                            'jump' => 'app/api',
                        ],

                        [
                            'name' => 'blacklist',
                            'title' => '黑名单管理',
                            'jump' => 'app/blacklist',
                        ],

                        [
                            'name' => 'cache',
                            'title' => '缓存管理',
                            'jump' => 'app/cache',
                        ],

                        [
                            'name' => 'statistics',
                            'title' => '数据统计',
                            'jump' => 'app/statistics',
                        ],

                        [
                            'name' => 'storage',
                            'title' => '储存策略',
                            'jump' => 'app/storage',
                        ],

                        [
                            'name' => 'rechargeCard',
                            'title' => '卡密管理',
                            'jump' => 'app/rechargeCard',
                        ],
                    ],
            ],
            [
                'name' => 'user',
                'title' => '用户',
                'icon' => 'layui-icon-user',
                'list' =>
                    [

                        [
                            'name' => 'user',
                            'title' => '网站用户',
                            'jump' => 'user/list',
                        ],

                        [
                            'name' => 'user-groud',
                            'title' => '角色管理',
                            'jump' => 'user/group',
                        ],
                    ],
            ],
            [
                'name' => 'set',
                'title' => '设置',
                'icon' => 'layui-icon-set',
                'list' =>
                    $setList,
            ],
            [
                'name' => 'get',
                'title' => '执行SQL',
                'icon' => 'layui-icon-code-circle',
                'jump' => '/system/sql',
            ],
            [
                'name' => 'get',
                'title' => '授权',
                'icon' => 'layui-icon-auz',
                'jump' => '/system/get',
            ],
            [
                'name' => 'get',
                'title' => '检查更新',
                'icon' => 'layui-icon-release',
                'jump' => '/system/update',
            ],
        ];
        return msg(200, 'success', $data);
    }
}