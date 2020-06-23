<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property float $fraction 是否违规
 * @property int $file_size
 * @property int $final_request_time 最后请求时间
 * @property int $folder 目录
 * @property int $folder_id 目录
 * @property int $id
 * @property int $today_request 今日请求数
 * @property int $today_request_times
 * @property int $total_request_times 今日请求数
 * @property int $user_id
 * @property int $yesterday_request 昨日请求数
 * @property string $create_time 创建时间
 * @property string $filename
 * @property string $image_type
 * @property string $ip
 * @property string $md5
 * @property string $mime
 * @property string $pathname 目录地址
 * @property string $sha1
 * @property string $signatures
 * @property string $storage_key
 * @property string $update_time 更新时间
 * @property string $url
 * @property-read \app\model\Storage $storage
 * @property-read \app\model\User $user
 * @mixin think\Model
 */
class Image extends Model
{
    protected $autoWriteTimestamp = false;

    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
    // 设置json类型字段
    protected $json = ['url'];
    public function storage()
    {

        return $this->belongsTo(Storage::class, 'storage_key', 'name');
    }
    public function user()
    {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
