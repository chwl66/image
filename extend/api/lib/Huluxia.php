<?php
/**
 * FILE_NAME: Ali.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;


use api\ImageApi;

class Huluxia implements ImageApi
{
    public function upload($pathName)
    {
        $imageInfo = get_image_info($pathName);
        $UploadUrl = 'https://upload.huluxia.net/upload/image/larger';
        $data['file'] = new \CURLFile($pathName);

        $result =  hidove_post($UploadUrl, $data);
        if (strpos($result,'huluxia.com') !== false) {
            $result = json_decode($result, true);
            $imageUrl = str_replace('http://','https://',$result['url']);
            return $imageUrl;
        } else {
            return '上传失败！';
        }
    }
}