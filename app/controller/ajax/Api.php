<?php


namespace app\controller\ajax;


class Api
{

    public function get()
    {
        $collection = \app\model\Api::order('id', 'asc')->where('is_ok', 1)->select();
        return msg(200, 'success', $collection);
    }

}