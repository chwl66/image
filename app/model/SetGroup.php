<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * Class app\model\SetGroup
 *
 * @property int $id
 * @property string $mark 备注名，显示在前台
 * @property string $name 组名，英文、数字
 */
class SetGroup extends Model
{
    protected $autoWriteTimestamp = false;
}
