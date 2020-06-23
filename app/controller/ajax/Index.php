<?php


namespace App\controller\ajax;


use app\BaseController;
use think\facade\Db;
use think\facade\Request;

class Index extends BaseController
{


    protected function initialize()
    {
    }

    //探索
    public function explore()
    {
        if (hidove_config_get('system.other.explore') != 1) {
            return msg(400, 'The Explore has been turned off.');
        }
        $page = Request::param('page');
        $limit = Request::param('limit');
        $page = empty($page) ? 1 : $page;
        $limit = empty($limit) ? 10 : $limit;

        $images = Db::name('image')
            ->alias('a')
            ->field('a.signatures,a.create_time')
            ->join('user b', 'b.id=a.user_id')
            ->where('b.is_private', 0)
            ->union(function ($query) {
                $query->field('signatures,create_time')->name('image')->where('user_id', 0);
            })
            ->page($page, $limit)
            ->order('create_time', 'desc')
            ->cache(true,240,'explore')
            ->select()->toArray();
        array_walk($images, function (&$value) {
            $value['url'] = splice_distribute_url($value['signatures']);
        });
        return msg(200, 'success', $images);
    }

    public function uploadOption()
    {
        $imageConfig = hidove_config_get('system.upload.');
        $imageConfig['imageType'] = explode(',', $imageConfig['imageType']);
        $apiInfo = \app\model\Api::order('id', 'asc')->where('is_ok', 1)->select();
        $data = [
            'imageType' => $imageConfig['imageType'],
            'maxImageSize' => (int)$imageConfig['maxImageSize'],
            'maxFileCount' => (int)$imageConfig['maxFileCount'],
            'apiType' => $apiInfo,
        ];
        return msg(200, 'success', $data);
    }


    //图片预览，解决跨域问题
    public function imagePreview()
    {
        $url = Request::param('url');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return msg(400, 'URL格式错误');
        }
        $data = hidove_get($url);
        if (empty($data)) {
            return msg(400, '图片获取错误');
        }
        return response()->data($data)->header([
            'Content-type' => 'image/png'
        ]);
    }
}