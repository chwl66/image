<?php


namespace app\controller\api;


use app\controller\ajax\provider\ImagesProvider;
use app\middleware\TokenAuth;
use app\model\Image;
use app\Request;

class ImageUpdate
{
    protected $middleware = [
        TokenAuth::class
    ];
    public function __construct()
    {
    }

    /**
     * 更新图片的外链地址
     */
    public function update(Request $request)
    {

        $signatures = $request->param('signatures');


        if (empty($signatures)) {
            return msg(400, 'The Signatures can not be null!');
        }
        $publicCloud = $request->param('publicCloud');
        $privateStorage = $request->param('privateStorage');

        if (empty($privateStorage) && empty($publicCloud)) {
            return msg(400, 'The privateStorage and publicCloud can not be null at the same time!');
        }
        $publicCloud = format_api_type($publicCloud);
        $privateStorage = format_api_type($privateStorage);

        $model = Image::where([
            'signatures' => $signatures,
            'user_id' => $request->user->id,
        ])->findOrEmpty();
        if ($model->isEmpty()) {
            return msg(400, 'This picture was not found!');
        }

        $apiType = [
            'publicCloud' => $publicCloud,
            'privateStorage' => $privateStorage
        ];
        $updateInfo = (new ImagesProvider())->updateInfo($model, [], $apiType);
        if ($updateInfo === true) {
            return msg(200, 'success',$model->url);
        }
        return msg(400, $updateInfo);
    }
}