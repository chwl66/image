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
        $result = hidove_post($UploadUrl, $data);
        $result = json_decode($result, true);
        //token失效
        if (empty($result)) {
            $cookie = hidove_config_get('api.chaoxing.cookie');
            $hidove_get = hidove_get('http://pan.ananas.chaoxing.com/tk', [
                'cookie:' . $cookie
            ]);
            if (strpos($hidove_get, 'tk=') !== 0) {
                (new imageApiProvider())->sendMailReminder($data['token'], __CLASS__);
                return '上传失败！';
            }
            $data['token'] = get_mid_str($hidove_get,'tk=\'','\';');
            Cache::set('upload_api_chaoxing', $data['token']);
            $result = hidove_post($UploadUrl, $data);
            $result = json_decode($result, true);
        }
        if (!empty($result['objectid'])) {
            $objectid = $result['objectid'];
        } else if (!empty($result['panMsg']['objectid'])) {
            $objectid = $result['panMsg']['objectid'];
        } else {
            return '上传失败！';
        }
        return 'http://p.ananas.chaoxing.com/star3/origin/' . $objectid;
    }
}