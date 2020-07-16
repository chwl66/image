<?php
/**
 * FILE_NAME: Baidu.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Baidu implements ImageApi
{
    public function upload($pathName)
    {
        $data['image'] = new \CURLFile($pathName);
        list($s1, $s2) = explode(' ', microtime());
        $timestamp = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
        $UploadUrl = 'https://graph.baidu.com/upload?from=pc&tn=pc&uptime=' . $timestamp;
        $result = hidove_post($UploadUrl, $data, 'http://image.baidu.com');
        $result = json_decode($result, true);
        if (!empty($result['data']) && $result['status'] == 0) {
            if (empty($result['data']['sign'])){
                return '上传失败！';
            }
            $imageUrl = 'https://graph.baidu.com/resource/' . $result['data']['sign'] . '.jpg';
            return $imageUrl;
        } else {
            return '上传失败！';
        }
    }
}