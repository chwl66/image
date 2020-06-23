<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $denomination 面额
 * @property int $id
 * @property int $use_time 使用时间
 * @property int $used_time 使用时间
 * @property int $user_id 使用者ID
 * @property string $create_time 生成时间
 * @property string $key
 * @property-read \app\model\User $user
 * @mixin think\Model
 */
class RechargeCard extends Model
{
    protected $autoWriteTimestamp = false;
    public function user()
    {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
