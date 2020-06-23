<?php


namespace app\controller\index\service;


use app\model\Api;

class ImageFilter
{



    public function __construct($imageUrl,$image)
    {
        $this->imageUrl = $imageUrl;
        $this->image = $image;
    }
    public function run(){

        //去除已关闭的cdn节点
        $apiList = Api::where('is_ok', 1)->order('weight', 'desc')->select();

        //去除用户已禁用的cdn节点
        $forbiddenNode = [];
        if (!empty($this->image->user)){
            $forbiddenNode = $this->image->user->forbidden_node;
        }
        $maxWeight = 1;
        $minWeight = 1;
        $result = [];
        foreach ($apiList as $key => &$value) {
            $value['key'] = strtolower($value['key']);
            if (in_array($value['key'], $forbiddenNode))
                continue;
            if (isset($this->imageUrl[$value['key']])) {
                $result[$value['key']]['key'] = $value['key'];
                $result[$value['key']]['data'] = $this->imageUrl[$value['key']];
                $result[$value['key']]['weight'] = $value['weight'];
                if ($value['weight'] > $maxWeight) $maxWeight = $value['weight'];
                if ($minWeight = 0) $minWeight = $value['weight'];
                if ($value['weight'] < $minWeight) $minWeight = $value['weight'];
            }
        }
        array_walk($this->imageUrl, function ($value, $key) use (&$result, $minWeight, $maxWeight) {
            if (strpos($key, 'Private') !== false) {
                $result[$key]['key'] = $key;
                $result[$key]['data'] = $value;
                $result[$key]['weight'] = ($minWeight + $maxWeight) / 2;
            }
        });
        return $result;
    }

}