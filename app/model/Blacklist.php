<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $duration
 * @property int $fraction 图片评分
 * @property int $id
 * @property int $release_time
 * @property string $create_time
 * @property string $image
 * @property string $ip
 * @property string $reason
 * @property string $referer 来源域名
 * @property string $username
 * @mixin think\Model
 */
class Blacklist extends Model
{
    protected $autoWriteTimestamp = false;
    //
}
