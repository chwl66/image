<?php
/**
 * FILE_NAME: Chaoxing.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年6月1日18:19:32
 */

namespace api\lib;


use api\ImageApi;
use api\provider\imageApiProvider;
use think\facade\Cache;

class Chaoxing implements ImageApi
{
    public function upload($pathName)
    {
        $UploadUrl = 'http://cloud.ananas.chaoxing.com/h5_upload';

        $data['token'] = Cache::get('upload_api_chaoxing');;

        $data['file'] = new \CURLFile($pathName);

        $data ['v'] = '';
        $data ['d'] = '';
        $res = hidove_post($UploadUrl, $data);
        $json = json_decode($res, true);
        //token失效
        if (empty($json)) {
            $cookie = hidove_config_get('api.chaoxing.cookie');
            $hidove_get = hidove_get('http://pan.ananas.chaoxing.com/tk', [
                'cookie:' . $cookie
            ]);
            if (strpos($hidove_get, 'tk=') !== 0) {
                hidove_log($res);
                hidove_log($hidove_get);
                (new imageApiProvider())->sendMailReminder($data['token'], __CLASS__);
                return '上传失败！';
            }
            $data['token'] = get_mid_str($hidove_get,'tk=\'','\';');
            Cache::set('upload_api_chaoxing', $data['token']);
            $res = hidove_post($UploadUrl, $data);
            $json = json_decode($res, true);
        }
        if (isset($json['objectid'])) {
            $objectid = $json['objectid'];
        } else if (isset($json['panMsg']['objectid'])) {
            $objectid = $json['panMsg']['objectid'];
        } else {
            hidove_log($res);
            return '上传失败！';
        }
        return 'http://p.ananas.chaoxing.com/star3/origin/' . $objectid;
    }
}