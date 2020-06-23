<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class UpdateToV2FromV1 extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('UpdateToV2FromV1')
            ->setDescription('从V1版本更新到V2版本');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        //转化图片

        $output->writeln('开始转化图片表');
        $output->writeln('开始清空图片表');

        Db::name('image')->delete(true);
        $output->writeln('图片表已清空');

        Db::connect('v1')->name('imageinfo')->chunk(100, function ($values) use ($output) {
            $data = [];
            foreach ($values as $value) {
                $value['url'] = json_decode($value['url'],true);
                if (!empty($value['url']['this'])){
                    $value['url']['this'] = ltrim($value['url']['this'],'images/');
                }
                if (!empty($value['url']['github'])){
                    $value['url']['github'] = preg_replace('~https://cdn.jsdelivr.net/gh/[\w]+/[\w]+/~','',$value['url']['github']);
                }
                $value['url'] = json_encode($value['url']);
                $value['pathname'] = ltrim($value['pathname'],'images/');

                $data[$value['id']] = [
                    'id' => $value['id'],
                    'signatures' => $value['signatures'],
                    'storage_key' => $value['storagekey'],
                    'url' => $value['url'],
                    'pathname' => $value['pathname'],
                    'user_id' => $value['userid'],
                    'folder_id' => $value['folder'],
                    'filename' => $value['filename'],
                    'fraction' => $value['isillegal'],
                    'image_type' => $value['imagetype'],
                    'mime' => $value['imagemime'],
                    'file_size' => $value['imagesize'],
                    'sha1' => $value['sha1'],
                    'md5' => $value['md5'],
                    'create_time' => $value['createtime'],
                    'update_time' => $value['updatetime'],
                    'ip' => $value['ip'],
                    'today_request_times' => $value['todayrequest'],
                    'total_request_times' => $value['todayrequest'],
                    'final_request_time' => $value['finalrequesttime'],
                    'is_invalid' => 0,
                ];
            }

            try {
                Db::name('image')->insertAll($data);
                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理完毕');
            } catch (\Exception $e) {
                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理异常:' . $e->getMessage());

            }

        });
        $output->writeln('图片表数据转化完毕');
        //转化用户表
        $output->writeln('开始转化用户表');
        Db::connect('v1')->name('user')->chunk(100, function ($values) use ($output) {
            foreach ($values as $value) {
                if ($value['id'] == 1) {
                    $output->writeln('ID：' . $value['id'] . '为管理员用户，已跳过');
                    continue;
                }
                $storage = [];
                $storageModel = Db::connect('v1')->name('storage')
                    ->where('userid', $value['id'])
                    ->select();
                if (!empty($storageModel)) {
                    foreach ($storageModel as $v) {
                        $storage[$v['name']] = $v['data'];
                    }
                }
                $storage = json_encode($storage);
                try {
                    $data = [
                        'id' => $value['id'],
                        'username' => $value['username'],
                        'password' => $value['password'],
                        'email' => $value['mail'],
                        'token' => $value['token'],
                        'create_time' => $value['createtime'],
                        'capacity_used' => $value['capacityused'],
                        'api_folder_id' => $value['apifolder'],
                        'group_id' => $value['groupid'],
                        'reset_key' => $value['resetkey'],
                        'reset_time' => $value['resettime'],
                        'ip' => $value['ip'],
                        'is_private' => $value['isprivate'],
                        'forbidden_node' => $value['forbiddennode'],
                        'is_whitelist' => $value['iswhitelist'],
                        'finance' => $value['finance'],
                        'expiration_date' => $value['expirationdate'],
                        'watermark' => '{}',
                        'storage' => $storage,
                    ];
                    Db::name('user')->insert($data);
                    $output->writeln('ID：' . $value['id'] . '处理完毕');
                } catch (\Exception $e) {

                    $output->writeln('ID：' . $value['id'] . '处理异常:' . $e->getMessage());
                }
            }
        });
        $output->writeln('用户表数据转化完毕');

        //转化目录表
        $output->writeln('开始转化目录表');
        Db::connect('v1')->name('folders')->chunk(100, function ($values) use ($output) {
            foreach ($values as $value) {
                try {
                    $data = [
                        'id' => $value['id'],
                        'user_id' => $value['userid'],
                        'parent_id' => $value['parentid'],
                        'name' => $value['name'],
                        'update_time' => $value['updatetime'],
                        'create_time' => $value['createtime'],
                    ];
                    Db::name('folders')->insert($data);
                    $output->writeln('ID：' . $value['id'] . '处理完毕');
                } catch (\Exception $e) {

                    $output->writeln('ID：' . $value['id'] . '处理异常:' . $e->getMessage());
                }
            }
        });
        $output->writeln('目录表数据转化完毕');


        //转化接口请求记录表


        $output->writeln('开始转化接口请求记录表');

        $output->writeln('开始清空接口请求记录表');
        Db::name('api_request')->delete(true);
        $output->writeln('接口请求记录表已清空');
        Db::connect('v1')->name('apirequest')->chunk(100, function ($values) use ($output) {
            $data = [];
            foreach ($values as $value) {
                $data[$value['id']] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'key' => $value['key'],
                    'create_time' => $value['createtime'],
                    'update_time' => $value['updatetime'],
                    'total_request_times' => $value['requesttimes'],
                ];
            }
            try {

                Db::name('api_request')->insertAll($data);
                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理完毕');
            } catch (\Exception $e) {

                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理异常:' . $e->getMessage());
            }

        });
        $output->writeln('接口请求记录表数据转化完毕');

        //转化图片请求记录表
        $output->writeln('开始转化图片请求记录表');

        $output->writeln('开始清空图片请求记录表');
        Db::name('image_request')->delete(true);
        $output->writeln('图片请求记录表已清空');
        Db::connect('v1')->name('imagerequest')->chunk(100, function ($values) use ($output) {
            $data = [];

            foreach ($values as $value) {
                $data[$value['id']] = [
                    'id' => $value['id'],
                    'referer' => $value['referer'],
                    'create_time' => $value['createtime'],
                    'final_request_time' => $value['updatetime'],
                    'ip' => $value['requesttimes'],
                    'today_request_times' => $value['todayrequesttimes'],
                    'total_request_times' => $value['requesttimes'],
                ];

            }
            try {
                Db::name('image_request')->insertAll($data);
                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理完毕');
            } catch (\Exception $e) {

                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理异常:' . $e->getMessage());
            }
        });
        $output->writeln('图片请求记录表转化完毕');

        //转化卡密表
        $output->writeln('开始转化卡密表');

        $output->writeln('开始清空卡密表');
        Db::name('recharge_card')->delete(true);
        $output->writeln('卡密表已清空');
        Db::connect('v1')->name('rechargecard')->chunk(100, function ($values) use ($output) {
            $data = [];

            foreach ($values as $value) {
                $data[$value['id']] = [
                    'id' => $value['id'],
                    'key' => $value['key'],
                    'denomination' => $value['denomination'],
                    'user_id' => $value['userid'],
                    'create_time' => $value['createtime'],
                    'used_time' => $value['usetime'],
                ];

            }
            try {
                Db::name('recharge_card')->insertAll($data);
                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理完毕');
            } catch (\Exception $e) {

                $output->writeln('ID：' . implode(',', array_keys($data)) . '处理异常:' . $e->getMessage());
            }
        });
        $output->writeln('卡密表数据转化完毕');

        //转化用户分组表
        $output->writeln('开始转化用户分组表');
        Db::connect('v1')->name('group')->chunk(100, function ($values) use ($output) {
            foreach ($values as $value) {
                try {
                    $data = [
                        'id' => $value['id'],
                        'name' => $value['name'],
                        'capacity' => $value['capacity'],
                        'storage' => $value['storage'],
                        'price' => $value['price'],
                        'frequency' => $value['frequency'],
                        'picture_process' => $value['pictureprocess'],
                    ];
                    Db::name('group')->insert($data);

                    $output->writeln('ID：' . $value['id'] . '处理完毕');
                } catch (\Exception $e) {

                    $output->writeln('ID：' . $value['id'] . '处理异常:' . $e->getMessage());
                }
            }

        });
        $output->writeln('用户分组表数据转化完毕');

    }
}
