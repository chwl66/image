<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $final_request_time 最后一次请求时间
 * @property int $id
 * @property int $request_times 请求次数
 * @property int $today_request_times 今日请求次数
 * @property int $total_request_times 请求次数
 * @property string $create_time 第一次请求时间
 * @property string $ip 请求的ip地址
 * @property string $referer 来源地址
 * @property string $update_time 最后一次请求时间
 * @mixin think\Model
 */
class ImageRequest extends Model
{
    protected $autoWriteTimestamp = false;
}
