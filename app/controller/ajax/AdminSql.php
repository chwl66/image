<?php


namespace app\controller\ajax;


use think\facade\Db;
use think\facade\Request;

class AdminSql
{
    public function execute(){
        $sql = Request::param('sql');
        try {
            $lines = explode("\n",$sql);
            $tmp = '';
            $number = 0;
            foreach ($lines as $key => &$line) {
                $line = trim($line);
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*') {
                    unset($lines[$key]);
                    continue;
                }
                $tmp .= $line;
                if (substr($line, -1, 1) == ';') {
                    unset($lines[$key]);
                    $number += Db::execute($tmp);
                    $tmp = '';
                }
                unset($lines[$key]);
            }
            return msg(200,'影响记录行数：'.$number);
        }catch (\Exception $e){
            return msg(400,$e->getMessage());

        }
    }

}