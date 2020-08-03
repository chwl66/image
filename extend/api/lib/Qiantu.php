<?php
/**
 * FILE_NAME: Qiantu.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020-6-17 11:13:33
 */

namespace api\lib;

use api\ImageApi;
use think\facade\Log;

class Qiantu implements ImageApi
{
    public function upload($pathName)
    {
        $data['image'] = img2base64($pathName);
        $cookie = hidove_config_get('api.qiantu.cookie');
        $headers = array(
            'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Cookie:' . $cookie,
        );
        $res = hidove_post('https://www.58pic.com/index.php?m=ajaxResume&a=baseUpload', $data,
            'https://www.58pic.com', $headers);
        $result = json_decode($res);
        if (!empty($result->path))
            return 'https:' . $result->path;
        hidove_log($res);
        return '上传失败!';
    }
}