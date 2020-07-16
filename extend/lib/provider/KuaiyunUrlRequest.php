<?php


namespace lib\provider;

class KuaiyunUrlRequest
{
    public $url;
    public $headers;
    public $params;
    public $body;
    public $expectedFormat;
    public $method;
    public $data;


    /**
    公共方法
     **/
    public function __construct($aUrl, array $aHeaders, array $aParams, $aFormat = "json", $isPost = false, $aBody = "+")
    {
        $this->url = $aUrl;
        $this->headers = $aHeaders;
        $this->params = $aParams;
        $this->expectedFormat = $aFormat;
        $this->method = ($isPost ? "POST" : "GET");
        $this->body = $aBody;

    }
    //
    public function exec()
    {

        $url = $this->url;

        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_HEADER, 1);
        curl_setopt($request, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        if($this->method == "POST")
        {
            curl_setopt($request, CURLOPT_POST, 1);
            curl_setopt($request, CURLOPT_POSTFIELDS, $this->body);
        }

        $response = curl_exec($request);
        $httpCode = curl_getinfo($request, CURLINFO_HTTP_CODE);
        if ($httpCode == '200') {
            $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            curl_close($request);
            return $body;
        }else{
            curl_close($request);
            return $response;
        }
    }
}