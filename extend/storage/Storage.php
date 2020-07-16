<?php

namespace storage;


interface Storage
{

    /**
     * 传入配置参数
     * Storage constructor.
     */
    public function __construct($option);

    /**
     * 上传文件
     * @param $realPath
     * @return mixed 返回数据
     * 成功格式：true
     * 失败格式：$e->getMessage()
     */
    public function upload($realPath,$newPath);

    /**
     * 单个删除
     * @param $realPath
     * @return mixed
     */
    public function delete($realPath);

    /**
     * 多文件删除
     * @param $fileList
     * @return mixed
     */
    public function deletes($fileList);
}