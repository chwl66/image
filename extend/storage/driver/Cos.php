<?php
/**
 * FILE_NAME: Cos.php
 * From: www.hidove.cn
 * Author: Ivey
 * Date: 2020年5月22日17:25:41
 */

namespace storage\driver;

use Qcloud\Cos\Client;
use storage\Storage;

class Cos implements Storage
{
    /**
     * Upyun 上传
     */
    private $options;
    private $handle;

    private $remoteName;
    public function __construct($options){
        $this->options = $options;

        $this->handle = new Client([
            'region' => $this->options['Region'],
            'credentials' => [
                'secretId' => $this->options['SecretId'],
                'secretKey' => $this->options['SecretKey'],
            ],
        ]);
    }

    public function upload($realPath, $newPath)
    {
        //上传所在完整路径
        $this->remoteName = $newPath;
        $file = file_get_contents($realPath);
        $this->handle->putObject([
            'Bucket' => $this->options['Bucket'],
            'Key' =>  $this->remoteName,
            'Body' => $file,
        ]);
        return true;
    }
    public function delete($realPath){
        $this->remoteName = str_replace('images/','',$realPath);
        $this->handle->deleteObject([
            'Bucket' => $this->options['Bucket'],
            'Key' => $this->remoteName,
        ]);
        return true;
    }
    public function deletes($fileList){
        $objects = [];
        foreach ($fileList as $value) {
            $this->remoteName = str_replace('images/','',$value);
            $objects[] = ['Key' => $this->remoteName ];
        }

        $this->handle->deleteObjects([
            'Bucket' => $this->options['Bucket'],
            'Objects' => $objects,
        ]);
        return true;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}