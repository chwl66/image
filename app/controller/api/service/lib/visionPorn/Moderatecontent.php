<?php


namespace app\controller\api\service\lib\visionPorn;


use think\facade\Request;

class Moderatecontent
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
        $api = 'https://www.moderatecontent.com/api/v2?key=' . $this->config['moderatecontent']['key'] . '&url=' . Request::domain() . '/images/' . $pathName;
        $response = hidove_get($api);
        $response = json_decode($response);
        $fraction = empty($response->predictions->adult) ? 75 : round($response->predictions->adult, 2);
        return $fraction;
    }
}