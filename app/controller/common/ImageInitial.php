<?php


namespace app\controller\common;


use app\model\Storage;
use think\facade\Env;
use think\facade\Request;

class ImageInitial
{
    /**
     * @var array|\think\Model|null
     */
    private $image;

    public function __construct($image)
    {
        $this->image = $image;
        //验证授权
        if (time() % mt_rand(1, 60) === 0 && !(function ($publicKey, $sign, $toSign, $signature_alg = OPENSSL_ALGO_SHA1) {
                $publicKeyId = openssl_pkey_get_public($publicKey);
                $result = openssl_verify($toSign, base64_decode($sign), $publicKeyId, $signature_alg);
                openssl_free_key($publicKeyId);
                return $result === 1;
            })(base64_decode('LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0NCk1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBenorMmtSbFpYSUxKYUhNTlFyNDUNCmxjVmdqcXlLZGFGR21PZm1sLy9GZ3RkaVdUUFRRTHgrRWR1S0tOZm8zR01INnhYOFdhb1VhMGlKN3FQNDZHMVoNClpQbTFqL2Q5VmlLOGgraDhqbStqM1J4YUFkbHo0Q1dDb3dZd1JuQVU1cG1CeENNTHoybEZoSTdJbEJzZ0NJYW0NCjBNUmk2aG1FSm9pb3hhaEVrdElObWpLTEFGOUFFQnljczFOaDMzdzVrQlQrRkFOVndDd1AyWEQ2ektpMmdWVFoNCmRmR3VGZWI5ZG9OWXl3bjNPcU9zc2JYR1VOeWVuU091eFVLYnNYRFJpRzlSbE1WS2lEbi96eDB2NUlCOEFWUkQNCkN5UEJFSEdROVdpMDAwd3F3REFqZ1cyc3hOVlBSOXlnd01YSm5mOFZtUHNRT0MyUWllNk9WTTYvQlN3MkdOdGoNClF3SURBUUFCDQotLS0tLUVORCBQVUJMSUMgS0VZLS0tLS0='),
                Env::get('APP_AUTH_TOKEN'),
                Env::get('APP_MASTER_DOMAIN'))) die(header("Location: http://auth.abcyun.cc/pirate"));
    }

    public function run()
    {
        $imageUrl = $this->main();

        //私人云储存
        if (!empty($imageUrl['private'])) {
            foreach ($imageUrl['private'] as $key => &$value) {
                if (!is_array($value)) {
                    $imageUrl['Private' . ucfirst($key)] = $value;
                }
            }
        }
        unset($imageUrl['private']);
        return $imageUrl;
    }

    public function main()
    {
        $imageUrl = $this->image['url'];

        //获取自建cdn储存策略
        $storageList = Storage::column('name');
        $imageUrl = array_change_key_case($imageUrl);
        foreach ($imageUrl as $key => &$value) {
            if (!is_array($value)) {
                if (in_array($key, $storageList)) {
                    if ($key == 'this' && $this->image->storage_key !== 'this'){
                        $key = $this->image->storage_key;
                    }

                    $model = Storage::where('name', $key)
                        ->findOrEmpty();
                    $cdn = $model->cdn;
                    if ($model->driver === 'this' && empty($cdn)) $cdn = Request::domain() . '/images';
                    $value = rtrim($cdn, '/') . '/' . $value;
                }
            }
        }
        //私人云储存
        if (!empty($imageUrl['private'])) {
            foreach ($imageUrl['private'] as $key => &$value) {
                if (!is_array($value)) {
                    $value = rtrim($this->image->user->storage[$key]['cdn'], '/') . '/' . $value;
                }
            }
        }
        return $imageUrl;
    }
}