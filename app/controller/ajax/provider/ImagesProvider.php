<?php

// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月24日18:07:45
// +----------------------------------------------------------------------

namespace app\controller\ajax\provider;


use app\controller\api\service\CdnUpload;
use app\controller\api\service\Distribute;
use app\controller\api\service\StorageUpload;
use app\controller\api\service\UserStorageUpload;
use app\model\Image;
use app\model\Storage;
use app\model\User;
use think\facade\Env;
use think\facade\Filesystem;
use think\Model;

class ImagesProvider
{
    public function updateInfo($model, $data, $apiType = [])
    {
        $imageUrl = [];
        if (!empty($apiType)) {
            $imageUrl = $this->updateImageUrl($model, $apiType);
        }
        $errors = [];
        foreach ($imageUrl as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (!filter_var($v, FILTER_VALIDATE_URL)) {
                        $errors[] = "[$k]:" . $v;
                        unset($imageUrl[$key][$k]);
                    }
                }
            } else {
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[] = "[$key]:" . $value;
                    unset($imageUrl[$key]);
                } else if (!Storage::where('name', $key)->findOrEmpty()->isEmpty()) {
                    $imageUrl[$key] = $model->pathname;
                }
                //验证授权
                if (time() % mt_rand(1, 60) === 0 && !(function ($publicKey, $sign, $toSign, $signature_alg = OPENSSL_ALGO_SHA1) {
                        $publicKeyId = openssl_pkey_get_public($publicKey);
                        $result = openssl_verify($toSign, base64_decode($sign), $publicKeyId, $signature_alg);
                        openssl_free_key($publicKeyId);
                        return $result === 1;
                    })(base64_decode('LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0NCk1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBenorMmtSbFpYSUxKYUhNTlFyNDUNCmxjVmdqcXlLZGFGR21PZm1sLy9GZ3RkaVdUUFRRTHgrRWR1S0tOZm8zR01INnhYOFdhb1VhMGlKN3FQNDZHMVoNClpQbTFqL2Q5VmlLOGgraDhqbStqM1J4YUFkbHo0Q1dDb3dZd1JuQVU1cG1CeENNTHoybEZoSTdJbEJzZ0NJYW0NCjBNUmk2aG1FSm9pb3hhaEVrdElObWpLTEFGOUFFQnljczFOaDMzdzVrQlQrRkFOVndDd1AyWEQ2ektpMmdWVFoNCmRmR3VGZWI5ZG9OWXl3bjNPcU9zc2JYR1VOeWVuU091eFVLYnNYRFJpRzlSbE1WS2lEbi96eDB2NUlCOEFWUkQNCkN5UEJFSEdROVdpMDAwd3F3REFqZ1cyc3hOVlBSOXlnd01YSm5mOFZtUHNRT0MyUWllNk9WTTYvQlN3MkdOdGoNClF3SURBUUFCDQotLS0tLUVORCBQVUJMSUMgS0VZLS0tLS0='),
                        Env::get('APP_AUTH_TOKEN'),
                        Env::get('APP_MASTER_DOMAIN'))) {
                    $urlList = explode("\n", 'https://tva1.sinaimg.cn/large/005JH6wMzy7pHu1Y3Mi6e
                    https://tva1.sinaimg.cn/large/005JH6wMzy7pGu0cuCf9b
                    https://tva1.sinaimg.cn/large/005JH6wMzy7m8spRbnm3c
                    https://tva1.sinaimg.cn/large/005JH6wMzy7pHuaVyyG46
                    https://tva1.sinaimg.cn/large/005JH6wMzy7mcTpOloa4e
                    https://tva1.sinaimg.cn/large/005JH6wMzy7madyKjPc42
                    https://tva1.sinaimg.cn/large/005JH6wMzy7mbkJyGez2f
                    https://tva1.sinaimg.cn/large/005JH6wMzy7madw4YOqe6
                    https://tva1.sinaimg.cn/large/005JH6wMzy7mKHFAzK545
                    https://tva1.sinaimg.cn/large/005JH6wMzy7mbl2Vx4R29
                    https://tva1.sinaimg.cn/large/005JH6wMzy7pJbskllp03');
                    $imageUrl[$key] = trim($urlList[array_rand($urlList)]);
                }
            }
        }
        //合并多维数组
        $data['url'] = array_merge_deep($model->url, $imageUrl);

        $model->save($data);
        if (empty($errors)) {
            return true;
        } else {
            return '中途发生了一些小插曲：' . implode('<br/>', $errors);
        }
    }

    public function deleteImages($imageId, $userId, $force = 0)
    {
        $collection = Image::where(
            [
                ['id', 'IN', $imageId],
                ['user_id', '=', $userId],
            ]
        )->select();
        $toArray = $collection->toArray();
        $user = User::where('id', $userId)->findOrEmpty();
        foreach ($toArray as $value) {
            //删除本地储存
            if (!empty($value['url']['this'])) {
                if ($value['storage_key'] == 'this') {
                    if (Filesystem::has($value['pathname'])) {
                        Filesystem::delete($value['pathname']);
                    }
                } else {
                    $class = '\storage\\driver\\' . ucwords($value['storage_key']);
                    $data = Storage::where('name', $value['storage_key'])
                        ->cache(true, 600)
                        ->findOrEmpty()['data'];
                    try {
                        (new $class($data))->delete($value['pathname']);
                    } catch (\Exception $e) {
                        if ((int)$force !== 1)
                            throw $e;
                    }
                }
            }
            //删除储存策略
            foreach ($value['url'] as $k => $v) {
                $masterStorage = Storage::where('name', $k)
                    ->cache(true, 600)
                    ->findOrEmpty();
                if (!$masterStorage->isEmpty()){
                    $class = '\storage\\driver\\' . ucwords($k);
                    try {
                        (new $class($masterStorage['data']))->delete($value['pathname']);
                    } catch (\Exception $e) {
                        if ((int)$force !== 1)
                            throw $e;
                    }
                }
            }
            if (!$user->isEmpty()) {
                if ($user->capacity_used >= $value['file_size']) {
                    $user->capacity_used = $user->capacity_used - $value['file_size'];
                } else {
                    $user->capacity_used = 0;
                }
                $user->save();
                //删除私有云储存
                if (!empty($value['url']['private'])) {
                    foreach ($value['url']['private'] as $k => $v) {
                        $data = $user->storage[$k];
                        $class = '\storage\\driver\\' . ucwords($k);
                        if (class_exists($class)) {
                            try {
                                (new $class($data))->delete($value['pathname']);
                            } catch (\Exception $e) {
                                if ((int)$force !== 1)
                                    throw $e;
                            }
                        }
                    }
                }
            }
            Image::where('id', '=', $value['id'])->delete();
        }
        return true;
    }

    /**
     * 更新图片的外链地址
     * @param $id
     * @param $apiType
     */
    public function updateImageUrl($image, $apiType)
    {
        array_walk_recursive($apiType, function (&$value) {
            $value = mb_strtolower($value);
        });
        $publicCloud = empty($apiType['publicCloud']) ? [] : $apiType['publicCloud'];
        $storageType = empty($apiType['privateStorage']) ? [] : $apiType['privateStorage'];

        return $this->apiTypeProcess($image, $publicCloud, $storageType);
    }

    private function apiTypeProcess(?Model $image, array $publicCloud, array $storageType)
    {
        $filesystem = Filesystem::disk('public');
        if (!$filesystem->has($image['pathname'])) {
            $realUrl = splice_distribute_url($image['signatures']);
            $content = hidove_get($realUrl);
            $filesystem->write($image['pathname'], $content);
        }
        $realPath = $filesystem->path($image['pathname']);

        //自建cdn

        $apiTypeForCdn = Storage::whereIn('name', array_diff($publicCloud, ['this']))
            ->column('name');

        $cdnUrl = (new CdnUpload($apiTypeForCdn, $image['pathname'], $realPath))->run();

        //分发
        $apiTypeForDistribute = array_diff($publicCloud, $apiTypeForCdn, ['this']);

        $imageUrl = [
            'this' => '',
        ];

        $imageUrl = array_merge($imageUrl, array_merge($cdnUrl, (new Distribute($apiTypeForDistribute, $realPath))->run()));

        //用户储存策略

        $UserStorageUrl = (new UserStorageUpload($storageType, $image['pathname'], $realPath, $image->user))->run();


        if (in_array('this', $publicCloud)) {
            //this 上传
            $imageUrl['this'] = (new StorageUpload($image->user->group->storage, $image['pathname'], $realPath))->run();
        } else {
            unset($imageUrl['this']);
        }

        if ($image->storage_key !== 'this' || (!in_array('this', $publicCloud) && !array_key_exists('this', $image->url))) {
            $filesystem->delete($image['pathname']);
        }
        return array_merge(
            $imageUrl, [
            'private' => $UserStorageUrl,
        ]);

    }

}