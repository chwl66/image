<?php
/**
 * FILE_NAME: Ali.php
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
        $result = hidove_post($UploadUrl, $data);
        $result = json_decode($result, true);
        if ($result['code'] == 200) {
            $imageUrl = $result['data']['url'];
            return $imageUrl;
        } else if (!empty($result['msg'])) {
            return $result['msg'];
        } else {
            return '上传失败！';
        }
    }
}