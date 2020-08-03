<?php
/**
 * FILE_NAME: Toutiao.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Toutiao implements ImageApi
{
    public function upload($pathName)
    {
        $data['photo'] = new \CURLFile($pathName);

        $UploadUrl = 'https://mp.toutiao.com/upload_photo/?type=json';
        $res = hidove_post($UploadUrl, $data, '"https://mp.toutiao.com/');
        $result = json_decode($res, true);
        if ($result['message'] == 'success')
            return $result['web_url'];
        hidove_log($res);
        return '上传失败！';
    }
}