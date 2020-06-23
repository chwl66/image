<?php


namespace app\controller\api\service\lib\visionPorn;


use think\Exception;
use think\facade\Cache;
use think\facade\Filesystem;

class Baidu
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
        //获取access_token
        //https://aip.baidubce.com/oauth/2.0/token
        $accessToken = Cache::get('baidu_access_token');
        if (!$accessToken) {
            $response = hidove_post('https://aip.baidubce.com/oauth/2.0/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->config['baidu']['client_id'],
                'client_secret' => $this->config['baidu']['client_secret']
            ]);
            $response = json_decode($response);
            $accessToken = $response->access_token;
            if (empty($accessToken)) {
                throw new Exception("获取accessToken失败，请检查client_id和client_secret", 10006);
            }
            Cache::set('baidu_access_token', $accessToken, 86400 * 28);
        }
        //图片审核
        //https://aip.baidubce.com/rest/2.0/solution/v1/img_censor/v2/user_defined
        $response = hidove_post(
            'https://aip.baidubce.com/rest/2.0/solution/v1/img_censor/v2/user_defined',
            [
                'access_token' => $accessToken,
                'image' => base64_encode(Filesystem::read($pathName)),
            ], 'https://www.baidu.com', ['Content-Type:application/x-www-form-urlencoded']);
        $response = json_decode($response);
        switch ($response->conclusion) {
            case '合规':
                $fraction = 0;
                break;
            case '不合规':
                $fraction = 100;
                break;
            case '疑似':
                $fraction = 75;
                break;
            default:
                $fraction = 75;
        }
        return $fraction;
    }
}