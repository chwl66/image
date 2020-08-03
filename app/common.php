<?php

// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月24日18:07:45
// +----------------------------------------------------------------------

use app\model\Set;
use app\model\User;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;
use think\facade\Session;
use think\helper\Str;

function msg($code = 200, $msg = 'success', $data = [])
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ]);
}

function json_list($code, $msg = 'success', $data = [], $page, $limit, $count)
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
        'page' => $page,
        'limit' => $limit,
        'count' => $count,
    ]);
}

function json_table($code = 200, $msg = 'success',
                    $data = [],
                    $page,
                    $limit,
                    $count
)
{
    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
        'page' => $page,
        'limit' => $limit,
        'count' => $count,
    ]);
}

function make_token()
{
    return md5(time() . 'HidoveImage' . uniqid() . mt_rand(0, 9999));
}

function encrypt_password($password)
{
    return md5($password . 'HidoveImage');
}

function format_date($timestamp)
{
    return date('Y-m-d H:i:s', $timestamp);
}

function hidove_get($url, $headers = [], $referer = null)
{

    $headers = array_merge([
        'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
        'x-forwarded-for:' . rand_ip(),
    ], $headers);
    // 创建一个新 cURL 资源
    $curl = curl_init();
    // 设置URL和相应的选项
    // 需要获取的 URL 地址
    curl_setopt($curl, CURLOPT_URL, $url);
    #启用时会将头文件的信息作为数据流输出。
    curl_setopt($curl, CURLOPT_HEADER, false);
    #在尝试连接时等待的秒数。设置为 0，则无限等待。
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    #允许 cURL 函数执行的最长秒数。
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    #关闭ssl
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    #TRUE 将 curl_exec获取的信息以字符串返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    #设置header
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // 跟踪重定向
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    #伪造来源网址REFERER
    curl_setopt($curl, CURLOPT_REFERER, empty($referer) ? $url : $referer);
    // 抓取 URL 并把它传递给浏览器
    $res = curl_exec($curl);
    // 关闭 cURL 资源，并且释放系统资源
    if ($res === false) {
        return "CURL Error:" . curl_error($curl);
    }
    curl_close($curl);
    return $res;
}


function hidove_post(
    $url,
    $post,
    $referer = 'https://www.baidu.com',
    $headers = []
)
{
    // 初始化
    $headers = array_merge([
        'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
        'x-forwarded-for:' . rand_ip(),

    ], $headers);
    // 创建一个新 cURL 资源
    $curl = curl_init();
    // 设置URL和相应的选项
    // 需要获取的 URL 地址
    curl_setopt($curl, CURLOPT_URL, $url);
    #启用时会将头文件的信息作为数据流输出。
    curl_setopt($curl, CURLOPT_HEADER, false);
    #设置头部信息
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    #在尝试连接时等待的秒数。设置为 0，则无限等待。
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    #允许 cURL 函数执行的最长秒数。
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    #设置请求信息
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    #设置referer
    curl_setopt($curl, CURLOPT_REFERER, $referer);
    #关闭ssl
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    #TRUE 将 curl_exec获取的信息以字符串返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // 抓取 URL 并把它传递给浏览器
    $return = curl_exec($curl);
    if ($return === false) {
        return "CURL Error:" . curl_error($curl);
    }
    curl_close($curl);
    return $return;
}


function hidove_curl($url, $put, $headers = [],
                     $type = 'PUT'
)
{

    $headers = array_merge([
        'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
        'x-forwarded-for:' . rand_ip(),
    ], $headers);
    // 创建一个新 cURL 资源
    $curl = curl_init();
    // 设置URL和相应的选项
    // 需要获取的 URL 地址
    curl_setopt($curl, CURLOPT_URL, $url);
    #启用时会将头文件的信息作为数据流输出。
    curl_setopt($curl, CURLOPT_HEADER, false);
    #设置头部信息
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    #在尝试连接时等待的秒数。设置为 0，则无限等待。
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    #允许 cURL 函数执行的最长秒数。
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    #设置请求信息
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $put); //定义提交的数据
    #关闭ssl
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    #TRUE 将 curl_exec获取的信息以字符串返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // 抓取 URL 并把它传递给浏览器
    $return = curl_exec($curl);
    if ($return === false) {
        return "CURL Error:" . curl_error($curl);
    }
    curl_close($curl);
    return $return;
}

function hidove_config_get($name)
{
    $data = Cache::get('config_' . $name);
    if ($data) {
        return $data;
    }
    if (Str::endsWith($name, '.')) {

        $model = Set::where('name', 'like', "%$name%")
            ->select();
        $toArray = $model->toArray();
        $data = [];
        foreach ($toArray as $value) {
            $data[Str::substr($value['name'], Str::length($name), Str::length($value['name']))]
                = $value['value'];
        }
    } else {
        $data = Set::where('name', $name)
            ->value('value');
    }
    Cache::tag('config')->set('config_' . $name, $data);
    return $data;
}

