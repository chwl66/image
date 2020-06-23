<?php
declare (strict_types=1);

namespace app\middleware;

use app\model\User;
use think\facade\Request;

class TokenAuth
{
    public function handle($request, \Closure $next)
    {

        $token = Request::param('token');
        if (empty($token)) {
            return msg(400, 'This token can\'t be null!');
        }
        $token = trim($token);

        $model = User::where('token', $token)->findOrEmpty();

        if ($model->isEmpty()) {

            return msg(400, 'This token is illegal!');
        }
        $request->user = $model;
        return $next($request);
    }
}
