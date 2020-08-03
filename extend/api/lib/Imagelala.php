<?php
/**
 * FILE_NAME: Chevereto.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年2月13日21:03:03
 */

namespace api\lib;


use api\ImageApi;

class Imagelala implements ImageApi
{
    public function upload($pathName)
    {
        //Chevereto图床的上传api
        $UploadUrl = 'http://chevereto.icdn.best/api/1/upload';
        //填写Chevereto图床的key
        $data['key'] = 'b5a4cfc7b5dabdedbc7a1aacd38a5313';
        $data['format'] = 'json';

        $data['source'] = new \CURLFile($pathName);

        $res = hidove_post($UploadUrl, $data);
        $result = json_decode($result, true);
        if ($result['status_code'] == 200) return $result['image']['url'];
        hidove_log($res);
        return '上传失败！';
    }
}