function hidove_config_to_array($data)
{
    $config = [];
    $recursion = function ($explode, $config, $value) use (&$recursion) {
        if (count($explode) > 0) {
            $array_pop = array_shift($explode);
            if (empty($config[$array_pop])) {
                $config[$array_pop] = [];
            }
            if (count($explode) > 0) {
                $config[$array_pop] = $recursion($explode, $config[$array_pop], $value);
            } else {
                $config[$array_pop] = $value;
            }
        }
        return $config;
    };
    foreach ($data as $key => $value) {
        $explode = explode('.', $key);
        $config = array_merge($config, $recursion($explode, $config, $value));
    }
    return $config;
}

function hidove_config_json_to_array($data)
{
    foreach ($data as $key => $value) {
        $data[$key] = json_decode($value, true);
    }
    return $data;
}

function get_image_info($fileName)
{
    $data = getimagesize($fileName);
    switch ($data[2]) {
        case 1:
            return [
                'type' => 'gif',
                'mime' => $data['mime']
            ];
        case 2:
            return [
                'type' => 'jpg',
                'mime' => $data['mime']
            ];
        case 3:
            return [
                'type' => 'png',
                'mime' => $data['mime']
            ];
        case 6:
            return [
                'type' => 'bmp',
                'mime' => $data['mime']
            ];
        case 17:
            return [
                'type' => 'ico',
                'mime' => $data['mime']
            ];
        default:
            return [
                'type' => 'jpg',
                'mime' => $data['mime']
            ];
    }
}

/**
 * 下划线转驼峰
 * 思路:
 * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
 * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
 */
function camelize($uncamelized_words, $separator = '_')
{
    $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
}

/**
 * 驼峰命名转下划线命名
 * 思路:
 * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 */
function un_camelize($camelCaps, $separator = '_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}


function hidove_md5($str)
{
    return md5($str . 'Hidove');
}

function get_size($filesize)
{
    if ($filesize >= 1073741824) {
        $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
    } elseif ($filesize >= 1048576) {
        $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
    } elseif ($filesize >= 1024) {
        $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
    } else {
        $filesize = $filesize . ' 字节';
    }
    return $filesize;
}

function format_bytes($size, $r = 2)
{
    $units = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, $r) . ' ' . $units[$i];
}

//以下是取中间文本的函数
function get_mid_str($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    //echo '左边:'.$left;
    $right = strpos($str, $rightStr, $left);
    //echo '<br>右边:'.$right;
    if ($left < 0 or $right < $left) return '';
    return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}

//以下是取右边文本的函数
function get_right_str($str, $leftStr)
{
    $left = strpos($str, $leftStr);
    return substr($str, $left + strlen($leftStr));
}

//以下是取左边边文本的函数
function get_left_str($str, $rightStr)
{
    $right = strpos($str, $rightStr);
    return substr($str, 0, $right);
}

/**图片转base64
 * @param $filePath string 图片地址
 * @return string
 */
/**
 * 图片转base64
 * @param $filePath
 * @param bool $type 为真是返回纯base64数据
 * @return string
 */
function img2base64($filePath, $type = false)
{
    $image_info = getimagesize($filePath);
    $image_data = fread(fopen($filePath, 'r'), filesize($filePath));
    $base64_encode = base64_encode($image_data);
    if ($type === true) {
        return $base64_encode;
    }
    return 'data:' . $image_info['mime'] . ';base64,' . $base64_encode;
}

//随机IP
function rand_ip()
{
    $ip2id = round(rand(600000, 2550000) / 10000); //第一种方法，直接生成
    $ip3id = round(rand(600000, 2550000) / 10000);
    $ip4id = round(rand(600000, 2550000) / 10000);
    //下面是第二种方法，在以下数据中随机抽取
    $arr_1 = array("218", "218", "66", "66", "218", "218", "60", "60", "202", "204", "66", "66", "66", "59", "61", "60", "222", "221", "66", "59", "60", "60", "66", "218", "218", "62", "63", "64", "66", "66", "122", "211");
    $randarr = mt_rand(0, count($arr_1) - 1);
    $ip1id = $arr_1[$randarr];
    return $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
}


function get_web_upload_key()
{
    $key = Cache::get('web_upload_key');
    if (empty($key)) {
        $key = make_token();
        Cache::set('web_upload_key', $key, mt_rand(1200, 3600 * 3));
    }
    return $key;
}

/**
 * 是否为前台上传
 * @return bool
 */
