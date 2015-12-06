<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\upload\validators;

use app\upload\Upload;
use app\upload\UploadImages;
use RedBeanPHP\Facade as R;
use Exception;

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
    const HTTP_ERROR   = '~^HTTP/[12]\.[0-9] [54][0-9]{2}.*$~i';

    public function uploadValidator(Upload $object)
    {
        try {
            $this->checkResolution($object->file['tmp_name']);
            $this->isInDatabase($object->file['tmp_name']);
            $this->checkFileSize(filesize($object->file['tmp_name']));
        } catch (Exception $exc) {
            $object->set_error('file: ' . $object->file['original_filename'] . ' error: ' . $exc->getMessage());
        }
    }

    public function checkResolution($file)
    {
        list($width, $height) = getimagesize($file);
        if ($width > self::MAX_WIDTH) {
            throw new Exception(
            sprintf('Image width is to large. Your image has %dpx. Max is %dpx', $width, self::MAX_WIDTH));
        }
        if ($height > self::MAX_HEIGHT) {
            throw new Exception(
            sprintf('Image height is to large. Your image has %dpx. Max is %dpx'), $height, self::MAX_HEIGHT);
        }
        if ($width < self::MIN_WIDTH) {
            throw new Exception(
            sprintf('Image width is to low. Your image has %dpx. Min is %dpx', $width, self::MIN_WIDTH));
        }
        if ($height < self::MIN_HEIGHT) {
            throw new Exception(
            sprintf('Image height is to low. Your image has %dpx. Min is %dpx'), $height, self::MIN_HEIGHT);
        }
    }

    public function isInDatabase($file)
    {
        $md5Temp = md5_file($file);
        if (R::find(TB_IMAGES, 'md5 = :md5', [':md5' => $md5Temp])) {
            throw new Exception('Image exists in Database');
        }
    }

    public function checkMimeType($contentType, array $mimeTypes)
    {
        if (!isset($contentType)) {
            throw new Exception('Target file have no mimeType');
        }
        if ($mimeTypes && !in_array($contentType, $mimeTypes)) {
            throw new Exception('Wrong mime types');
        }
    }

    public function checkFileSize($filesize)
    {
        if ($filesize < self::MIN_FILESIZE) {
            throw new Exception('File is too small');
        }
        if ($filesize > self::MAX_FILESIZE) {
            throw new Exception('File is too large');
        }
    }

    public function checkHttpResponse(array $headers)
    {
        if (!$headers) {
            throw new Exception('Target server no response');
        }
        foreach ($headers as $key => $option) {
            if (is_array($option) || !is_int($key)) {
                continue;
            }
            if (preg_match(self::HTTP_ERROR, $option)) {
                throw new Exception('Target server no response');
            }
        }

        return true;
    }
}