<?php


namespace app\controller\api;


use app\BaseController;
use app\middleware\TokenAuth;
use app\model\Folders;
use app\Request;

class FolderList extends BaseController
{

    protected $middleware = [
        TokenAuth::class
    ];
    /**
     * 根据用户id、目录id输出所有目录
     */
    public function get(Request $request)
    {
        $param = $request->param();

        $limit = empty($param['limit']) ? 10 : $param['limit'];
        $page = empty($param['page']) ? 1 : $param['page'];
        $folderList = Folders::where(
            'user_id' , $request->user->id
        )->field('id,name,parent_id,create_time')
            ->page($page, $limit)
            ->select();
        return msg(200, 'success', $folderList);
    }
}