<?php


namespace app\controller\api\service;


use app\model\Api;

class Distribute
{

    private $apiType;
    private $realPath;
    /**
     * @var \think\Collection
     */
    private $apiList;

    public function __construct($apiType, $realPath)
    {
        $this->apiType = $apiType;
        $this->realPath = $realPath;
        $this->apiList = Api::field('key,is_ok')->cache(true,180)->select();
    }

    public function run()
    {
        $distributeLoadBalance = false;

        $apiType = $this->apiType;
        $imageUrl = [];
        //负载均衡
        if (count($this->apiType) > hidove_config_get('system.loadBalance.min')) {
            $distributeLoadBalance = (new DistributeLoadBalance())->run($this->apiType, $this->realPath);
        }
        if ($distributeLoadBalance !== false) {
            $apiType = array_diff($this->apiType, $distributeLoadBalance['apiType']);
            $imageUrl = $distributeLoadBalance['data'];
        }
        foreach ($apiType as $value) {
            $class = '\\api\\lib\\' . ucfirst($value);
            $first = $this->apiList->where('key', $value)->first();
            if (!class_exists($class) || empty($first)) {
                $imageUrl[$value] = '该接口不存在';
            } elseif ($first->is_ok !== 1) {
                $imageUrl[$value] = '该接口已停用';
            } else {
                try {
                    $imageUrl[$value] = (new $class())->upload($this->realPath);
                }catch (\Exception $e){
                    $imageUrl[$value] = $e->getMessage();
                }
            }
        }
        return $imageUrl;
    }

}