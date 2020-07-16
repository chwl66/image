<?php
/**
 * FILE_NAME: Github.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年3月18日20:29:31.
 */

namespace storage\driver;

use Exception;
use storage\Storage;

class Github implements Storage
{
    protected $options;
    protected $handle;

    public function __construct($options)
    {
        $this->options = $options;
        $this->handle = new \lib\Github($options['owner'], $options['repository'], $options['token']); //实例化对象
    }

    public function upload($realPath, $newPath)
    {
        // 成功上传后 获取上传信息
        $this->handle->upload($realPath, $newPath);

        return true;
    }

    public function delete($fileName)
    {
        //上传所在完整路径
        $this->handle->delete($fileName);

        return true;
    }

    public function deletes($fileList)
    {
        $errorData = [];
        foreach ($fileList as $key => $value) {
            try {
                $this->handle->delete(str_replace('images/', '', $value));
            } catch (\Exception $e) {
                //删除文件失败!
                $errorData[] = basename($value);
            }
        }
        if (count($errorData) !== 0) {
            throw new Exception(count($errorData).' 个删除失败:'.implode(',', $errorData));
        }

        return true;
    }
}
