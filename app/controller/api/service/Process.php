<?php


namespace app\controller\api\service;

use think\facade\Filesystem;
use think\Image;

/**
 * 图片处理
 * Class Process
 * @package app\controller\api\service
 */
class Process
{
    private $imagePath;
    private $image;
    private $config;


    public function __construct($config, $imagePath)
    {
        $this->config = $config;

        if (
            $this->config['process']['quality'] == 100
            && $this->config['process']['interlace'] != 1
            && $this->config['watermark']['switch'] != 1
        ) return;

        $this->imagePath = $imagePath;

        $this->image = Image::open($this->imagePath);
    }

    /**
     * 图片处理
     */
    public function run()
    {

        if (
            $this->config['process']['quality'] == 100
            && $this->config['process']['interlace'] != 1
            && $this->config['watermark']['switch'] != 1
        ) return;

        if (in_array($this->image->mime(), ['image/x-icon', 'image/gif', 'image/vnd.microsoft.icon']))
            return;

        //水印添加
        if ($this->config['watermark']['switch'] == 1) {
            //判断尺寸是否符合要求
            if (
                $this->image->width() >= $this->config['watermark']['width']
                &&
                $this->image->height() >= $this->config['watermark']['height']
            ) $this->addWatermark($this->config['watermark']['type']);
        }
        //保存图片
        if (empty($this->config['process']['quality'])) $this->config['process']['quality'] = 100;
        $this->config['process']['interlace'] = boolval($this->config['process']['interlace']);
        $this->image->save($this->imagePath, null, $this->config['process']['quality'], $this->config['process']['interlace']);
    }

    protected function addWatermark($type)
    {
        switch ($type) {
            case 1:
                $this->addImageWatermark();
                break;
            case 2:
                $this->addTextWatermark();
                break;
            default:
                return;
        }
    }

    //添加图片水印
    protected function addImageWatermark()
    {
        $imageWatermark = $this->config['imageWatermark'];
        ///images/watermark
        if (!Filesystem::has($imageWatermark['pathname'])) {
            return;
        }
        if (empty($imageWatermark['locate'])) $imageWatermark['locate'] = 9;
        if (empty($imageWatermark['alpha'])) $imageWatermark['alpha'] = 100;
        $this->image->water(Filesystem::path($imageWatermark['pathname']),
            $imageWatermark['locate'],
            $imageWatermark['alpha']);
    }

    //添加文字水印
    protected function addTextWatermark()
    {
        $textWatermark = $this->config['textWatermark'];
        if (empty($textWatermark['text'])) $textWatermark['text'] = "Hidove";
        //字体文件路径
        if (empty($textWatermark['font'])) {
            $textWatermark['font'] = realpath('static/common/ttf/default.ttf');
        } else {
            $textWatermark['font'] = realpath('static/common/ttf') . "/" . $textWatermark['font'];
            if (!file_exists($textWatermark['font'])) {
                $textWatermark['font'] = realpath('static/common/ttf/default.ttf');
            }
        }
        if (empty($textWatermark['size'])) $textWatermark['size'] = 12;
        if (empty($textWatermark['color'])) $textWatermark['color'] = '#00000000';
        if ($textWatermark['locate'] < 1 || $textWatermark['locate'] > 9) $textWatermark['locate'] = 9;
        if (empty($textWatermark['offset'])) $textWatermark['offset'] = 0;
        if (empty($textWatermark['angle'])) $textWatermark['angle'] = 0;
        $this->image->text(
            $textWatermark['text'],
            $textWatermark['font'],
            $textWatermark['size'],
            $textWatermark['color'],
            $textWatermark['locate'],
            $textWatermark['offset'],
            $textWatermark['angle']
        );
    }
}