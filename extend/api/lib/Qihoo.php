<?php
/**
 * FILE_NAME: Qihoo.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019/10/6 10:40
 */

namespace api\lib;

use api\ImageApi;

class Qihoo implements ImageApi
{
    /**
     * 奇虎图床
     * 2019年9月18日01:32:17
     */
    public function upload($pathName)
    {
        $imageInfo = get_image_info($pathName);
        $file = curl_file_create($pathName, $imageInfo['mime'], 'hidove.' . $imageInfo['type']);
        $data['upload'] = $file;
        $UploadUrl = 'https://st.so.com/stu';
        $result = $this->HidovePostLocation($UploadUrl, $data, 'http://st.so.com/');
        if ($result['info']['redirect_url'] !== '') {
            preg_match('~imgkey=(.+?)&~', $result['info']['redirect_url'], $matches);
            if (!empty($matches[1]))
                return 'https://ps.ssl.qhmsg.com/' . $matches[1];
        }
        hidove_log($result);
        return '上传失败！';
    }

    private function HidovePostLocation($url, $post, $referer, $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
    ]
    )
    {
        // 创建一个新 cURL 资源
        $curl = curl_init();
        // 设置URL和相应的选项
        // 需要获取的 URL 地址
        curl_setopt($curl, CURLOPT_URL, $url);
        #启用时会将头文件的信息作为数据流输出。
        curl_setopt($curl, CURLOPT_HEADER, false);
        #设置头部信息
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        #在尝试连接时等待的秒数。设置为 0，则无限等待。
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        #允许 cURL 函数执行的最长秒数。
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        #设置请求信息
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        #设置referer
        curl_setopt($curl, CURLOPT_REFERER, $referer);
//    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36');
        #关闭ssl
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        #TRUE 将 curl_exec获取的信息以字符串返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // 抓取 URL 并把它传递给浏览器
        $return = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        return [
            'data' => $return,
            'info' => $info,
        ];
    }
}