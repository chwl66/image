<?php
declare (strict_types=1);

namespace app\model;

use think\facade\Request;
use think\facade\Session;
use think\Model;

/**
 * @property int $api_folder_id token接口上传的文件夹id
 * @property int $capacity_used 已用容量
 * @property int $expiration_date Expiration date用户组过期时间
 * @property int $finance 用户余额
 * @property int $group_id 权限级别
 * @property int $id
 * @property int $is_private 是否在探索显示
 * @property int $is_whitelist 是否白名单
 * @property int $reset_time
 * @property string $create_time
 * @property string $email
 * @property string $forbidden_node 禁用节点
 * @property string $ip
 * @property string $mail
 * @property string $password
 * @property string $reset_key
 * @property string $storage
 * @property string $token
 * @property string $username
 * @property string $watermark
 * @property-read \app\model\Folders $api_folder
 * @property-read \app\model\Folders[] $folders
 * @property-read \app\model\Group $group
 * @property-read \app\model\Image[] $images
 * @mixin think\Model
 */
class User extends Model
{
    // 开启自动写入时间戳字段

    protected $autoWriteTimestamp = false;

    // 设置json类型字段
    protected $json = ['watermark', 'forbidden_node', 'storage'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    protected $field = [
        'username',
        'password',
        'email',
        'token',
        'create_time',
        'capacity_used',
        'api_folder_id',
        'group_id',
        'reset_key',
        'reset_time',
        'ip',
        'is_private',
        'forbidden_node',
        'is_whitelist',
        'finance',
        'expiration_date',
        'watermark',
        'storage'
    ];
    protected $hidden = [
        'password',
        'reset_key',
        'reset_time',
    ];


    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function apiFolder()
    {
        return $this->hasOne(Folders::class, 'id', 'api_folder_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'user_id', 'id');
    }

    public function folders()
    {
        return $this->hasMany(Folders::class, 'user_id', 'id');
    }

    public static function is_login($model = null)
    {
        if ($model instanceof User && !empty($user) && $model->isExists()) {
            return true;
        }
        if (Session::get('userId')) {
            return true;
        }
        $model = self::where([
            'token' => get_token()
        ])->findOrEmpty();
        if ($model->isExists()) {
            return true;
        }
        return false;
    }

    public static function get_user()
    {

        $userId = Session::get('userId');
        if ($userId) {
            $where = [
                'id' => $userId
            ];
        } else {
            $where = [
                'token' => get_token()
            ];
        }
        $model = self::where($where)->findOrEmpty();
        if ($model->isExists()) {
            return $model;
        }
        return null;
    }

    public static function get_user_id()
    {

        $userId = Session::get('userId');
        if ($userId) {
            return $userId;
        }

        $model = self::where([
            'token' => get_token()
        ])->findOrEmpty();
        if ($model->isExists()) {
            return $model->id;
        }
        return null;
    }

    /**
     * 是否为管理员
     * @param User|null $user
     * @return bool
     */
    public static function is_admin(User $user = null)
    {

        if (!$user instanceof User) {
            $user = self::get_user();
        }
        if (!empty($user) && $user->isExists() && $user->group_id === 2) {
            return true;
        }
        return false;
    }
}
