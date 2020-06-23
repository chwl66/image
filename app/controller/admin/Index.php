<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月15日19:54:54
// +----------------------------------------------------------------------

namespace app\controller\admin;

use app\BaseController;
use app\middleware\AdminAuth;
use think\facade\View;

class Index extends BaseController
{
    protected $middleware = [
        AdminAuth::class
    ];
    public function index(){
        $Hidove['system']['basic'] = hidove_config_get('system.base.');
        $Hidove['system']['other'] = hidove_config_get('system.other.');

        View::assign([
            'Hidove'=>$Hidove
        ]);
        return View::fetch();
    }
}
