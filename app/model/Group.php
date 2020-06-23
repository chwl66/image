<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $capacity 字节默认1GB
 * @property int $frequency 1小时内上传图片数
 * @property int $id
 * @property int $picture_process 图片处理
 * @property int $price 用户升级用户组消耗余额
 * @property string $name
 * @property string $storage 储存策略
 * @mixin think\Model
 */
class Group extends Model
{
    protected $autoWriteTimestamp = false;
}
