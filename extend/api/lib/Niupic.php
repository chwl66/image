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

        $result = hidove_post('https://niupic.com/index/upload/process', $data, 'https://niupic.com/');
        $result = json_decode($result,true);
        if (isset($result['data'])){
            $imageUrl =  'https://'.$result['data'];
            if (filter_var($imageUrl,FILTER_VALIDATE_URL)){
                return $imageUrl;
            }
        }
        return '上传失败';
    }
}