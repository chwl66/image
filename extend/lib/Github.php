<?php

namespace lib;

use Exception;

class Github
{
    private $config;

    public function __construct($owner, $repository, $token)
    {
        $this->config['owner'] = $owner;
        $this->config['repository'] = $repository;
        $this->config['token'] = $token;
    }

    public function upload($realPath, $newPath)
    {
        $imageInfo = get_image_info($realPath);
        $imageInfo['path'] = $newPath;
        $UploadUrl = 'https://api.github.com/repos/'.$this->config['owner'].'/'.$this->config['repository'].'/contents/'.$imageInfo['path'];
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
            'Authorization: Bearer '.$this->config['token'],
        ];
        $data = [
            'message' => date('Y-m-d H:i:s').' 上传',
            'content' => base64_encode(file_get_contents($realPath)),
        ];
        $result = hidove_curl($UploadUrl, json_encode($data), $headers, 'PUT');
        $result = json_decode($result);
        if (!empty($result->content)) {
            return true;
        }
        if (!empty($result->message)) {
            throw new Exception($result->message);
        }
        throw new Exception('API 可能抽风了');
    }

    public function delete($filePath)
    {
//        DELETE /repos/:owner/:repo/contents/:path
        $imageInfo['path'] = $filePath;
        $UploadUrl = 'https://api.github.com/repos/'.$this->config['owner'].'/'.$this->config['repository'].'/contents/'.$imageInfo['path'];
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
            'Authorization: Bearer '.$this->config['token'],
        ];
        $imageInfo = hidove_get($UploadUrl);
        $imageInfo = json_decode($imageInfo);
        if (empty($imageInfo)) {
            throw new Exception('API 可能抽风了');
        }
        if (!empty($imageInfo->message) && $imageInfo->message == 'Not Found') {
            return true;
        }
        if (empty($imageInfo->sha)) {
            throw new Exception('获取sha失败');
        }
        $data = [
            'message' => date('Y-m-d H:i:s').' 删除',
            'sha' => $imageInfo->sha,
        ];
        $result = hidove_curl($UploadUrl, json_encode($data), $headers, 'DELETE');
        $result = json_decode($result);
        if (!empty($result->commit)) {
            return true;
        }
        if (!empty($result->message)) {
            throw new Exception($result->message);
        }
        throw new Exception('API 可能抽风了');
    }
}
