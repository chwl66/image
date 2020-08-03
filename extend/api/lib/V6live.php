<?php
/**
 * FILE_NAME: V6.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年6月17日10:27:07
 */

namespace api\lib;

use api\ImageApi;
use think\facade\Log;

class V6live implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);
        $data['pid'] = 1001;

        $headers = array(
            'Content-Type:multipart/form-data',
            'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
        );
        $res = hidove_post('https://pic.v.6.cn/api/uploadForGeneral.php', $data,
            'https://v.6.cn', $headers);
        $result = json_decode($res);
        if (!empty($result->content->url->link))
            return $result->content->url->link;

        hidove_log($res);
        if (!empty($result->content)) return $result->content;
        return '上传失败!';
    }
}