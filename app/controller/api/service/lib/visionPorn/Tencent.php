<?php


namespace app\controller\api\service\lib\visionPorn;



use think\facade\Filesystem;

class Tencent
{
    private $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param $url
     * @return false|float|int
     */
    public function run($pathName)
    {
        // 图片base64编码
        $base64 = base64_encode(Filesystem::read($pathName));
        // 设置请求数据
        $appkey = $this->config['tencent']['appkey'];
        $params = [
            'app_id' => $this->config['tencent']['appid'],
            'image' => $base64,
            'time_stamp' => strval(time()),
            'nonce_str' => strval(rand()),
            'sign' => '',
        ];
        $params['sign'] = $this->getTencentReqSign($params, $appkey);
        // 执行API调用
        $url = 'https://api.ai.qq.com/fcgi-bin/vision/vision_porn';
        $response = hidove_post($url, $params);
        $response = json_decode($response, true);
        $fraction = 75;
        foreach ($response['data']['tag_list'] as $key => $value) {
            if ($value['tag_name'] == 'normal_hot_porn') {
                $fraction = $value['tag_confidence'];
            }
        }
        return $fraction;
    }

    /** 根据 接口请求参数 和 应用密钥 计算 请求签名
     * @param $params array 接口请求参数（特别注意：不同的接口，参数对一般不一样，请以具体接口要求为准）
     * @param $appkey string 应用密钥
     * @return string 签名结果
     */
    private function getTencentReqSign($params, $appkey)
    {
        // 1. 字典升序排序
        ksort($params);
        // 2. 拼按URL键值对
        $str = '';
        foreach ($params as $key => $value) {
            if ($value !== '') {
                $str .= $key . '=' . urlencode($value) . '&';
            }
        }
        // 3. 拼接app_key
        $str .= 'app_key=' . $appkey;
        // 4. MD5运算+转换大写，得到请求签名
        $sign = strtoupper(md5($str));
        return $sign;
    }
}