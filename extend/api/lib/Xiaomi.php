<?php
/**
 * FILE_NAME: Xiaomi.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Xiaomi implements ImageApi
{
    public function upload($pathName)
    {
        $imageInfo = get_image_info($pathName);
        $file = curl_file_create($pathName, $imageInfo['mime'], 'hidove.' . $imageInfo['type']);
        $data['pic'] = $file;
        $UploadUrl = 'https://shopapi.io.mi.com/homemanage/shop/uploadpic';
        $result = hidove_post($UploadUrl, $data, 'http://shopapi.io.mi.com');
        $result = json_decode($result, true);
        if (!empty($result) && $result['code'] == 0) {
            $imageUrl = $result['result'];
            $imageUrl = substr($imageUrl, 0, stripos($imageUrl, '&'));
            return $imageUrl;
        } else {
            return '上传失败！';
        }
    }
}