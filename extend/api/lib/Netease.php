<?php
/**
 * FILE_NAME: Neteasy.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Netease implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);

        $UploadUrl = 'http://you.163.com/xhr/file/upload.json';
        $result = hidove_post($UploadUrl, $data, '"https://mp.toutiao.com/');
        $result = json_decode($result, true);
        if ($result['code'] == 200) {
            $imageUrl = $result['data'][0];
            $imageUrl = str_replace('http://','https://',$imageUrl);
            return $imageUrl;
        } else {
            return '上传失败！';
        }
    }
}