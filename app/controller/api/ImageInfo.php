<?php


namespace app\controller\api;


use app\middleware\TokenAuth;
use app\model\Image;
use think\facade\Request;

class ImageInfo
{

    protected $middleware = [
        TokenAuth::class
    ];
    /**
     * 根据signatures查询图片信息
     */
    public function get()
    {

        $signatures = Request::param('signatures');

        if (empty($signatures)) {
            return msg(400, 'The Signatures can not be null!');
        }
        $imageinfo = Image::where([
            'signatures' => $signatures
        ])->field([
            'signatures',
            'url',
            'pathname',
            'user_id',
            'folder_id',
            'filename',
            'fraction',
            'image_type',
            'mime',
            'file_size',
            'sha1',
            'md5',
            'create_time',
            'update_time',
            'ip',
            'today_request_times',
            'total_request_times',
            'final_request_time',
        ])
            ->findOrEmpty();
        if (!$imageinfo->isExists()) {
            return msg(400, 'This image is not found');
        }
        return msg(200, 'success', $imageinfo);
    }
}