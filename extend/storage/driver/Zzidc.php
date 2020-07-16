<?php
/**
 * FILE_NAME: Zzidc.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019年11月25日19:37:23
 */

namespace storage\driver;

use Exception;
use lib\Kuaiyun;
use storage\Storage;

class Zzidc implements Storage
{
    /**
     * Upyun 上传
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
        $this->handle = new Kuaiyun([
            'accessKey' => $this->options['accessKey'],
            'secretKey' => $this->options['secretKey'],
            'resource' => $this->options['resource'],
            'voucher' => $this->options['voucher'],
            'bucketName' => $this->options['bucketName'],
        ]);
    }

    public function upload($realPath, $newPath)
    {
        //上传所在完整路径
        $response = $this->handle->upload(array(
            'filePath' => $realPath,
            'remoteName' => $newPath,
        ));
        if ($response == 'success') {
            return true;
        } else {
            $result = '上传失败 ' . $response;
        }
        throw new Exception($result);
    }

    public function delete($fileName)
    {
        $this->handle->delete($fileName);
        return true;
    }

    public function deletes($fileList)
    {
        $errorData = [];
        foreach ($fileList as $key => $value) {
            $this->remoteName = str_replace('images/', '', $value);
            /*删除文件*/
            $response = $this->handle->delete($this->remoteName);
            if ($response != 'success') {
                $errorData[] = '[' . basename($value) . ']错误信息：' . $response;
            }
        }
        if (count($errorData) !== 0) {
            throw new Exception(count($errorData) . ' 个删除失败:' . implode(',', $errorData));
        }
        return true;

    }

}