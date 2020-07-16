<?php
/**
 * FILE_NAME: Ali.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;


use api\ImageApi;

class Ali implements ImageApi
{
    public function upload($pathName)
    {
//        $imageInfo = getImageInfo($filePath);
        $UploadUrl = 'https://kfupload.alibaba.com/mupload';
        $headers = [
            'Content-Type: multipart/form-data; boundary=----WebKitFormBoundary5zcj0rAM8NsP64PJ',
            'Referer: https://report.aliexpress.com/health/report.htm?type=search&productId=33011978124',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
            'Accept-Language: zh-CN,zh;q=0.9',
        ];
        $data = '------WebKitFormBoundary5zcj0rAM8NsP64PJ
Content-Disposition: form-data; name="name"

k4p9pc4h.jpg
------WebKitFormBoundary5zcj0rAM8NsP64PJ
Content-Disposition: form-data; name="scene"

rfqFileRule
------WebKitFormBoundary5zcj0rAM8NsP64PJ
Content-Disposition: form-data; name="max"

5
------WebKitFormBoundary5zcj0rAM8NsP64PJ
Content-Disposition: form-data; name="min"

0
------WebKitFormBoundary5zcj0rAM8NsP64PJ
Content-Disposition: form-data; name="file"; filename="hidove.jpg"
Content-Type: image/jpeg

' . file_get_contents($pathName) .'

------WebKitFormBoundary5zcj0rAM8NsP64PJ--';
        $result =  hidove_post($UploadUrl, $data, 'http://www.aliexpress.com/', $headers);
        $result = json_decode($result, true);
        if ($result['code'] == 0) {
            $imageUrl = $result['url'];
            return $imageUrl;
        } else {
            return '上传失败！';
        }
    }
}