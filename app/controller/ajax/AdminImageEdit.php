<?php


namespace app\controller\ajax;


use think\facade\Filesystem;
use think\facade\Request;
use think\facade\Validate;

class AdminImageEdit
{

    public function upload(){
        $file = Request::file('file');
        $validate = Validate::rule([
            'file' => 'require|image|file|fileSize:204800'
        ]);
        if (!$validate->check(['file'=>$file])){
            return msg(400,$validate->getError());
        }
        Filesystem::putFileAs('watermark',$file,'watermark');
        return msg(200,'success');
    }
}