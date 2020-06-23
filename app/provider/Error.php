<?php


namespace app\provider;


class Error
{
    public function response($code = false)
    {
        if (!$code){
            $code = request()->param('code');
        }
        return response(file_get_contents(app()->getRootPath()."/public/static/index/images/$code.png"))
            ->code($code)
            ->contentType('image/png');
    }

}