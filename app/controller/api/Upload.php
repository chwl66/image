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
use app\model\User;
use app\validate\imageValidate;
use think\Exception;
use think\facade\Filesystem;
use think\facade\Log;
use think\facade\Request;
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
    private $signatures;
    private $time;
    /**
     * @var array
     */
    private $imageUrl;
    /**
     * @var array|\think\Model
     */
    private $model;
    private $pathName;
    /**
     * @var UploadedFile
     */
    private $file;

    protected function initialize()
    {
        $this->filesystem = Filesystem::disk('public');

        $this->config = hidove_config_to_array(hidove_config_get('system.'));

        $this->apiType = get_api_type();
        $this->privateStorage = get_param('privateStorage');

        $this->apiType = format_api_type($this->apiType);
        $this->privateStorage = format_api_type($this->privateStorage);

        $this->uploadConfig = $this->config['upload'];

        $this->distributeDomain = $this->config['distribute']['distribute'];
        if (!Str::endsWith($this->distributeDomain, '/')) {
            $this->distributeDomain = $this->distributeDomain . '/';
        }
        $this->param = Request::param();
        $this->time = time();
        $this->signatures = uniqid();

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
        $this->tempFilename = $tempPath . '/' . uniqid() . '.png';
        //指定用户组允许url上传
        if (!empty($this->param['url']) && User::is_admin($this->user)) {
            $imageTempData = hidove_get($this->param['url']);
            if (!file_exists($tempPath)) {
                mkdir($tempPath);
            }
            file_put_contents($this->tempFilename, $imageTempData);
            $urlArr = explode('/', $this->param['url']);
            $this->file = new UploadedFile($this->tempFilename, $urlArr[count($urlArr) - 1]);

        } else {
            // 获取表单上传文件
            $this->file = request()->file('image');
        }

        $validate = new imageValidate();

        if (!$validate->check(['image' => $this->file])) {
            return msg(400, $validate->getError());
        }

        //图片查重 仅对游客生效
        if (!User::is_login() && $this->uploadConfig['duplicates']['switch'] == 1) {
            $this->model = Image::where([
                'md5' => $this->file->md5(),
                'sha1' => $this->file->sha1(),
                'user_id' => 0,
            ])->whereBetweenTime('create_time', $this->time - $this->uploadConfig['duplicates']['time'], $this->time)
                ->findOrEmpty();
            if ($this->model->isExists()) {
                $imageUrl = (new ImageInitial($this->model))->main();
                $imageUrl = array_merge(['distribute' => splice_distribute_url($this->model->signatures)], $imageUrl);
                return msg(200, 'success', ['url' => $imageUrl]);
            }
            unset($this->model);
        }
        // 用户图片查重
        if (User::is_login()) {
            $this->model = Image::where([
                'md5' => $this->file->md5(),
                'sha1' => $this->file->sha1(),
                'user_id' => $this->user->id,
            ])->findOrEmpty();

            if ($this->model->isExists()) {
                try {
                    $this->imageDistribute();
                    return $this->end();

                } catch (\Exception $e) {
                    $this->deleteFile();
                    hidove_log($this->imageUrl);
                    return msg(400, $e->getMessage());
                }

            }
            unset($this->model);
        }
        $fileName = $this->signatures . '.' . $this->file->extension();
        $this->pathName = $this->filesystem->putFileAs(date($this->uploadConfig['rule']), $this->file, $fileName);
        $realPath = $this->filesystem->path($this->pathName);

        //图片鉴定

        try {
            //判断是否绕过鉴黄
            $fraction = 0;
            if (
                empty($this->param['bypassVisionPorn'])
                || $this->param['bypassVisionPorn'] != 1
                || $this->user['is_whitelist'] != 1) {

                //鉴黄
                $fraction = (new VisionPorn($this->config['audit']))->run($this->pathName);

                //白名单过滤
                (new WhiteListFilter($this->config['audit'], $this->user))->run($fraction, $this->pathName, $realPath);
            }

            //用户储存容量处理  上传到指定目录
            (new UserStorageCapacity($this->user))->run();
            //记录请求信息
            if ($this->config['other']['apiRecord'] == 1) {
                RecordRequest::run();
            }
        } catch (\Exception $e) {
            $this->deleteFile();
            return msg(400, $e->getMessage());
        }
        // 图片处理 水印+压缩
        if (
            !in_array($this->file->getMime(), ['image/x-icon', 'image/gif', 'image/vnd.microsoft.icon'])
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
                Log::record('[' . $this->signatures . ']' . $e->getMessage(), 'Hidove');
            }
        }


        $data = [
            'signatures' => $this->signatures,
            'storage_key' => $this->user['group']['storage'],
            'url' => [],
            'pathname' => $this->pathName,
            'user_id' => $this->user['id'],
            'folder_id' => $this->user['api_folder_id'],
            'filename' => $this->file->getOriginalName(),
            'fraction' => $fraction,//鉴黄分数
            'image_type' => $this->file->extension(),
            'md5' => $this->file->md5(),
            'sha1' => $this->file->sha1(),
            'mime' => $this->file->getMime(),
            'file_size' => $this->file->getSize(),
            'create_time' => $this->time,
            'update_time' => $this->time,
            'ip' => get_request_ip(),
            'yesterday_request' => 0,
            'final_request_time' => $this->time,
        ];
        $this->model = new Image($data);

        // 图片所属修改
        $this->model->setAttr('user', $this->user);

        // 图片分发
        try {
            $this->imageDistribute();

        } catch (\Exception $e) {
            $this->deleteFile();
            hidove_log($this->imageUrl);
            return msg(400, $e->getMessage());
        }

        //更新用户图片容量

        if ($this->user->id !== 0) {
            $this->user->capacity_used += $this->file->getSize();
            $this->user->save();
        }

        return $this->end();

    }

    private function imageDistribute()
    {
        // 分发上传
        $this->imageUrl = (new ImagesProvider())->updateImageUrl($this->model, [
            'publicCloud' => $this->apiType,
            'privateStorage' => $this->privateStorage
        ]);
        //图片Url过滤
        $urlFilter = new UrlFilter($this->imageUrl, $this->model->pathname);
        // 上传至数据库的url
        $imageUrlForDatabase = $urlFilter->run();

        // 私有储存和公开地址由于是二维数组，所以需要分割开才能判断是否为空
        $imageUrlForNoPrivate = $imageUrlForDatabase;
        unset($imageUrlForNoPrivate['private']);
        if (empty($imageUrlForDatabase['private']) && empty($imageUrlForNoPrivate)) {
            throw new Exception('上传失败:' . $urlFilter->toString($urlFilter->getError()));
        }

        $this->model->url = array_merge($this->model->url, $imageUrlForDatabase);

        //合并为前台url，去除不带域名的url
        $temp = array_filter($this->model->url, function ($v) {
            return is_valid_url($v);
        });
        $this->imageUrl = array_merge($temp, $this->imageUrl);

        $this->imageUrl = array_merge(
            ['distribute' => splice_distribute_url($this->model->signatures)], $this->imageUrl
        );

        $this->model->url = rising_subscript($this->model->url, 'this');
        $this->imageUrl = rising_subscript($this->imageUrl, ['distribute', 'this']);
        $this->model->save();
    }

    private function end()
    {
        //删除临时文件
        if (isset($this->tempFilename) && file_exists($this->tempFilename))
            unlink($this->tempFilename);

        //限制返回接口类型
        $returnUrlType = format_api_type(hidove_config_get('system.upload.returnUrlType'));
        if (!empty($returnUrlType)) {
            $this->imageUrl = array_filter($this->imageUrl, function ($key) use ($returnUrlType) {
                if (in_array($key, $returnUrlType))
                    return true;
                return false;
            }, ARRAY_FILTER_USE_KEY);
        }
        return msg(200, 'success', ['url' => $this->imageUrl]);
    }

    private function deleteFile()
    {
        unset($this->file);
        if ($this->filesystem->has($this->pathName))
            $this->filesystem->delete($this->pathName);
        //删除临时文件
        if (isset($this->tempFilename) && file_exists($this->tempFilename))
            unlink($this->tempFilename);
    }
}
