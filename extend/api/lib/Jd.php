<?php
/**
 * FILE_NAME: Jd.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:27
 */

namespace api\lib;

use api\ImageApi;

class Jd implements ImageApi
{
    public function upload($pathName)
    {
        $data['file'] = new \CURLFile($pathName);

        $hearder = [
            'Accept:application/json',
            'Accept-Encoding:gzip,deflate,sdch',
            'Accept-Language:zh-CN,zh;q=0.8',
            'Connection:close',
            'Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0'
        ];
        $result = $this->HidoveJdPost('https://search.jd.com/image?op=upload', $data, 'http://m.qzone.com/infocenter?g_f=', $hearder);
        preg_match(' ~callback\(\"(.+?)\"\);~', $result, $matches);
        if (!isset($matches[1])) {
            hidove_log($result);
            return '上传失败！';
        } else {
            if (!is_valid_url($matches[1])){
                return 'https://img' . rand(10, 14) . '.360buyimg.com/uba/' . $matches[1];
            }else{
                hidove_log($result);
                return '上传失败！'.$matches[1];
            }
        }
    }
    private function HidoveJdPost($url,$post,$referer,$headers = array('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50'))
    {
        // 创建一个新 cURL 资源
        $curl = curl_init();
        // 设置URL和相应的选项
        // 需要获取的 URL 地址
        curl_setopt($curl, CURLOPT_URL,$url);
        #启用时会将头文件的信息作为数据流输出。
        curl_setopt($curl, CURLOPT_HEADER, false);
//    curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
        #设置头部信息
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        #在尝试连接时等待的秒数。设置为 0，则无限等待。
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        #允许 cURL 函数执行的最长秒数。
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        #设置请求信息
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
        #设置referer
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        #关闭ssl
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        #TRUE 将 curl_exec获取的信息以字符串返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        // 抓取 URL 并把它传递给浏览器
        $return = curl_exec($curl);
        curl_close($curl);
        return $return;
    }
}