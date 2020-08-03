<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\Group;
use app\model\RechargeCard;
use app\model\User;
use Carbon\Carbon;
use think\facade\Request;
use think\facade\Session;

class UserFinance extends BaseController
{
    private $user;

    protected function initialize()
    {
        $userId = User::get_user_id();;
        $this->user = User::where('id', $userId)->find();
    }

    public function updateGroup()
    {
        $groupId = Request::param('id');
        if (in_array($groupId, [1, 2])) {
            return msg(400, '没有权限升级');
        }
        $model = Group::where('id', $groupId)->findOrEmpty();
        if (!$model->isExists()) {
            return msg(400, '该用户组不存在');
        }
        if ($this->user->finance < $model->price) {
            return msg(400, '您的余额不够，请充值');
        }
        if (in_array($this->user->group_id,[2])){
            return msg(400, '您当前所在用户组无法进行升级');
        }
        $this->user->finance = $this->user->finance - $model->price;
        if ($this->user->expiration_date > time()) {
            $this->user->expiration_date = Carbon::parse($this->user->expiration_date)->addYear(1)->getTimestamp();
        } else {
            $this->user->expiration_date = Carbon::now()->addYear(1)->getTimestamp();
        }
        $this->user->group_id = $groupId;
        $this->user->save();
        return msg(200, 'success');
    }


    public function getGroupList()
    {
        $groupList = Group::whereNotIn('id', [1, 2])->select();
        return msg(200, 'success', $groupList);
    }
    public function recharge(){
        $card = Request::param('card');
        $model = RechargeCard::where([
            ['key', '=', $card],
            ['user_id', '=', 0],
            ['used_time', '=', 0],
        ])->findOrEmpty();
        if (!$model->isExists()){
            return msg(400, '卡密无效');
        }
        $model->user_id = $this->user->id;
        $model->use_time = time();
        $model->save();
        $this->user->finance += $model->denomination;
        $this->user->save();
        return msg(200, 'success');
    }
}