<?php
/**
 * FILE_NAME: Bjbcebos.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Bjbcebos implements ImageApi
{
    //https://bj.bcebos.com
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);
        $UploadUrl = 'https://zhiqiu.baidu.com/imcswebchat/api/file/upload';
        $result= hidove_post($UploadUrl, $data);
        $hidove_post = json_decode($result);
        if (!empty($hidove_post->url)) {
            return $hidove_post->url;
        } else if (!empty($hidove_post->msg)) {
            $res = '上传失败' . $hidove_post->msg;
        } else {
            $res = '上传失败';
        }
        hidove_log($result);
        return $res;


    }

}
