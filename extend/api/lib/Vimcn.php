<?php
/**
 * FILE_NAME: Vimcnphp
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Vimcn implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);

        $res = hidove_post('https://img.vim-cn.com/', $data, 'https://img.vim-cn.com/');
        $result = preg_replace("~\s~", '', $res);
        $result = str_replace("http://", 'https://', $result);
        if (!is_valid_url($result)) hidove_log($res);
        return $result;
    }
}