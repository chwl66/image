<?php
declare (strict_types=1);

namespace app\validate;

use think\File;
use think\Validate;

class imageValidate extends Validate
{

    protected $rule = [];

    protected $message = [
        'image.checkImage' => ':attribute not a valid image',
    ];

    private $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = hidove_config_get('system.upload.');

        $this->rule = [
            'image' => [
                'require',
                'file',
                'image',
                'fileExt:' . $this->config['imageType'],
                'fileSize:' . $this->config['maxImageSize'],
            ]
        ];
    }

    /**
     * 验证图片的宽高及类型
     * @access public
     * @param mixed $file 上传文件
     * @param mixed $rule 验证规则
     * @return bool
     */
    public function image($file, $rule): bool
    {
        if (!($file instanceof File)) {
            return false;
        }

        if ($rule) {
            $rule = explode(',', $rule);

            [$width, $height, $type] = getimagesize($file->getRealPath());

            if (isset($rule[2])) {
                $imageType = strtolower($rule[2]);

                if ('jpg' == $imageType) {
                    $imageType = 'jpeg';
                }

                if (image_type_to_extension($type, false) != $imageType) {
                    return false;
                }
            }

            [$w, $h] = $rule;

            return $w == $width && $h == $height;
        }

        return in_array($this->getImageType($file->getRealPath()), [1, 2, 3, 6, 17, 18]);
    }
}
