<?php


namespace app\controller\api\service;


use app\model\Storage;
use think\facade\Filesystem;
use think\helper\Str;

class CdnUpload
{

    private $apiType;
    private $pathName;

    public function __construct($apiType, $pathName,$realPath)
    {
        $this->pathName = $pathName;
        $this->apiType = $apiType;
        $this->realPath = $realPath;
    }

    public function run()
    {
//        $realPath = Filesystem::disk('public')->path($this->pathName);

        $imageUrl = [];
        foreach ($this->apiType as $value) {

            $storageConfig = Storage::where('name', $value)
            ->findOrEmpty();

            if (!$storageConfig->isExists()) {
                continue;
            }
            $class = '\\storage\\driver\\' . ucfirst($storageConfig['driver']);
            if (!class_exists($class)) {
                $imageUrl[$value] = '该cdn驱动不存在';
                continue;
            }
            $storage = new $class($storageConfig['data']);
            try{
                $res = $storage->upload($this->realPath, $this->pathName);
            }catch (\Exception $e){
                $res = $e->getMessage();
            }
            if ($res === true) {
                $cdnDomain = $storageConfig['cdn'];
                if (!Str::endsWith($cdnDomain, '/')) {
                    $cdnDomain = $cdnDomain . '/';
                }
                $imageUrl[$value] = $cdnDomain . $this->pathName;
            } else {
                $imageUrl[$value] = $res;
            }
        }
        return $imageUrl;
    }
}