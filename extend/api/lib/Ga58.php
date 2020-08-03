<?php
/**
 * FILE_NAME: Ga58.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年6月17日10:27:07
 */

namespace api\lib;

use api\ImageApi;

class Ga58 implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);
        $data['type'] = 'file';

        $headers = array(
            'Content-Type:multipart/form-data',
            'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
        );
        $result = hidove_post('https://58ga.com/api.php?type=file', $data,
            'https://58ga.com', $headers);
        $json = json_decode($result);
        if (isset($json->path)) {
            return $json->path;
        }else {
            hidove_log($result);
            return '上传失败!';
        }
    }
}