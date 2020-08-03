<?php
/**
 * FILE_NAME: Niupic.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Niupic implements ImageApi
{
    public function upload($pathName)
    {
        $data['image_field'] = new \CURLFile($pathName);

        $res = hidove_post('https://niupic.com/index/upload/process', $data, 'https://niupic.com/');
        $result = json_decode($res,true);
        if (isset($result['data'])){
            $imageUrl =  'https://'.$result['data'];
            if (is_valid_url($imageUrl)){
                return $imageUrl;
            }
        }

        hidove_log($res);
        return '上传失败';
    }
}