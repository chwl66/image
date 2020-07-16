<?php
/**
 * FILE_NAME: Qiniu.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019年10月30日15:13:35
 */

namespace storage\driver;

use Exception;
use storage\Storage;

class Qiniu implements Storage
{
    /**
     * qiniu 上传
     */
    protected $options;
    protected $handle;
    protected $remoteName;
    protected $uploadToken;
    protected $UploadManager;
    protected $BucketManager;

    public function __construct($options){
        $this->options = $options;

        $auth = new \Qiniu\Auth($this->options['AccessKey'], $this->options['SecretKey']);
        $this->uploadToken = $auth->uploadToken($this->options['Bucket']);
        $config = new \Qiniu\Config();
        $this->UploadManager = new \Qiniu\Storage\UploadManager();
        $this->BucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
    }
    public function upload($realPath, $newPath)
    {
        $this->remoteName = $newPath;
        list($ret, $error) = $this->UploadManager->putFile($this->uploadToken, $this->remoteName, $realPath);
        if (null !== $error) {
            throw new Exception('上传失败 ' . $error);
        }
        //上传文件成功!
        return true;
    }
    public function delete($fileName){
        $this->remoteName = $fileName;
        $err = $this->BucketManager->delete($this->options['Bucket'], $this->remoteName);
        if ($err) {
            $this->error = $err;
            return $err;
        }
        return true;
    }
    public function deletes($fileList){
        array_walk($fileList,function (&$value){
            $value = str_replace('images/','',$value);
        });
        $ops = $this->BucketManager->buildBatchDelete($this->options['Bucket'], $fileList);
        list($ret, $err) = $this->BucketManager->batch($ops);
        if ($err) {
            throw new Exception('上传失败 ' . $err);
        }
        return true;

    }

}