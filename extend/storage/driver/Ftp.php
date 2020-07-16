<?php
/**
 * FILE_NAME: Ftp.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年5月9日13:08:03
 */

namespace storage\driver;


use Exception;
use storage\Storage;

class Ftp implements Storage
{
    /**
     * Ftp 上传
     */
    protected $options;
    protected $handle;
    protected $remoteName;
    protected $error;

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    public function __construct($options)
    {
        $this->options = $options;
        try {
            $this->handle = new \lib\Ftp();//实例化对象
            $serverConfig['server'] = $this->options['host'];//服务器地址(IP or domain)
            $serverConfig['username'] = $this->options['username'];//ftp帐户
            $serverConfig['password'] = $this->options['password'];//ftp密码
            $serverConfig['port'] = $this->options['port'];//ftp端口,默认为21
            $serverConfig['pasv'] = true;//是否开启被动模式,true开启,默认开启
            $serverConfig['ssl'] = false;//ssl连接,默认不开启
            $serverConfig['timeout'] = 60;//超时时间,默认60,单位 s
            $this->handle->start($serverConfig);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function upload($realPath, $newPath)
    {
        //上传所在完整路径
        $this->remoteName = $newPath;
        if (!file_exists($realPath)) {
            $result = 'Where are my papers?';
        }else{
            // 成功上传后 获取上传信息
            if ($this->handle->put($this->remoteName, $realPath)) {
                //上传文件成功!
                $result = true;
            } else {
                //上传失败
                $result = $this->handle->get_error();
            }
        }
        $this->handle->close();

        if ($result !== true){
            throw new Exception($result);
        }
        return true;
    }

    public function delete($fileName)
    {
        //上传所在完整路径
        $this->remoteName = str_replace('images/', '', $fileName);
        if ($this->handle->delete($this->remoteName)) {
            //删除文件成功!
            $result = true;
        } else {
            //上传失败
            $result = $this->handle->get_error();
        }
        $this->handle->close();
        if ($result !== true){
            throw new Exception($result);
        }
        return true;
    }

    public function deletes($fileList)
    {
        $errorData = [];
        foreach ($fileList as $key => $value) {
            $this->remoteName = str_replace('images/', '', $value);
            if (!$this->handle->delete($this->remoteName)) {
                //删除文件成功!
                $errorData[] = basename($value);
            }
        }
        if (count($errorData) == 0) {
            $result = true;
        } else {
            $result = count($errorData) . ' 个删除失败:'.implode(',',$errorData);
        }
        $this->handle->close();
        if ($result !== true){
            throw new Exception($result);
        }
        return true;
    }

}