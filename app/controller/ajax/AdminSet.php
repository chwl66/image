<?php


namespace app\controller\ajax;


use app\model\Set;
use app\model\SetGroup;
use think\facade\Cache;
use think\facade\Request;

class AdminSet
{
    public function get()
    {

        $id = Request::param('id');
        $name = Request::param('name');
        if (!empty($name)) {
            $id = SetGroup::where('name', $name)->value('id');
        }
        $model = Set::where('group_id', $id);

        $toArray = $model->select()->toArray();
        array_walk($toArray, function (&$value) {
            if (!empty($value['decode'])) {
                $value['value'] = $value['decode']($value['value']);
            }
        });
        return msg(200, 'success', $toArray);

    }

    public function update()
    {

        $param = Request::param();

        if (empty($param['id']) && empty($param['name'])) {
            return msg(400, 'The id and the name can\'t be null!');
        }
        $name = Request::param('name');
        if (!empty($name)) {
            $id = SetGroup::where('name', $name)->value('id');
        } else {
            $id = $param['id'];
        }
        $model = Set::where('group_id', $id);
        unset($param['id']);
        unset($param['name']);
        $collection = $model->select();
        foreach ($collection as $value) {
            $key = str_replace('.', '_', $value->name);
            if (isset($param[$key])) {

                if (!empty($value->encode)) {
                    $encode = $value->encode;
                    $value->value = $encode($param[$key]);
                } else {
                    $value->value = $param[$key];
                }
                unset($param[$key]);
                $value->save();
            }
        }
        //新增配置
        foreach ($param as $key => $value) {
            Set::create([
                'name' => trim(str_replace('_', '.', $key)),
                'value' => $value,
                'group_id' => $id,
            ]);
        }
        Cache::tag('config')->clear();
        return msg(200, 'success');
    }

    public function delete()
    {

    }

    public function create()
    {

    }

}