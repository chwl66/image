<?php


namespace app\controller\index\service;


use think\facade\Request;

class getHttpCode
{
    /**
     * @var array
     */
    private $config;

    public function run($url){

        $this->config = hidove_config_get('system.distribute.');
        $function = $this->config['api'];

        $code = 200;
        if (method_exists($this,$function)){
            $code = $this->$function($url);
        }
        return $code;
    }


    private function none($url)
    {
        return 200;
    }
    private function local($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_REFERER, Request::server('HTTP_REFERER'));
        curl_setopt($curl, CURLOPT_NOBODY, true);
        #关闭ssl
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        #在尝试连接时等待的秒数。设置为 0，则无限等待。
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        #允许 cURL 函数执行的最长秒数。
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($curl); //开始执行啦～
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); //用完记得关掉他
        return $code;
    }

    /** chinaz
     * @param $url
     * @return false|string
     */
    private function chinaz($url)
    {
        $data = hidove_get('http://mtool.chinaz.com/Tool/PageStatus/?host=' . $url);
        $code = get_mid_str($data, '返回状态码</td><td class="z-tl"><a href="javascript:">', '</a></td></tr>');
        if (empty($code)) {
            return 200;
        }
        return $code;
    }

    /**
     * 第三方api判断httpcode
     * @param $url
     * @return bool|mixed|string
     */
    private function other($url)
    {
        $data = hidove_get($this->config['apiUrl'] . $url);
        if (is_numeric($data)) {
            $code = $data;
        } else {
            $arr = json_decode($data, true);
            if (empty($arr['data']['code'])) {
                $code = 200;
            } else {
                $code = $arr['data']['code'];
            }
        }
        return $code;
    }
}