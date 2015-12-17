<?php

namespace app\upload\validators;

use app\upload\FileFromUrl;
use app\upload\DirectServer;

/**
 * Description of FileValidator
 *
 * @author user
 */
class FileValidator
{
    const MAX_WIDTH    = 961;
    const MAX_HEIGHT   = 641;
    const MIN_WIDTH    = 959;
    const MIN_HEIGHT   = 639;
    const MIN_FILESIZE = 90 * 1024;
    const MAX_FILESIZE = 512 * 1024;

    public function checkResolution(DirectServer $object)
    {
        list($width, $height) = getimagesize($object->file['tmp_name']);
        if(empty($width)|| empty($height)){
            return $object->set_error('Image has no width or height');
        }
        if ($width > self::MAX_WIDTH) {
            $object->set_error(sprintf('Image width is to large. Your image has %dpx. Max is %dpx', $width,
                    self::MAX_WIDTH));
        }
        if ($height > self::MAX_HEIGHT) {
            $object->set_error(
                sprintf('Image height is to large. Your image has %dpx. Max is %dpx'), $height, self::MAX_HEIGHT);
        }
        if ($width < self::MIN_WIDTH) {
            $object->set_error(
                sprintf('Image width is to low. Your image has %dpx. Min is %dpx', $width, self::MIN_WIDTH));
        }
        if ($height < self::MIN_HEIGHT) {
            $object->set_error(
                sprintf('Image height is to low. Your image has %dpx. Min is %dpx'), $height, self::MIN_HEIGHT);
        }
    }

    public function checkMimeType(DirectServer $object)
    {
        if (!isset($object->file['mime'])) {
            $object->set_error('Target file have no mimeType');
        }
        if (!in_array($object->file['mime'], $object->mimes)) {
            $object->set_error("File don't have correct type. Avaiable are: " . implode('|', $object->mimes));
        }
    }

    public function checkFileSize(DirectServer $object)
    {
        if ($object->file['size_in_bytes'] < self::MIN_FILESIZE) {
            $object->set_error('File is too small');
        }
        if ($object->file['size_in_bytes'] > self::MAX_FILESIZE) {
            $object->set_error('File is too large');
        }
    }

    public function validateUrl(FileFromUrl $object)
    {
        $urlRegex = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})'
            . '(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3})'
            . '{2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])'
            . '(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))'
            . '|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)'
            . '*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
        if (!preg_match($urlRegex, $object->url)) {
            $object->set_error("This isn't url adress");
        }
    }
}