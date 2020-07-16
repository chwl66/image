<?php
/**
 * FILE_NAME: Juejin.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Juejin implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);

        $UploadUrl = 'https://cdn-ms.juejin.im/v1/upload?bucket=gold-user-assets';
        $result = hidove_post($UploadUrl, $data, 'https://juejin.im');
        $result = json_decode($result, true);
        if ($result['m'] == 'ok') {
            $imageUrl = 'https://' . $result['d']['domain'] . '/' . $result['d']['key'];
            return $imageUrl;
        } else {
            return '上传失败！';
        }
    }
}