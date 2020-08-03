<?php
/**
 * FILE_NAME: Sougou.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Sougou implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);

        $UploadUrl = 'http://pic.sogou.com/ris_upload?r=' . rand(10000, 99999);
        $res = hidove_post($UploadUrl, $data, 'http://pic.sogou.com');
        preg_match('~query=(.+?)&~', $res, $ImageUrl);
        if (isset($ImageUrl[1]))
            return str_replace('http://', 'https://', urldecode($ImageUrl[1]));
        hidove_log($res);
        return '上传失败！';
    }
}