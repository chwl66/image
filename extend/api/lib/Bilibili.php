<?php
/**
 * FILE_NAME: Bilibili.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020-4-14 15:47:48
 */

namespace api\lib;

use api\ImageApi;
use api\provider\imageApiProvider;
use think\facade\Config;

class Bilibili implements ImageApi
{
    public function upload($pathName)
    {
        $cookie = hidove_config_get('api.bilibili.drawImage');
        $imageData = file_get_contents($pathName);
        $data = <<<EOT
------WebKitFormBoundary4lBGecdCqNbANSA9
Content-Disposition: form-data; name="file_up"; filename="5e723b67a121d.jpg"
Content-Type: image/jpeg

$imageData
------WebKitFormBoundary4lBGecdCqNbANSA9
Content-Disposition: form-data; name="biz"

draw
------WebKitFormBoundary4lBGecdCqNbANSA9
Content-Disposition: form-data; name="category"

daily
------WebKitFormBoundary4lBGecdCqNbANSA9--
EOT;
        $headers = [
            'Content-Type:multipart/form-data; boundary=----WebKitFormBoundary4lBGecdCqNbANSA9',
            "Cookie:$cookie",
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36'
        ];
        $UploadUrl = 'https://api.vc.bilibili.com/api/v1/drawImage/upload';
        $res = hidove_post($UploadUrl, $data, 'https://www.bilibili.com', $headers);
        $result = json_decode($res);
        if (isset($result->data->image_url)) {
            return str_replace('http', 'https', $result->data->image_url);
        } else if (isset($result->message)) {
            $response = '上传失败！' . $result->message;
        } else {
            $response = '上传失败！';
        }
        (new imageApiProvider())->sendMailReminder($res, __CLASS__);
        return $response;
    }
}