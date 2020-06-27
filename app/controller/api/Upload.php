<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月7日22:16:32
// +----------------------------------------------------------------------

namespace app\controller\api;


use app\BaseController;
use app\controller\ajax\provider\ImagesProvider;
use app\controller\api\service\AuthCheck;
use app\controller\api\service\BlackListFilter;
use app\controller\api\service\Process;
use app\controller\api\service\RecordRequest;
use app\controller\api\service\UrlFilter;
use app\controller\api\service\UserProcess;
use app\controller\api\service\UserStorageCapacity;
use app\controller\api\service\VisionPorn;
use app\controller\api\service\WhiteListFilter;
use app\controller\common\ImageInitial;
use app\model\Image;
use think\exception\ValidateException;
use think\facade\Filesystem;
use think\facade\Log;
use think\facade\Request;
use think\facade\Session;
use think\File;
use think\file\UploadedFile;
use think\helper\Str;

class Upload extends BaseController
{

    private $user = [];

    private $config;
    private $uploadConfig = [];

    private $apiType;
    private $privateStorage;

    private $distributeDomain;

    private $filesystem;

    protected $middleware = [];

    private $param;

    protected function initialize()
    {
        $this->filesystem = Filesystem::disk('public');

        $this->config = hidove_config_to_array(hidove_config_get('system.'));

        $this->apiType = Request::param('apiType');
        $this->privateStorage = Request::param('privateStorage');

        $this->apiType = format_api_type($this->apiType);
        $this->privateStorage = format_api_type($this->privateStorage);

        $this->uploadConfig = $this->config['upload'];

        $this->distributeDomain = $this->config['distribute']['distribute'];
        if (!Str::endsWith($this->distributeDomain, '/')) {
            $this->distributeDomain = $this->distributeDomain . '/';
        }
        $this->param = Request::param();
    }