function is_web_upload()
{
    $str = get_param('is_web_upload');
    $staticchars = 'elr5vCgGnQ9pMqJxVcwdoh2KDPjAHbafRtSU61iZkTON0s84YXImuy7zELBF3W';
    $decodechars = "";
    for ($i = 1; $i < strlen($str);) {
        $num0 = strpos($staticchars, $str[$i]);
        if ($num0 !== false) {
            $num1 = ($num0 + 59) % 62;
            $code = $staticchars[$num1];
        } else {
            $code = $str[$i];
        }
        $decodechars .= $code;
        $i += 3;
    }

    return $decodechars === get_web_upload_key() && Request::isAjax();
}

/**
 * 获取当前版本
 * @return array|mixed|null
 */
function get_current_version()
{
    return Config::get('hidove.version');
}

function get_request_ip()
{
    $ip = Request::server('HTTP_CF_CONNECTING_IP');
    if ($ip) return $ip;
    return Request::ip();
}

function hidove_log($data)
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    if (empty($json)) $json = $data;
    Log::record($json, 'Hidove');
}

/**
 * 获取APITYPE
 * @return array|mixed|string|null
 */
function get_api_type()
{
    return get_param('apiType');
}

/**
 * 是否为合法URL
 * @param null $url
 * @return bool
 */
function is_valid_url($url = null)
{
    if (!is_string($url)) return false;
    $filter_var = boolval(filter_var($url, FILTER_VALIDATE_URL));
    if ($filter_var) return $filter_var;
    $parse_url = parse_url($url);
    $path = array_pop($parse_url);

    $url = str_ireplace($path, '/' . urlencode($path), $url);
    return boolval(filter_var($url, FILTER_VALIDATE_URL));
}

/**
 * 获取token
 * @return array|mixed|string|null
 */
function get_token()
{
    $get_param = trim(get_param('token'));
    return $get_param;
}
function is_token(){
    $userId =  Session::get('userId');
    if (!$userId){
        if (get_token()){
            return true;
        }
    }
    return false;
}
function get_param($key)
{
    $param = Request::header($key);
    if ($param) return $param;

    $param = Request::header(mb_strtolower($key));
    if ($param) return $param;


    $param = Request::param($key);
    if ($param) return $param;

    return Request::param(mb_strtolower($key));
}

//拼接分发地址spliceDistributeUrl
function splice_distribute_url($signatures)
{
    $userId = User::get_user_id();

    $distribute = Cache::get(__FUNCTION__ . '_' . $userId);

    $suffix = hidove_config_get('system.distribute.suffix');

    if (!$distribute && !empty($userId)) {
        $model = User::where('id', $userId)
            ->findOrEmpty();
        @$distribute = $model->storage['this']['distribute'];
    }
    if (!is_valid_url($distribute)) {
        $distribute = hidove_config_get('system.distribute.distribute');
        if (!is_valid_url($distribute)) {
            $distribute = Request::domain();
        }
    }
    Cache::tag(['config_user', 'config_user_' . $userId])
        ->set(__FUNCTION__ . '_' . $userId, $distribute);
    Cache::tag('')
        ->append(__FUNCTION__ . '_' . $userId);

    $suffix = empty($suffix) ? '' : $suffix;
    return $distribute . '/image/' . $signatures . $suffix;
}

/**
 * 格式化为数组、去除两遍空格、去除空值、并且转小写
 * @param $apiType
 * @return array|false|string[]
 */
function format_api_type($apiType)
{
    if (!is_array($apiType)) {
        $apiType = explode(',', $apiType);
    }
    foreach ($apiType as $key => $value) {
        if (empty($value)) {
            unset($apiType[$key]);
            continue;
        }
        $apiType[$key] = strtolower(trim($value));
    }
    return $apiType;
}

/**
 * 数组完全合并
 * @param $arr
 * @param $arr2
 * @return mixed
 */
function array_merge_deep($arr, $arr2)
{
    foreach ($arr2 as $key => $value) {
        if (is_array($value) && isset($arr[$key])) {
            $arr[$key] = array_merge_deep($arr[$key], $value);
            continue;
        }
        $arr[$key] = $value;
    }
    return $arr;
}

/**
 * 获取模板路径
 * @return string
 */
function get_template_path()
{
    $template = hidove_config_get('system.base.template');
    if (empty(trim($template))) $template = 'default';
    return app()->getRootPath() . '/template/' . $template . '/';
}

/**
 * 提升数组下标
 * @param $array
 * @param string|array $key
 * @return array
 */
function rising_subscript($array, $key = 'this')
{
    if (is_array($key)) {
        $key = array_reverse($key);
        foreach ($key as $v){
            $array = rising_subscript($array, $v);
        }
        return $array;
    }
    if (isset($array[$key])) {
        $thisTmp[$key] = $array[$key];
        $array = array_merge($thisTmp, $array);
    }
    return $array;

}