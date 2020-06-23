<?php


namespace app\provider;


class Table
{

    public function run($data)
    {
        $data = $this->to2VArray($data);
        $table = new \think\console\Table();
        $table->setHeader([
            'name', 'value'
        ]);
        $table->setRows($data);
        $render = $table->render();
        return $render;
    }

    private function to2VArray($data)
    {

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (!is_numeric($k)){
                        $result[] = [
                            "$key.$k", $v
                        ];
                    }else{
                        $result[] = [
                            $key, implode(',',$value)
                        ];
                    }
                }
            } else {
                $result[] = [
                    $key, $value
                ];
            }
        }
        return $result;
    }
}