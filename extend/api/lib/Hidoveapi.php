<?php
/**
 * FILE_NAME: Hidove.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年5月31日20:41:08
 */

namespace api\lib;


use api\ImageApi;

class Hidoveapi implements ImageApi
{
    public function upload($pathName)
    {
        $UploadUrl = 'https://api.dalaola.com/api/imageupload/upload';
        $data['token'] = '你的token';

        $data['image'] = new \CURLFile($pathName);

        $data ['type'] = 'ali';
        $res = hidove_post($UploadUrl, $data);
        $result = json_decode($res, true);
        if ($result['code'] == 200) return $result['data']['url'];
        hidove_log($res);
        if (isset($result['msg'])) return $result['msg'];
        return '上传失败！';
    }
}