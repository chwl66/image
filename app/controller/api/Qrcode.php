<?php


namespace app\controller\api;


use think\facade\Request;

class Qrcode
{
    public function get(){

        $text = Request::param('text');
        if (empty($text)){
            return msg(400,'The text can\'t be null!');
        }
        return  response((new \app\provider\Qrcode())->writeString($text))->contentType('image/png');
    }

}