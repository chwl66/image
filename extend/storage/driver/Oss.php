<?php
/**
 * FILE_NAME: Oss.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019年10月30日15:13:35
 */

namespace storage\driver;

use storage\Storage;
use OSS\Core\OssException;
use OSS\OssClient;
class Oss implements Storage
{
    /**
     * Upyun 上传
     */
    protected $options;
    protected $handle;

    public function __construct($options){
        $this->options = $options;
        $this->handle = new OssClient(
            $this->options['AccessKeyId'],
            $this->options['AccessKeySecret'],
            $this->options['Endpoint'],
            false,
            null,
            null
        );
    }
    public function upload($realPath, $newPath)
    {
        //上传所在完整路径
        $this->handle->putObject($this->options['Bucket'], $newPath,file_get_contents($realPath));
        return true;
    }
    public function delete($fileName){

        $this->handle->deleteObject($this->options['Bucket'], $fileName);
        return true;
    }
    public function deletes($fileList){

        array_walk($fileList,function (&$value){
            $value = str_replace('images/','',$value);
        });
        $this->handle->deleteObjects($this->options['Bucket'], $fileList);

        return true;
    }

}