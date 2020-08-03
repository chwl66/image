<?php
/**
 * FILE_NAME: Suning.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:38
 */

namespace api\lib;

use api\ImageApi;


class Suning implements ImageApi
{

    /**
     * 苏宁图床
     * 2019年9月18日02:24:43
     */
    public function upload($pathName)
    {
        //http://review.suning.com/imageload/uploadImg.do
        $imageInfo = get_image_info($pathName);
        $data = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"Filedata\"; filename=\""
            . 'hidove.' . $imageInfo['type'] . "\"\r\nContent-Type: "
            . $imageInfo['type'] . "\r\n\r\n"
            . file_get_contents($pathName) . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"omsOrderItemId\"\r\n\r\n" . mt_rand(10, 100000) . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"custNum\"\r\n\r\n1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"deviceType\"\r\n\r\n1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--\r\n";
        $hearder = [
            'content-type:multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW',
            'Content-Type:application/x-www-form-urlencoded',
            'cache-control:no-cache',
            'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50'
        ];
        $UploadUrl = 'http://review.suning.com/imageload/uploadImg.do';
        $res = hidove_post($UploadUrl, $data, 'http://www.suning.com', $hearder);
        $result = json_decode($res, true);
        if (!empty($result['src'])) {
            $imageUrl = 'https:' . $result['src'] . '.jpg';
            return $imageUrl;
        }
        hidove_log($res);
        return '上传失败！';
    }
}