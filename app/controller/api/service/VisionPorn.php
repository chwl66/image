<?php


namespace app\controller\api\service;



class VisionPorn
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run($pathName)
    {

        $config = $this->config;

        if ($config['switch'] != 1) {
            return 0;
        }
        $class  ='\\app\\controller\\api\\service\\lib\\visionPorn\\'.ucfirst($config['type']);
        $hander = new $class($config);
        return $hander->run($pathName);

    }


}