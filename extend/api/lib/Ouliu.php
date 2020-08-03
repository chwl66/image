<?php
/**
 * FILE_NAME: Ouliu.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年5月29日10:09:43
 */

namespace api\lib;

use api\ImageApi;

class Ouliu implements ImageApi
{
    public function upload($pathName)
    {
        $data['ifile'] = new \CURLFile($pathName);


        $result = hidove_post('https://upload.ouliu.net', $data);
        //id="codedirect" class="imgcodebox" value="[\S]+"
        $imageUrl = get_mid_str($result, 'id="codedirect" class="imgcodebox" value="', '" onclick="setTxt');
        if (!empty($imageUrl))
            return str_replace('http://', 'https://', $imageUrl);
        hidove_log($result);
        return '上传失败';
    }
}