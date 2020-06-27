<?php


namespace app\controller\ajax;


class AdminTemplate
{

    public function get()
    {
        $pathArr = glob(app()->getRootPath() . '/template/*');
        array_walk($pathArr, function (&$v) {
            if (!is_dir($v))
                unset($v);
            $v = basename($v);
        });
        return msg(200, 'success', [
            'current' => hidove_config_get('system.base.template'),
            'list' => $pathArr
        ]);
    }
}