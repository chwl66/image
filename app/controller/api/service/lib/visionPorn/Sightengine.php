<?php


namespace app\controller\api\service\lib\visionPorn;


use think\facade\Filesystem;

class Sightengine
{    private $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }
    public function run($pathName)
    {
        $data =  [
            'models' => 'nudity',
            'api_user' => $this->config['sightengine']['api_user'],
            'api_secret' => $this->config['sightengine']['api_secret'],
            'media' => ''
        ];
        if (class_exists('CURLFile')) {     // php 5.5
            $data['media'] = new \CURLFile(Filesystem::path($pathName));
        } else {
            $data['media'] = '@' . Filesystem::path($pathName);
        }
        $response = hidove_post('https://api.sightengine.com/1.0/check.json',$data);
        $response = json_decode($response);
        //1.如果nudity.raw≥max（nudity.partial，nudity.safe）
        //  则图像包含原始裸体。
        //2.如果nudity.partial≥max（nudity.raw，nudity.safe）
        //  则图像包含部分裸体。您应该检查nudity.partial_tag以获取有关部分裸露类型的更多详细信息。
        //3.如果1.和2.都不为真，则图像被认为是安全的（无裸露）。
        $fraction = empty($response->nudity->raw) ? 75 : $response->nudity->raw * 100;
        return $fraction;
    }

}