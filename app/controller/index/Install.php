<?php


namespace app\controller\index;


use PDO;
use think\Exception;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Validate;
use think\facade\View;

class Install
{
    private $error = '';

    public function index($step = 1)
    {

        // 检测是否已安装
        if (file_exists(app()->getRootPath() . 'install.lock') && $step != 5) {
            exit('你已安装成功，需要重新安装请删除 install.lock 文件');
        }
        // 检查安装环境
        $requirement = [
            'PHP_VERSION' => PHP_VERSION >= 7.1,
            'pdo_mysql' => extension_loaded("pdo_mysql"),
            'Zend OPcache' => extension_loaded("Zend OPcache"),
            'gd' => extension_loaded("gd"),
            'curl' => extension_loaded("curl"),
            'fileinfo' => extension_loaded("fileinfo"),
            'ZipArchive' => class_exists("ZipArchive"),
            'is_writable' => is_writable(app()->getRuntimePath()) && is_writable(app()->getRootPath() . 'public'),
        ];
        if (in_array(false, $requirement)) {
            $step = 1;
        }
        $param = Request::param();
        switch ($step) {
            case 1:
                //环境检查
                View::assign('requirement', $requirement);
                break;
            case 2:
                if (!Request::isPost()) {
                    break;
                }
                $env = [
                    'mysql_HOSTNAME',
                    'mysql_DATABASE',
                    'mysql_USERNAME',
                    'mysql_PASSWORD',
                    'mysql_HOSTPORT',
                ];
                $validate = Validate::rule([
                    'mysql_HOSTNAME|数据库连接地址' => 'require',
                    'mysql_DATABASE|数据库名' => 'require',
                    'mysql_USERNAME|数据库用户名' => 'require',
                    'mysql_PASSWORD|数据库密码' => 'require',
                    'mysql_HOSTPORT|数据库连接端口' => 'require|number',
                ]);
                if (!$validate->check($param)) {
                    $this->error = $validate->getError();
                    break;
                }
                $dsn = 'mysql:host=' . $param['mysql_HOSTNAME'] . ';dbname=' . $param['mysql_DATABASE'] . ';port=' . $param['mysql_HOSTPORT'] . ';charset=utf8';
                try {
                    new PDO($dsn, $param['mysql_USERNAME'], $param['mysql_PASSWORD']);
                } catch (\Exception $e) {
                    $this->error = $e->getMessage();
                    break;
                }
                try {
                    $envFile = file_get_contents(app()->getRootPath() . '.example.env');

                    foreach ($env as $value) {
                        $envFile = str_ireplace('{{' . $value . '}}', $param[$value], $envFile);
                    }
                    file_put_contents(app()->getRootPath() . '.env', $envFile);
                } catch (\Exception $e) {
                    $this->error = $e->getMessage();
                    break;
                }
                return redirect('/install?step=3');
                break;
            case 3:
                //写入数据库
                if (!Request::isPost()) {
                    Cache::delete('install_sql');
                    break;
                }
                return $this->installSql();
                break;
            case 4:
                //配置管理信息
                if (!Request::isPost()) {
                    break;
                }

                $validate = Validate::rule([
                    'MASTER_DOMAIN|主站域名' => 'require',
                    'AUTH_TOKEN|授权码' => 'require',
                    'username|管理员用户名' => 'require|alphaNum|length:5,26',
                    'password|管理员密码' => 'require|alphaDash|length:6,26',
                    'password_confirm|管理员密码' => 'require|confirm:password',
                    'email|邮箱' => 'require|email',
                ]);
                if (!$validate->check($param)) {
                    $this->error = $validate->getError();
                    break;
                }
                try {
                    $env = [
                        'MASTER_DOMAIN',
                        'AUTH_TOKEN',
                    ];
                    $envFile = file_get_contents(app()->getRootPath() . '.env');
                    foreach ($env as $value) {
                        $envFile = str_ireplace('{{' . $value . '}}', $param[$value], $envFile);
                    }
                    file_put_contents(app()->getRootPath() . '.env', $envFile);
                } catch (\Exception $e) {
                    $this->error = $e->getMessage();
                    break;
                }
                //保存管理员信息
                try {
                    $model = \app\model\User::where('id', 1)
                        ->find();
                    $model->username = $param['username'];
                    $model->password = hidove_md5($param['password']);
                    $model->email = $param['email'];
                    $model->token = make_token();
                    $model->create_time = time();
                    $model->group_id = 2;
                    $model->save();
                } catch (\Exception $e) {
                    $this->error = $e->getMessage();
                    break;
                }
                file_put_contents(app()->getRootPath() . 'install.lock', date('Y-m-d H:i:s'));
                return redirect('/install?step=5');
        }
        View::assign('step', $step);
        View::assign('error', $this->error);
        return View::fetch();
    }

    private function installSql()
    {

        $lines = Cache::get('install_sql');
        try {
            if (!$lines) {
                $installSql = app()->getRootPath() . 'install.sql';
                if (!is_file($installSql)) {
                    throw new Exception('数据库 .SQL 文件不存在');
                }
                $lines = file($installSql);
            }
            //写入数据库
            $sql = '';
            $index = 0;
            $log = '';
            foreach ($lines as $key => &$line) {

                $line = trim($line);
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*') {
                    unset($lines[$key]);
                    continue;
                }
                $sql .= $line;
                if (substr($line, -1, 1) == ';') {
                    unset($lines[$key]);
                    Db::execute($sql);
                    $log .= "执行成功：$sql\n";
                    $sql = '';
                    ++$index;
                    if ($index >= 20) {
                        break;
                    }
                }
                unset($lines[$key]);
            }
            Cache::set('install_sql', $lines);
            if (empty($lines)) {
                return msg(200, '数据库写入完毕!');
            } else {
                return msg(100, rtrim($log, "\n"));
            }
        } catch (\Exception $e) {
            return msg(400, $e->getMessage());
        }
    }

}