<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: <2019年11月25日19:36:53>
// +----------------------------------------------------------------------
namespace lib;


use lib\provider\KuaiyunMethod;

class Kuaiyun
{
    private $option;
    private $token;
    private $handle;
    public function __construct($option)
    {
        $this->option = $option;
        $this->handle = new KuaiyunMethod();
        $this->token = $this->handle->get_token($this->option['voucher'],$this->option['accessKey'],$this->option['secretKey'],$this->option['resource']);


    }
    public function upload($parram){
        /*上传文件*/
        $response = $this->handle->send_file($parram['filePath'],$parram['remoteName'],$this->token,$this->option['bucketName'],$this->option['resource']);
        return $response;
    }
    public function delete($filePath){
        /*删除文件*/
        $response = $this->handle->del_file($this->token,$filePath,$this->option['bucketName'],$this->option['resource']);
        return $response;
    }

}
