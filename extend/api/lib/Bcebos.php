<?php
/**
 * FILE_NAME: Bcebos.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年4月15日23:07:55
 */

namespace api\lib;

use api\ImageApi;
use api\provider\imageApiProvider;
use think\facade\Config;
use think\facade\Log;

class Bcebos implements ImageApi
{
    public function upload($pathName)
    {
        $cookie = hidove_config_get('api.baidu.bcebos');
        $imageData = file_get_contents($pathName);
        $data =<<<EOT
------WebKitFormBoundarysNY1ZCY0q0rljfT3
Content-Disposition: form-data; name="img"; filename="1297.jpg"
Content-Type: image/jpeg

$imageData
------WebKitFormBoundarysNY1ZCY0q0rljfT3--
EOT;
        $headers = [
            'Content-Type:multipart/form-data; boundary=----WebKitFormBoundarysNY1ZCY0q0rljfT3',
            "Cookie:$cookie",
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36'
        ];
        $UploadUrl = 'https://developer.baidu.com/forum/upload/image';
        $res = hidove_post($UploadUrl, $data, 'https://www.baidu.com', $headers);
        $result = json_decode($res);
        if (isset($result->data->imageUrl)) {
            $imageUrl = $result->data->imageUrl;
            return $imageUrl;
        } else if (isset($result->errorInfo)) {
            $response = '上传失败！' . $result->errorInfo;
        } else {
            $response = '上传失败！';
        }
        hidove_log($res);
        (new imageApiProvider())->sendMailReminder($res,__CLASS__);
        return $response;

    }
}