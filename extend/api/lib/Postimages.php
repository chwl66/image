<?php
/**
 * FILE_NAME: Postimages.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年5月29日10:27:52
 */

namespace api\lib;

use api\ImageApi;

class Postimages implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);

        $data['token'] = '61aa06d6116f7331ad7b2ba9c7fb707ec9b182e8';
        $data['optsize'] = 0;
        $data['expire'] = 0;
        $data['upload_referer'] = 'aHR0cHM6Ly9wb3N0aW1nLmNjL2ZTV0JCRjdGL2IzZDA0NTVi';
        $data['session_upload'] = time();
        $data['numfiles'] = 1;
        $data['upload_session'] = '73U3eTMDVmWUXUbgIn62cdeHgwbAjRJE';

        $res = hidove_post('https://postimages.org/json/rr', $data);
        //{"status":"OK","url":"https:\/\/postimg.cc\/fSWBBF7F\/b3d0455b"}
        $result = json_decode($res);
        if (empty($result->url)) {
            hidove_log($res);
            return '上传失败';
        }
        $hidove_get = hidove_get($result->url);
        //<meta property="og:image" content="https://i.postimg.cc/RVnVKWHF/QQ-20200414180021.png" />
        $url = get_mid_str($hidove_get, '<meta property="og:image" content="', '" />');
        if (!empty($url)) return $url;
        hidove_log($res);
        hidove_log($hidove_get);
        return '上传失败';
    }
}