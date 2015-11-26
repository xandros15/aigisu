<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\validators;

use app\Upload;
use app\UploadImages;
use RedBeanPHP\Facade as R;
use Exception;

/**
 * Description of FileValidator
 *
 * @author user
 */
class FileValidator
{
    const MAX_WIDTH  = 961;
    const MAX_HEIGHT = 641;
    const MIN_WIDTH  = 959;
    const MIN_HEIGHT = 639;

    public function uploadValidator(Upload $object)
    {
        try {
            $this->checkResolution($object->file['tmp_name']);
            $this->isInDatabase($object->file['tmp_name']);
        } catch (Exception $exc) {
            $object->set_error('file: ' . $object->file['original_filename'] . ' error: ' . $exc->getMessage());
        }
    }

    public function checkResolution($file)
    {
        list($width, $height) = getimagesize($file);
        if ($width > self::MAX_WIDTH) {
            throw new Exception("Wrong image width");
        }
        if ($height > self::MAX_HEIGHT) {
            throw new Exception("Wrong image height");
        }
    }

    public function isInDatabase($file)
    {
        $md5Temp = md5_file($file);
        if (R::find(TB_IMAGES, 'md5 = :md5', [':md5' => $md5Temp])) {
            throw new Exception('Image exists in Database');
        }
    }
}