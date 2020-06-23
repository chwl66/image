<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月24日18:42:16
// +----------------------------------------------------------------------

namespace app\controller\admin;

use app\BaseController;
use think\facade\Config;
use think\facade\Db;
use think\facade\Env;
use think\facade\Request;
use think\facade\View;

class Update extends BaseController
{

    public function index()
    {
        return View::fetch();
    }

    public function check()
    {

        $res = hidove_get('http://auth.abcyun.cc/api/update');
        $json = json_decode($res, true);
        if (empty($json)) {
            return msg(400, '检查更新失败');
        }
        if (function_exists('opcache_reset'))
            opcache_reset();
        $data = [
            'version' => Config::get('hidove.version'),
            'lastestVersion' => $json['data']['version'],
            'content' => $json['data']['content'],
        ];
        return msg(200, '检查更新成功', $data);
    }

    public function download()
    {
        if (!class_exists('ZipArchive'))
            return msg(400, '不支持ZipArchive，无法更新');

        $data = hidove_post('http://auth.abcyun.cc/api/update', [
            'domain' => Request::host(),
            'version' => Config::get('hidove.version'),
            'token' => Env::get('app.auth.token'),
        ]);
        $json = json_decode($data, true);
        if (is_array($json)) {
            return msg(400, $json['msg']);
        }
        $filename = app()->getRootPath() . 'HidoveImage_' . uniqid() . '.zip';
        file_put_contents($filename, $data);

        $zip = new \ZipArchive();

        if ($zip->open($filename) === true) {
            $zip->extractTo(app()->getRootPath());
            $zip->close();
        } else {
            unlink($filename);
            return msg(400, '解压更新包失败');
        }
        unlink($filename);
        if (function_exists('opcache_reset'))
            opcache_reset();
        return msg(200, '更新包解压完毕');
    }

    public function updateSql()
    {
        $glob = glob(app()->getRootPath() . 'update*.sql');
        if (empty($glob)) {
            return msg(200, '未发现数据库更新文件');
        }
        foreach ($glob as $value) {

            try {
                $lines = file($value);
                $sql = '';
                $number = 0;
                foreach ($lines as $key => &$line) {

                    $line = trim($line);
                    if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*') {
                        unset($lines[$key]);
                        continue;
                    }
                    $sql .= $line;
                    if (substr($line, -1, 1) == ';') {
                        unset($lines[$key]);
                        $number += Db::execute($sql);
                        $sql = '';
                    }
                    unset($lines[$key]);
                }
                if ($number > 0) {
                    $result[$value] = '[' . basename($value) . "：影响的记录数 $number" . ']';
                }
            } catch (\Exception $e) {
                $result[$value] = '[' . basename($value) . '：' . $e->getMessage() . ']';
            } finally {
                unlink($value);
            }
        }
        return msg(200, '数据库执行结果：' . implode("\n", $result));
    }
}