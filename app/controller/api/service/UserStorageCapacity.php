<?php


namespace app\controller\api\service;


use app\model\Folders;
use think\Exception;

class UserStorageCapacity
{
    private $user;
    private $folderId;

    public function __construct($user)
    {
        $this->user = $user;
        $this->folderId = get_param('folder');

    }

    public function run()
    {
        if ($this->user->username == '游客') {
            return;
        }

        //进行用户组过期检测
        if ($this->user->expiration_date < time() && !in_array($this->user->group_id, [1, 2])) {
            $this->user->group_id = 1;
            $this->user->save();
        }

        //检测储存空间是否已满
        if ($this->user->capacity_used >= $this->user->group->capacity) {
            throw new Exception('用户储存空间已满', 10006);
        }
        //上传到指定目录
        if (!empty($this->folderId)) {
            $this->user->api_folder_id = $this->folderId;
        }
        if ($this->user->api_folder_id != 0) {
            $folderInfo = Folders::where([
                'id' => $this->user->api_folder_id,
                'user_id' => $this->user->id
            ])->findOrEmpty();
            if (!$folderInfo->isExists()) {
                $this->user->api_folder_id = 0;
            }
        }
        $this->user->save();
    }

}