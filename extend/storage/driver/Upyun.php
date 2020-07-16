<?php
/**
 * FILE_NAME: Upyun.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019年10月30日15:13:35
 */

namespace storage\driver;

use Exception;
use storage\Storage;

class Upyun implements Storage
{
    /**
     * Upyun 上传
     */
    protected $options;
    protected $handle;
    protected $remoteName;

    public function __construct($options)
    {
        $this->options = $options;

        $serviceConfig = new \Upyun\Config(
            $this->options['ServiceName'],
            $this->options['OperatorName'],
            $this->options['OperatorPwd']
        );
        $this->handle = new \Upyun\Upyun($serviceConfig);
    }

    public function upload($realPath, $newPath)
    {
        //上传所在完整路径
        $this->handle->write($newPath, fopen($realPath, 'r'));
        //上传文件成功!
        return true;
    }

    public function delete($fileName)
    {
        $this->handle->delete($fileName);
        return true;
    }

    public function deletes($fileList)
    {
        foreach ($fileList as $key => $value) {
            $this->remoteName = str_replace('images/', '', $value);
            $res = $this->handle->delete($this->remoteName);
            if (!$res){
                $err[] = $res;
            }
        }
        if (!empty($err)){
            throw new Exception(implode(',',$err));
        }
        return true;
    }

}