<?php
/**
 * FILE_NAME: Uploadcc.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Uploadcc implements ImageApi
{
    public function upload($pathName)
    {
        //https://upload.cc/image_upload
        $data['uploaded_file[]'] = new \CURLFile($pathName);

        $result = hidove_post('https://upload.cc/image_upload',$data,'https://upload.cc/');
        $result = json_decode($result,true);
        if (!empty($result['success_image'][0]['url'])) {
            $result = 'https://upload.cc/'.$result['success_image'][0]['url'];
        }else{
            $result = '上传失败';
        }
        return $result;
    }
}