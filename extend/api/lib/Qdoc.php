<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年3月27日16:40:09
// +----------------------------------------------------------------------

namespace api\lib;

use api\ImageApi;

class Qdoc implements ImageApi
{
    public function upload($pathName)
    {
        $UploadUrl = 'https://docs.qq.com/ep/api/attach_local';
        $data['file'] = new \CURLFile($pathName);

        $cookie = hidove_config_get('api.qpic.doc');
        $headers = array(
            'Cookie: ' . $cookie,
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36'
        );
        $res = hidove_post($UploadUrl, $data, 'https://www.qq.com/', $headers);
        $result = json_decode($res);
        if (isset($result->url))
            return get_left_str($result->url, '?');
        hidove_log($res);
        return '上传失败！';
    }

}