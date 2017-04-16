<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-30
 * Time: 13:21
 */

namespace Aigisu\Components\Validators\Rules;


use InvalidArgumentException;
use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as v;
use SplFileInfo;

class ImageSize extends AbstractRule
{
    const HARD_MAX_HEIGHT = 6000;
    const HARD_MAX_WIDTH = 6000;

    /** @var int */
    public
        $maxHeight = self::HARD_MAX_HEIGHT,
        $maxWidth = self::HARD_MAX_WIDTH;

    /** @var int */
    public
        $minHeight = 0,
        $minWidth = 0;

    /**
     * ImageSize constructor.
     *
     * @param null $minResolution
     * @param null $maxResolution
     */
    public function __construct($minResolution = null, $maxResolution = null)
    {
        $this->setResolutionParams($minResolution, $maxResolution);
    }

    /**
     * @param $input
     *
     * @return bool
     */
    public function validate($input)
    {
        $passed = false;

        if ($input) {
            if ($input instanceof SplFileInfo) {
                $input = $input->getPathname();
            }

            if ($imageSize = getimagesize($input)) {
                list($width, $height) = $imageSize;
                $passed = $this->isCorrectResolution($width, $height);
            }
        }

        return $passed;
    }

    /**
     * @param $minResolution
     * @param $maxResolution
     */
    private function setResolutionParams($minResolution, $maxResolution): void
    {
        if ($minResolution) {
            $minResolution = $this->parseResolutionParams($minResolution);
            $this->minHeight = $minResolution['height'];
            $this->minWidth = $minResolution['width'];
        }

        if ($maxResolution) {
            $maxResolution = $this->parseResolutionParams($maxResolution);
            $this->maxHeight = $maxResolution['height'];
            $this->maxWidth = $maxResolution['width'];
        }
    }

    /**
     * @param  array|int $resolution
     *
     * @throws InvalidArgumentException
     * @return array
     */
    private function parseResolutionParams($resolution): array
    {
        if (is_array($resolution)) {
            return ['width' => reset($resolution), 'height' => end($resolution)];
        }

        if (is_scalar($resolution)) {
            return ['width' => $resolution, 'height' => $resolution];
        }

        throw new InvalidArgumentException('Wrong type of resolution');
    }

    /**
     * @param int $width
     * @param int $height
     *
     * @return bool
     */
    private function isCorrectResolution(int $width, int $height): bool
    {
        $correctWidth = v::between(
            $this->minWidth, $this->maxWidth
        )->validate($width);
        $correctHeight = v::between(
            $this->minHeight, $this->maxHeight
        )->validate($height);

        return $correctHeight && $correctWidth;
    }
}
