<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $delete_time 删除时间
 * @property int $id ID
 * @property int $parent_id 上级文件夹ID
 * @property int $user_id 用户ID
 * @property string $create_time 添加时间
 * @property string $name 文件夹名称
 * @property string $update_time 更新时间
 * @property-read \app\model\Folders $parent
 * @property-read \app\model\Folders $user
 * @property-read \app\model\Folders[] $children
 * @property-read \app\model\Image[] $images
 * @mixin think\Model
 */
class Folders extends Model
{
    protected $autoWriteTimestamp = false;
    public function parent()
    {
        return $this->belongsTo(Folders::class, 'parent_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(Folders::class, 'parent_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(Folders::class, 'user_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'folder_id', 'id');
    }

}
