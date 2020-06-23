<?php


namespace app\controller\api\service;


use app\provider\WeightRand;
use think\facade\Log;

class DistributeLoadBalance
{

    public function run($apiType, $realPath)
    {
        $config = hidove_config_get('system.loadBalance.');

        if ($config['switch'] != 1) {
            return false;
        }
        $apiType = array_diff($apiType, explode(',',$config['exception']));

        $nodes = $config['node'];
        $nodes = explode("\n", base64_decode($nodes));

        array_walk($nodes, function (&$value, $key) use ($nodes) {
            $value = [
                'weight' => (count($nodes) - $key) * 1000,
                'data' => $value,
            ];
        });
        $data['apiType'] = implode(',',$apiType);
        $node = (new WeightRand())->run($nodes)['data'];
        if (class_exists('CURLFile')) {     // php 5.5
            $data['image'] = new \CURLFile(realpath($realPath));
        } else {
            $data['image'] = '@' . realpath($realPath);
        }
        $response = hidove_post($node, $data);
        $json = json_decode($response,true);
        if (empty($json)) {
            Log::record(__CLASS__ . $response, 'Hidove');
            return false;
        }
        if ($json['code'] == 200) {
            return [
                'data' => $json['data']['url'],
                'apiType' => $apiType,
            ];
        } else {
            Log::record(__CLASS__ . $response, 'Hidove');
            return false;
        }
    }
}