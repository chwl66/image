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
        $result = hidove_post($UploadUrl, $data);
        $result = json_decode($result);
        if (!empty($result->url)) {
            return str_replace('http://','https://',get_left_str($result->url,'?'));
        } else {
            Log::record(json_encode($result),'Hidove');
            return '上传失败！';
        }
    }
}