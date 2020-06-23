<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $id
 * @property int $request_times
 * @property int $total_request_times
 * @property string $create_time
 * @property string $key api唯一标识
 * @property string $name 接口别名
 * @property string $update_time
 * @mixin think\Model
 */
class ApiRequest extends Model
{
    protected $autoWriteTimestamp = false;
}
