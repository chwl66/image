<?php


namespace app\exception;


use app\provider\Error;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

class Http extends Handle
{
    public function render($request, Throwable $e): Response
    {

        if ($e instanceof ValidateException) {
            if ($request->isAjax()) {
                return msg(401, $e->getMessage() . ',请重新提交以获取新令牌')
                    ->code(200);
            }
        }
        if ($this->app->isDebug()) {
            // 交给系统处理
            return parent::render($request, $e);
        }

        // 请求异常
        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();

            $template = $this->app->config->get('app.http_exception_template');

            if (!$this->app->isDebug() && !empty($template[$status])) {
                //渲染错误页面
                return Response::create($template[$status], 'view', $status)->assign(['e' => $e]);
            }
            if ($request->isAjax()) {
                return msg($status, $e->getMessage())
                    ->code($status);
            }
            if (in_array($status, [410])) {
                return (new Error())->response($status);
            }
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}