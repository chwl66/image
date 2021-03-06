<?php
declare (strict_types = 1);

namespace app\middleware;


use app\model\User;

class UserAuth
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
        $userId = User::get_user_id();
        if (empty($userId)){
            return redirect('/user/login');
        }
        $user = User::where('id', $userId)->findOrEmpty();
        if ($user->isEmpty()){
            return redirect('/user/login');
        }
        $request->userId = $userId;
        $request->user = $user;
        return $next($request);
    }
}
