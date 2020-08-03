<?php
/**
 * FILE_NAME: Chevereto.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年2月13日21:03:03
 */

namespace api\lib;


use api\ImageApi;

class Imgbb implements ImageApi
{
    public function upload($pathName)
    {
        //Chevereto图床的上传api
        $UploadUrl = 'https://api.imgbb.com/1/upload';
        //填写Chevereto图床的key
        $data['key'] = '';
        $data['format'] = 'json';

        $data['image'] = new \CURLFile($pathName);

        $res = hidove_post($UploadUrl, $data);
        $result = json_decode($res, true);
        if ($result['status'] == 200)
            return $result['data']['url'];

        hidove_log($res);
        return '上传失败！';
    }
}