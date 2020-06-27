<?php


namespace app\controller\api\service;


use app\model\Blacklist;
use think\Exception;
use think\facade\Request;

class WhiteListFilter
{

    private $audit;

    public function __construct($audit, $user)
    {
        $this->audit = $audit;
        $this->fraction = $this->audit['fraction'];
        $this->user = $user;
    }

    public function run($fraction, $pathName, $realPath)
    {

        if ($this->audit['switch'] != 1) {
            return true;
        }
        //非白名单用户进行拦截检测
        //违规分数超过指定分直接拦截
        if ($this->user['is_whitelist'] != 1 && $fraction >= $this->fraction) {
            unset($imageInfo);
            //是否直接删除
            $img2base64 = '';
            if ($this->audit ['action'] == 1) {
                $img2base64 = img2base64($realPath);
            }
            $tmp = time();
            $insertData = [
                'username' => $this->user['username'],
                'ip' => Request::ip(),
                'referer' => Request::server('HTTP_REFERER'),
                'reason' => '图片[' . $pathName . ']涉嫌违规，禁止上传',
                'image' => $img2base64,
                'create_time' => $tmp,
                'duration' => $this->audit ['duration'],
                'fraction' => $fraction,
                'release_time' => $tmp + (int)$this->audit['duration'],
            ];
            Blacklist::insert($insertData);
            throw new Exception('图片[' . $pathName . ']涉嫌违规，禁止上传', 10006);
        }
        return true;
    }

}