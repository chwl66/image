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

    public function test()
    {
        $json = [
            'audit' => '{"switch":0,"fraction":"0","duration":"5000","action":1,"type":"moderatecontent","moderatecontent":{"key":"e9526e3e9a18eb4c3c946d5f556e1890"},"sightengine":{"api_user":"569295573","api_secret":"xM53PTx9ssyDeooexLa8"},"baidu":{"client_id":"peQ5cLUwT9M0oncEWN7FOxnl","client_secret":"hqhyGN1LYaw3V5hU95cTYpo9Xet2bnMs"},"tencent":{"appid":"2130990323","appkey":"VsgLDyDJnfHI7ZTH"}}',
            'distribute' => '{"distribute":"http:\/\/img.com","suffix":".jpg","api":"local","httpCode":[200,301,302],"apiUrl":"https:\/\/img.abcyun.co\/?url="}',
            'imageEdit' => '{"imageWatermark":{"locate":"1","alpha":"0"},"textWatermark":{"text":"Hidove","font":"default.ttf","size":"20","color":"#00000000","locate":"9","offset":"0","angle":"0"},"interlace":"true","quality":"100","watermark":{"type":"2","width":"587","height":"367","switch":1}}',
            'other' => '{"notify":"SGlkb3Zl5Zu+5bqKIOWPkeW4g+WFqOaWsFByb+eJiOacrO+8jOivt+W4ruWKqeaIkeS7rOa1i+ivleW5tuWujOWWhO+8jOWmguaciemXrumimO+8jOivt+eCueWHuzxhIGhyZWY9Imh0dHBzOi8vYmxvZy5oaWRvdmUuY24vcG9zdC80NzkiPjxpIGNsYXNzPSJmYWIgZmEtdGVsZWdyYW0iPjwvaT48L2E+6YCa55+l5oiR5Lus44CC","superToken":"123456789","explore":"true","apiRecord":1,"retentionDomain":"d3d3LmhpZG92ZS5jbgoqLmhpZG92ZS5jbg==","shopUrl":"https:\/\/www.hidove.cn","financeNotice":"6L+Z6YeM5piv5pSv5LuY5Lit5b+D5YWs5ZGK","authPath":"Hidove"}',
        ];
        foreach ($json as $k => $v) {

            $json_decode = json_decode($v, true);
            $mm = 'system.' . $k;
            foreach ($json_decode as $key => $value) {
                $mark = $key;

                if (is_array($value)) {
                    foreach ($value as $a => $b){
                        $name =  $mm.'.'.$key.'.'.$a;
                        $valueA =$b;

                        Set::create([
                            'name' => $name,
                            'value' => $valueA,
                            'mark' => $mark,
                            'group_id' => 1,
                            'type' => 'input',
                        ]);
                    }
                }else{
                    $name =  $mm .'.'. $key;
                    $valueA = $value;

                    Set::create([
                        'name' => $name,
                        'value' => $valueA,
                        'mark' => $key,
                        'group_id' => 1,
                        'type' => 'input',
                    ]);
                }
            }
        }
        dd(true);
    }
}