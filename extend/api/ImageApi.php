<?php


namespace api;


interface imageApi
{
    /** 上传文件
     * @param $pathName String 传入文件相对路径
     * @return String 返回直链
     */
    public function upload($pathName);
}