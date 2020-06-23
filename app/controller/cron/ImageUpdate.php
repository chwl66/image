<?php


namespace app\controller\cron;


use app\crontab\Distribute;
use app\Request;

class ImageUpdate
{
    public function index(Request $request)
    {
        $token = $request->param('token');

        if ($token !== hidove_config_get('system.other.superToken')){
            return msg( 400,'The token is illegal!');
        }
        return (new Distribute())->execute();
    }
}