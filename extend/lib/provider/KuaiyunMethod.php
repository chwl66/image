<?php


namespace lib\provider;

class KuaiyunMethod
{

    /** 获取操作秘钥token函数 **/
    public function get_token($voucher,$accessKey,$secretKey,$resource){
        $url = "http://api.storagesdk.com/restful/storageapi/storage/getToken"; //获取操作秘钥token方法
        $query = array();
        $data = array();
        $data["voucher"]= $voucher;
        $data["accessKey"]= $accessKey ;
        $data["secretKey"]= $secretKey;
        $data["resource"]= $resource;
        $body = json_encode($data);
        $headers = array("Content-Type: application/json; charset=utf-8");
        $request = new KuaiyunUrlRequest($url, $headers, $query, "json", true, $body);
        $response = $request->exec();
        $msg = json_decode($response,true)["message"];
        $arr = explode(":",$msg);
        $token = $arr[1];
        return $token;
    }

    /** 上传文件函数 **/
    public function send_file($localFile,$fileName,$token,$bucketName,$resource){
        $url = "http://api.storagesdk.com/restful/storageapi/file/uploadFile"; //上传文件方法
        $query = array();
        $data = array();
        $data['input'] = file_get_contents($localFile);
        $file = base64_encode($fileName);
        $len = strlen(file_get_contents($localFile));
        $headers = array("Content-Type: application/json;charset=utf-8",
            "token:{$token}",
            "fileName:{$file}",
            "bucketName:{$bucketName}",
            "resource:{$resource}",
            "length:{$len}");
        $body = $data["input"];
        $request = new KuaiyunUrlRequest($url, $headers, $data, "json", true, $body);
        $response = $request->exec();
        $msg = json_decode($response,true)["message"];
        return $msg;
    }

    /** 获取文件的url函数 **/
    public function get_url($token,$fileName,$bucketName,$minutes,$leng,$resource){
        $url = "http://api.storagesdk.com/restful/storageapi/file/getFileUrl"; // 获取文件的url方法
        $query = array();
        $data = array();
        $data["token"]= $token;
        $data["fileName"]= $fileName;
        $data["bucketName"]= $bucketName;
        $data["minutes"]= $minutes;
        $data["leng"]= $leng;
        $data["resource"]="{$resource}";
        $body = json_encode($data);
        //print_r($body);
        $headers = array("Content-Type: application/json; charset=utf-8");
        $request = new KuaiyunUrlRequest($url, $headers, $query, "json", true, $body);
        $response = $request->exec();
        $url = json_decode($response,true)["message"];
        return $url;
        //print_r($response);
    }

    /** 删除文件函数**/
    public function del_file($token,$fileName,$bucketName,$resource){
        $url = "http://api.storagesdk.com/restful/storageapi/file/deleteFile"; // 删除文件方法
        $query = array();
        $data = array();
        $data["token"] = $token;
        $data["fileName"] = $fileName;
        $data["bucketName"] = $bucketName;
        $data["resource"] = $resource;
        $body = json_encode($data);
        $headers = array("Content-Type: application/json; charset=utf-8");
        $request = new KuaiyunUrlRequest($url, $headers, $query, "json", true, $body);
        $response = $request->exec();
        $result	= json_decode($response, true)["message"];
        return $result;

    }
}
