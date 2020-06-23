<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $group_id 分组id
 * @property int $id
 * @property string $mark 表单的备注信息
 * @property string $name 键名
 * @property string $type 表单类型input或者textare
 * @property string $value 值
 * @mixin think\Model
 */
class Set extends Model
{
    protected $autoWriteTimestamp = false;
    public function group(){
        return $this->belongsTo(SetGroup::class,'group_id','id');
    }

}
