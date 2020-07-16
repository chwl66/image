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
use think\facade\Cache;

class Qcoral implements ImageApi
{
    public function upload($pathName)
    {
        $imageInfo = get_image_info($pathName);
        $UploadUrl = 'https://upload.coral.qq.com/image/upload';
        $imageMime = $imageInfo['mime'];
        $imageType = $imageInfo['type'];
        $imageData = file_get_contents($pathName);
        $cookie = hidove_config_get('api.qpic.coral');
        $skey = get_mid_str($cookie, 'skey=', ';');
        $g_tk = $this->getGTK($skey);
        $data = <<<eot
------WebKitFormBoundary0txTkLuQtALgBABi
Content-Disposition: form-data; name="picture"; filename="hidove.$imageType"
Content-Type: $imageMime

$imageData
------WebKitFormBoundary0txTkLuQtALgBABi
Content-Disposition: form-data; name="type"

1
------WebKitFormBoundary0txTkLuQtALgBABi
Content-Disposition: form-data; name="_method"

put
------WebKitFormBoundary0txTkLuQtALgBABi
Content-Disposition: form-data; name="code"

0
------WebKitFormBoundary0txTkLuQtALgBABi
Content-Disposition: form-data; name="source"

1
------WebKitFormBoundary0txTkLuQtALgBABi
Content-Disposition: form-data; name="g_tk"

$g_tk
------WebKitFormBoundary0txTkLuQtALgBABi
eot;
        $headers = array(
            'Content-Type: multipart/form-data; boundary=----WebKitFormBoundary0txTkLuQtALgBABi',
            'Cookie: ' . $cookie,
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36'
        );
        $result = hidove_post($UploadUrl, $data, 'https://v.qq.com/frames/coral/video.html', $headers);
        if (strpos($result, 'qpic.cn') !== false) {
            $result = json_decode($result, true);
            if (empty($result['data']['picture'][0]['url'])) return '上传失败！';
            $imageUrl = $result['data']['picture'][0]['url'] . '/0';
            $imageUrl = str_replace('http://', 'https://', $imageUrl);
            return $imageUrl;
        } else {
            if (!empty($result['errorMsg'])) {
                $this->sendMailReminder($result['errorMsg'],"Qpic");
                return '上传失败！' . $result['errorMsg'];
            }else{
                $this->sendMailReminder(null,"Qpic");
                return '上传失败！';
            }
            //{"errCode":8,"errorMsg":"not Login yet","info":{"time":1581831901}}
        }
    }

    private function getGTK($access_token)
    {
        $hash = 2013;
        for ($i = 0; $i < strlen($access_token); ++$i) {
            $hash += ($hash << 5) + $this->utf8_unicode($access_token[$i]);
        }
        return $hash & 0x7fffffff;
    }

    private function utf8_unicode($c)
    {
        switch (strlen($c)) {
            case 1:
                return ord($c);
            case 2:
                $n = (ord($c[0]) & 0x3f) << 6;
                $n += ord($c[1]) & 0x3f;
                return $n;
            case 3:
                $n = (ord($c[0]) & 0x1f) << 12;
                $n += (ord($c[1]) & 0x3f) << 6;
                $n += ord($c[2]) & 0x3f;
                return $n;
            case 4:
                $n = (ord($c[0]) & 0x0f) << 18;
                $n += (ord($c[1]) & 0x3f) << 12;
                $n += (ord($c[2]) & 0x3f) << 6;
                $n += ord($c[3]) & 0x3f;
                return $n;
        }
    }


    private function sendMailReminder($msg = 'Cookie已失效！请及时更新Cookie！',$apiType = "Qpic")
    {
        $emailReminder = Cache::get($apiType.'EmailReminder');
        $date = date('Y-m-d H:i:s');
        $lastEmailReminderDate = date('Y-m-d H:i:s', $emailReminder);
        $subject = "【重要】 $date $apiType CDN 出现错误！";
        $content =<<<EOT
apiType：$apiType  错误信息：$msg<br/>
当前时间：  $date
上次发送邮件时间：$lastEmailReminderDate
<br/><p style="color: red;">如未及时更新，本系统将一小时提醒一次！</p>
EOT;

        if (time() - $emailReminder > 3600) {
            Cache::set($apiType.'EmailReminder', time());

            (new imageApiProvider())->sendMailReminder($content,__CLASS__);
        }
    }

}