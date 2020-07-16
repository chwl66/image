<?php
/**
 * FILE_NAME: Sina.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年3月21日21:14:15
 */

namespace api\lib;

use api\ImageApi;
use think\facade\Cache;
use think\facade\Config;

class Sina implements ImageApi
{

    public function upload($pathName)
    {
//        $UploadUrl = 'https://picupload.weibo.com/interface/pic_upload.php?data=1&p=1&url=0&markpos=1&logo=1&nick=%40Hidove&marks=1&app=miniblog&s=json&pri=0&file_source=2';
        $UploadUrl = 'https://picupload.weibo.com/interface/pic_upload.php?data=1&p=1';
        $Cookie = Cache::remember('upload_api_sina', function () {
            return $this->login(hidove_config_get('api.sina.username'), hidove_config_get('api.sina.password'));
        });
        $headers = [
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36',
            'Content-Type: multipart/form-data',
            "Cookie: $Cookie"
        ];
        $data = file_get_contents($pathName);
        $result = hidove_post($UploadUrl, $data, ' https://d.weibo.com/?topnav=1&mod=logo&wvr=6', $headers);
        $result = json_decode($result);
        if (!empty($result->data->pics->pic_1->pid)) {
            $imageUrl = 'https://tva' . mt_rand(1, 4) . '.sinaimg.cn/large/' . $result->data->pics->pic_1->pid . '.jpg';
            return $imageUrl;
        } else {
            Cache::delete('upload_api_sina');
            if (!$Cookie) {
                return '上传失败！Cookie获取失败！';
            }
            return '上传失败！';
        }
    }

    /**
     * 新浪微博登录(无加密接口版本)
     * @param string $u 用户名
     * @param string $p 密码
     * @return string    返回最有用最精简的cookie
     */
    private function login($u, $p)
    {
        $loginUrl = 'https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.15)&_=1403138799543';
        $loginData['entry'] = 'sso';
        $loginData['gateway'] = '1';
        $loginData['from'] = 'null';
        $loginData['savestate'] = '30';
        $loginData['useticket'] = '0';
        $loginData['pagerefer'] = '';
        $loginData['vsnf'] = '1';
        $loginData['su'] = base64_encode($u);
        $loginData['service'] = 'sso';
        $loginData['sp'] = $p;
        $loginData['sr'] = '1920*1080';
        $loginData['encoding'] = 'UTF-8';
        $loginData['cdult'] = '3';
//        $loginData['domain'] = 'sina.com.cn';
        $loginData['domain'] = 'm.weibo.cn';
        $loginData['prelt'] = '0';
        $loginData['returntype'] = 'TEXT';
        return $this->loginPost($loginUrl, $loginData);
    }

    /**
     * 发送微博登录请求
     * @param string $url 接口地址
     * @param array $data 数据
     * @return json         算了，还是返回cookie吧//返回登录成功后的用户信息json
     */
    private function loginPost($url, $data)
    {
        $tmp = '';
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $tmp .= $key . "=" . $value . "&";
            }
            $post = trim($tmp, "&");
        } else {
            $post = $data;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $return = curl_exec($ch);
        curl_close($ch);
        $cookie = get_mid_str($return, "Set-Cookie: SUB", '; ');
        if (empty($cookie)) {
            return false;
        }
        $cookie = 'SUB' . $cookie . ';';
        return $cookie;
    }
}