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
/**
 * Description of FileValidator
 *
 * @author user
 */
class FileValidator
{
    const MAX_WIDTH  = 960;
    const MAX_HEIGHT = 640;

    public function haveCorrectSize(Upload $object)
    {
        $imageSize = getimagesize($object->file['tmp_name']);
        if ($imageSize[0] > self::MAX_WIDTH) {
            $object->set_error('Image ' . $object->file['original_filename'] . ' has too much width');
        }
        if ($imageSize[1] > self::MAX_HEIGHT) {
            $object->set_error('Image ' . $object->file['original_filename'] . ' has too much heigh');
        }
    }

    public function isInDatabase(Upload $object)
    {
        $md5Temp = md5_file($object->file['tmp_name']);
        if (R::find(TB_IMAGES, 'md5 = :md5', [':md5' => $md5Temp])) {
            $object->set_error('Image ' . $object->file['original_filename'] . ' exists in Database');
        }
    }
}