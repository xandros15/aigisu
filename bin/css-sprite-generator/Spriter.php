<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-12
 * Time: 22:43
 */

namespace Xandros15;


class Spriter
{
    const OPT_CSS_CLASS_NAME = 'OPT_CSS_CLASS_NAME';
    const OPT_CSS_PREFIX = 'OPT_CSS_PREFIX';
    const OPT_CSS_IMAGE_URL = 'OPT_CSS_IMAGE_URL';
    const OPT_SPRITE_FORMAT = 'OPT_SPRITE_FORMAT';

    const FORMAT_PNG = 'png';
    const FORMAT_JPG = 'jpg';


    /** @var  \Imagick */
    private $imagick;
    private $options = [
        self::OPT_CSS_CLASS_NAME => 'sprite',
        self::OPT_CSS_PREFIX => 'sprite',
        self::OPT_CSS_IMAGE_URL => 'sprite',
        self::OPT_SPRITE_FORMAT => 'png'
    ];

    public function __construct(array $paths, array $options = [])
    {
        $this->setOptions($options);
        $this->imagick = new \Imagick($paths);
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function save($cssFilename, $spriteFilename)
    {
        return $this->generateSprite()->writeImage($spriteFilename) &&
        file_put_contents($cssFilename, $this->generateCss());
    }

    private function generateSprite()
    {
        $this->imagick->resetIterator();
        $sprite = $this->imagick->appendImages(true);
        $sprite->setFormat($this->options[self::OPT_SPRITE_FORMAT]);
        return $sprite;
    }

    private function generateCss()
    {
        $imagesCss = '';
        $prefix = $this->options[self::OPT_CSS_PREFIX];
        $className = $this->options[self::OPT_CSS_CLASS_NAME];
        $imageUrl = $this->options[self::OPT_CSS_IMAGE_URL];
        $totalWidth = 0;
        $totalHeight = 0;
        foreach ($this->imagick as $image) {
            $imageClassName = $prefix . '-' . basename($image->getImageFilename());
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();
            $imagesCss .= ".{$className}.{$imageClassName}{background-position:0-{$totalHeight}px;width:{$width}px;height:{$height}px;}";

            $totalWidth = $totalWidth < $width ? $width : $totalWidth;
            $totalHeight += $height;
        }
        return "{$imagesCss} .{$className}" .
        "{background-image:url('{$imageUrl}');background-repeat:no-repeat;}";
    }
}
