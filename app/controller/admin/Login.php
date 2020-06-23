<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2019年11月4日18:57:23
// +----------------------------------------------------------------------

namespace app\controller\admin;

use app\BaseController;
use think\facade\View;

class Login extends BaseController
{
    public function index(){
        $Hidove['system']['basic'] = hidove_config_get('system.base.');
        $Hidove['system']['other'] = hidove_config_get('system.other.');

        View::assign([
            'Hidove'=>$Hidove
        ]);
        return View::fetch();
    }
}
