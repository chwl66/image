<?php


namespace app\controller\api\service;


use think\facade\Filesystem;
use think\helper\Str;

class UserStorageUpload
{

    private $apiType;
    private $pathName;
    private $user;

    public function __construct($apiType, $pathName, $realPath, $user)
    {
        $this->pathName = $pathName;
        $this->apiType = $apiType;
        $this->user = $user;
        $this->realPath = $realPath;
    }

    public function run()
    {
        if (empty($this->apiType)) {
            return [];
        }
//        $realPath = Filesystem::disk('public')->path($this->pathName);

        $imageUrl = [];
        foreach ($this->apiType as $value) {

            $class = '\\storage\\driver\\' . ucfirst($value);
            if (!class_exists($class)) {
                $imageUrl[$value] = '该cdn驱动不存在';
                continue;
            }
            $storageConfig = $this->user->storage[$value];

            if (empty($storageConfig)) {
                $imageUrl[$value] = '未配置该cdn,请配置好后重试';
                continue;
            }
            if (empty($storageConfig['cdn'])) {
                $imageUrl[$value] = '未配置cdn域名,请配置好后重试';
                continue;
            }
            try {
                $upload = (new $class($storageConfig))
                    ->upload($this->realPath, $this->pathName);
                if ($upload !== true) {
                    $imageUrl[$value] = $upload;
                    continue;
                }
                $imageUrl[$value] = rtrim($storageConfig['cdn'], '/') . '/' . $this->pathName;

            } catch (\Exception $e) {
                $imageUrl[$value] = $e->getMessage();
            }
        }
        return $imageUrl;
    }
}