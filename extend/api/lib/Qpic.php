<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2019年11月4日18:57:23
// +----------------------------------------------------------------------

namespace api\lib;

use api\ImageApi;
use api\provider\imageApiProvider;

class Qpic implements ImageApi
{
    public function upload($pathName)
    {
        $UploadUrl = 'http://bar.video.qq.com/cgi-bin/fans_admin_upload_pic';
        $data['picture'] = new \CURLFile($pathName);

        $cookie = '';
        $res = hidove_post($UploadUrl, $data, $UploadUrl, [
            "cookie:$cookie"
        ]);
        $res = get_mid_str($res, 'fansAdminImgCallback(', ');');
        $result = json_decode($res);
        if (isset($result->data->strUrl) && is_valid_url($result->data->strUrl))
            return str_replace('http://', 'https://', $result->data->strUrl);

        (new imageApiProvider())->sendMailReminder($res, __CLASS__);

        hidove_log($res);
        if (isset($result->strErrMsg)) return '上传失败！' . $result->strErrMsg;
        return '上传失败！';
    }
}