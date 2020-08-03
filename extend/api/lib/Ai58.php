<?php
/**
 * FILE_NAME: Ai58.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020-8-3 22:22:12
 */

namespace api\lib;

use api\ImageApi;

class Ai58 implements ImageApi
{
    public function upload($pathName)
    {
        $data = [
            "Pic-Size" => "0*0",
            "Pic-Encoding" => "base64",
            "Pic-Path" => "/nowater/webim/big/",
            "Pic-Data" => img2base64($pathName, true)
        ];
        $data = json_encode($data, JSON_UNESCAPED_SLASHES);

        $headers = [
            'Content-Type:multipart/form-data',
        ];

        $res = hidove_post('https://upload.58cdn.com.cn/json', $data, 'https://ai.58.com/', $headers);
        if (!empty($res)) {
            $imageUrl = 'https://pic' . mt_rand(1, 8) . '.58cdn.com.cn/nowater/webim/big/' . $res;
            return $imageUrl;
        } else {
            hidove_log($res);
            return '上传失败！';
        }
    }
}