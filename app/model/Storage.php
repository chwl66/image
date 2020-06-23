<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $cdn
 * @property string $create_time
 * @property string $data
 * @property string $driver
 * @property string $name
 * @property string $type
 * @property string $update_time
 * @mixin think\Model
 */
class Storage extends Model
{
    //
    // 设置json类型字段
    protected $json = ['data'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
    protected $autoWriteTimestamp = false;
}
