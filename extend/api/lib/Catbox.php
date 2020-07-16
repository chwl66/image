<?php
/**
 * FILE_NAME: Catbox.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Catbox implements ImageApi
{
    public function upload($pathName)
    {
        $data['fileToUpload'] = new \CURLFile($pathName);

        $data['reqtype'] = 'fileupload';
        $result = hidove_post('https://catbox.moe/user/api.php', $data, 'https://catbox.moe/user/api.php');
        if (strpos($result,' ') || empty($result)) {
            return '上传失败！';
        } else {
            return $result;
        }
    }
}