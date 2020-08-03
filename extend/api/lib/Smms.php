<?php
/**
 * FILE_NAME: Smms.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 11:16
 */

namespace api\lib;

use api\ImageApi;

class Smms implements ImageApi
{
    /**
     * sm图床
     * 2019年9月8日03:00:27
     */
    public function upload($pathName)
    {
        $data['smfile'] = new \CURLFile($pathName);

        $headers = array(
            'Content-Type:' . 'multipart/form-data',
            'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
//            'Authorization: xxx',
        );
        $res = hidove_post('https://sm.ms/api/v2/upload', $data, 'https://sm.ms/api/v2/upload', $headers);
        $result = json_decode($res, true);
        if (!empty($result['data']))
            return $result['data']['url'];

        if (!empty($result)) {
            if ($result['code'] == 'exception')
                return substr($result['message'], strpos($result['message'], 'http'));

            hidove_log($res);
            return '上传失败！' . $result['message'];
        }
        hidove_log($res);
        return $result;
    }
}