<?php


namespace app\controller\api\service;


use app\model\Storage;
use think\facade\Filesystem;
use think\helper\Str;

class StorageUpload
{
    private $storageDriver;
    private $pathName;
    private $realPath;

    public function __construct($storageDriver, $pathName,$realPath)
    {
        $this->storageDriver = $storageDriver;
        $this->pathName = $pathName;
        $this->realPath = $realPath;
    }

    public function run()
    {
        $class = '\\storage\\driver\\' . ucfirst($this->storageDriver);

        if (!class_exists($class)) {
            return'该cdn驱动不存在';
        }
        $storageConfig = Storage::where('name',$this->storageDriver)->findOrEmpty();

        if (!$storageConfig->isExists()) {
            return '未配置该cdn,请配置好后重试';
        }
        if (empty($storageConfig['cdn'])) {
            return '未配置cdn域名,请配置好后重试';
        }
        try{
            $upload = (new $class($storageConfig['data']))
                ->upload($this->realPath, $this->pathName);

            if ($upload !== true) {
                return $upload;
            }
            return rtrim($storageConfig['cdn'],'/').'/' . $this->pathName;

        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}