<?php
/**
 * FILE_NAME: This.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2019年10月30日15:13:35
 */

namespace storage\driver;

use Exception;
use League\Flysystem\FileNotFoundException;
use storage\Storage;
use think\facade\Filesystem;

class This implements Storage
{
    /**
     * 本地 上传
     */
    protected $options;
    protected $handle;

    public function __construct($option)
    {
    }

    public function upload($realPath, $newPath)
    {
        return true;
    }

    public function delete($fileName)
    {
        if (Filesystem::has($fileName)){
            Filesystem::delete($fileName);
        }
        return true;
    }

    public function deletes($fileList)
    {
        $errorData = [];
        foreach ($fileList as $key => $value) {
            $res = $this->delete($value);
            if ($res !== true) {
                $errorData[] = basename($value).$res;
            }
        }
        if (count($errorData) !== 0) {
            throw new Exception(count($errorData) . ' 个删除失败:',implode(',',$errorData));
        }
        return true;
    }

}