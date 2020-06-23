<?php


namespace app\provider;


class WeightRand
{

    private $data;


    // 权重数值越高，被返回的概率越大
    public function run($data)
    {
        $this->data = $data;

        //array_column 取出指定的列
        $column = array_column($this->data, 'weight');
        //计算总权重
        $weightSum = array_sum($column);
        //打乱数组
        shuffle($this->data);
        $result = $this->weightRandClosure($this->data, $weightSum);
        return $result;
    }

    /**递归获取有效cdn地址
     * @param $data array
     * @param $weightSum
     * @return mixed|string
     */
    private function weightRandClosure($data, $weightSum)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if($weightSum <= 0)
                return [];
            $randNum = mt_rand(1, $weightSum);
            if ($randNum <= $value['weight']) {
                $result = $value;
                break;
            } else {
                $weightSum -= $value['weight'];
            }
        }
        if (empty($result) && !empty($data)) {
            return $this->weightRandClosure($data, $weightSum);
        }
        return $result;
    }
}