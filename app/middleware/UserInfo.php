<?php
declare (strict_types = 1);

namespace app\middleware;


use app\model\User;
use think\facade\Session;

class UserInfo
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return \think\response\Redirect
     */
    public function handle($request, \Closure $next)
    {
        $userId = Session::get('userId');
        if (empty($userId)){
            return $next($request);
        }
        $user = User::where('id', $userId)->find();
        if ($user->isExists()){
            $request->userId = $userId;
            $request->user = $user;
        }
        return $next($request);
    }
}
