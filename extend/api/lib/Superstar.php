<?php

namespace api\lib;


use api\imageApi;
use think\facade\Log;

class Superstar implements ImageApi
{

    public function upload($pathName)
    {

        $UploadUrl = 'http://notice.chaoxing.com/pc/files/uploadNoticeFile';
        $data['attrFile'] = new \CURLFile($pathName);
        $res = hidove_post($UploadUrl, $data);
        $result = json_decode($res);
        if (!empty($result->url))
            return str_replace('http://', 'https://', get_left_str($result->url, '?'));
        hidove_log($res);
        return '上传失败！';
    }
}