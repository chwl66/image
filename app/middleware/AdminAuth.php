<?php
declare (strict_types=1);

namespace app\middleware;


use app\model\User;
use think\facade\Session;

class AdminAuth
{

    public function handle($request, \Closure $next)
    {
        $userId = Session::get('userId');

        if (empty($userId)) {
            if ($request->isAjax()){
                return msg(401,'请登录');
            }
            return redirect((string)url('admin.login/index'));
        }
        $user = User::where([
            ['id', '=', $userId],
            ['group_id', '=', 2],
        ])->findOrEmpty();
        if ($user->isEmpty()) {
            if ($request->isAjax()){
                return msg(401,'请登录');
            }
            return redirect((string)url('admin.login/index'));
        }
        $request->userId = $userId;
        $request->user = $user;
        return $next($request);
    }
}