    public function upload()
    {

        if (empty($this->apiType) && empty($this->privateStorage)) {
            return msg(400, 'The apiType and the privateStorage can\'t be null at the same time!');
        }


        try {
            //用户检测 请求次数限制
            $this->user = (new AuthCheck($this->config['base']))->run();
            //黑名单检测
            (new BlackListFilter($this->user))->run();

        } catch (\Exception $e) {

            return msg(400, $e->getMessage());
        }
        $tempPath = app()->getRuntimePath() . 'file';
        $tempFilename = $tempPath . '/' . uniqid() . '.png';
        //指定用户组允许url上传
        if (!empty($this->param['url']) && in_array($this->user->group_id, [2])) {
            $imageTempData = hidove_get($this->param['url']);
            if (!file_exists($tempPath)) {
                mkdir($tempPath);
            }
            file_put_contents($tempFilename, $imageTempData);
            $urlArr = explode('/', $this->param['url']);
            $file = new UploadedFile($tempFilename, $urlArr[count($urlArr) - 1]);

        } else {
            // 获取表单上传文件
            $file = request()->file('image');
        }
        try {
            $this->validate(['image' => $file],
                [
                    'image' => [
                        'require',
                        'file',
//                        'image',
                        'fileSize:' . $this->uploadConfig['maxImageSize'],
                        'fileExt:' . $this->uploadConfig['imageType'],
                    ]
                ]);
        } catch (ValidateException $e) {
            return msg(400, $e->getMessage());
        }


        //图片查重 仅对游客生效
        if (Session::get('userId') && $this->uploadConfig['duplicates']['switch'] == 1) {
            $time = time();
            $model = Image::where([
                'md5' => $file->md5(),
                'sha1' => $file->sha1(),
                'user_id' => 0,
            ])->whereBetweenTime('create_time', $time - $this->uploadConfig['duplicates']['time'], $time)
                ->findOrEmpty();
            if (!$model->isEmpty()) {
                $imageUrl = (new ImageInitial($model))->main();
                $imageUrl = array_merge(['distribute' => splice_distribute_url($model->signatures)], $imageUrl);
                return msg(200, 'success', ['url' => $imageUrl]);
            }
            unset($model);
        }
        $signatures = uniqid();
        $fileName = $signatures . '.' . $file->extension();
        $pathName = $this->filesystem->putFileAs(date($this->uploadConfig['rule']), $file, $fileName);
        $realPath = $this->filesystem->path($pathName);

        //图片鉴定

        try {
            //鉴黄
            $fraction = (new VisionPorn($this->config['audit']))->run($pathName);
            //白名单过滤

            (new WhiteListFilter($this->config['audit'], $this->user))->run($fraction, $pathName, $realPath);
            //用户储存容量处理  上传到指定目录
            (new UserStorageCapacity($this->user))->run();
            //记录请求信息
            if ($this->config['other']['apiRecord'] == 1) {
                RecordRequest::run();
            }
        } catch (\Exception $e) {
            $this->deleteFile($file, $pathName, $tempFilename);
            return msg(400, $e->getMessage());
        }

        if (
            !in_array($file->getMime(), ['image/x-icon', 'image/gif', 'image/vnd.microsoft.icon'])
            && $this->user->group->picture_process === 1
        ) {
            try {
                //图片处理 压缩 + 水印

                (new Process($this->config['imageEdit'], $realPath))
                    ->run();

                //用户水印
                (new UserProcess($this->user['watermark'], $realPath))
                    ->run();

            } catch (\Exception $e) {
                Log::record('[' . $signatures . ']' . $e->getMessage(), 'Hidove');
            }
        }


        $data = [
            'signatures' => $signatures,
            'storage_key' => $this->user['group']['storage'],
            'url' => [],
            'pathname' => $pathName,
            'user_id' => $this->user['id'],
            'folder_id' => $this->user['api_folder_id'],
            'filename' => $file->getOriginalName(),
            'fraction' => $fraction,//鉴黄分数
            'image_type' => $file->extension(),
            'md5' => $file->md5(),
            'sha1' => $file->sha1(),
            'mime' => $file->getMime(),
            'file_size' => $file->getSize(),
            'create_time' => time(),
            'update_time' => time(),
            'ip' => Request::ip(),
            'yesterday_request' => 0,
            'final_request_time' => time(),
        ];
        $model = new Image($data);
        $model->setAttr('user', $this->user);


        $imageUrl = (new ImagesProvider())->updateImageUrl($model, [
            'publicCloud' => $this->apiType,
            'privateStorage' => $this->privateStorage
        ]);

        //图片Url过滤

        $urlFilter = new UrlFilter($imageUrl, $pathName);
        $imageUrlForDatabase = $urlFilter->run();
        $imageUrlForNoPrivate = $imageUrlForDatabase;
        unset($imageUrlForNoPrivate['private']);
        if (empty($imageUrlForDatabase['private']) && empty($imageUrlForNoPrivate)) {
            $this->deleteFile($file, $pathName, $tempFilename);
            Log::record(json_encode($imageUrl, JSON_UNESCAPED_UNICODE), 'Hidove');
            return msg(400, '上传失败:' . $urlFilter->toString($urlFilter->getError()));
        }
        $imageUrl = array_merge(['distribute' => splice_distribute_url($signatures)], $imageUrl);

        $model->url = $imageUrlForDatabase;
        $model->save();

        //更新用户图片容量

        if ($this->user->id !== 0) {
            $this->user->capacity_used += $file->getSize();
            $this->user->save();
        }
        //删除临时文件
        if (isset($tempFilename) && file_exists($tempFilename))
            unlink($tempFilename);

        //限制返回接口类型
        $returnUrlType = format_api_type(hidove_config_get('system.upload.returnUrlType'));
        if (!empty($returnUrlType)) {
            $imageUrl = array_filter($imageUrl, function ($key) use ($returnUrlType) {
                if (in_array($key, $returnUrlType))
                    return true;
                return false;
            }, ARRAY_FILTER_USE_KEY);
        }
        return msg(200, 'success', ['url' => $imageUrl]);
    }

    private function deleteFile(&$file, $pathName, $tempFilename = '')
    {
        unset($file);
        if ($this->filesystem->has($pathName))
            $this->filesystem->delete($pathName);
        //删除临时文件
        if (isset($tempFilename) && file_exists($tempFilename))
            unlink($tempFilename);
    }
}
