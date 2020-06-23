<?php


namespace app\controller\api;


use app\middleware\TokenAuth;
use app\model\Image;
use app\Request;

class ImageList
{

    protected $middleware = [
        TokenAuth::class
    ];
    /**
     * 根据用户id、目录id输出当前目录所有图片
     */
    public function get(Request $request)
    {
        $param = $request->param();

        $folder = empty($param['folder']) ? 0 : $param['folder'];
        $limit = empty($param['limit']) ? 10 : $param['limit'];
        $page = empty($param['page']) ? 1 : $param['page'];
        $imageList = Image::where([
            'user_id' => $request->user->id,
            'folder_id' => $folder,
        ])->field('signatures,url,filename,image_type,folder_id,mime,file_size,sha1,md5,create_time')
            ->page($page, $limit)
            ->select();
        return msg(200, 'success', $imageList);
    }
}