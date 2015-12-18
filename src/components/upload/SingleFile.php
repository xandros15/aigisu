<?php

namespace app\upload;

use app\upload\Upload;
use app\alert\Alert;
use models\Image;
use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use Exception;

class SingleFile
{
    const MAX_WIDTH    = 961;
    const MAX_HEIGHT   = 641;
    const MIN_WIDTH    = 959;
    const MIN_HEIGHT   = 639;
    const MIN_FILESIZE = 90 * 1024;
    const MAX_FILESIZE = 512 * 1024;

    /** @var int */
    public $id;

    /** @var OODBBean */
    public $unit;

    /** @var OODBBean */
    public $imageBean;

    /** @var Upload */
    public $file;

    /** @var array */
    public $errors = [];

    /** @var string */
    public $url;

    /** @var int */
    public $scene;

    /** @var string */
    public $server;

    public function setPost(array $post)
    {
        foreach ($post as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function setFile(Upload $file)
    {
        $this->file = $file;
    }

    public function setUnit(OODBBean $unit)
    {
        $this->unit = $unit;
    }

    public function upload()
    {
        R::begin();
        try {
            $this->transaction($this->file);
            $newFileName = sprintf('%s%d.%s', $this->file->destination, $this->id, $this->file->extension);
            $this->file->upload($newFileName);
            R::commit();
        } catch (Exception $exc) {
            R::rollback();
            $this->setError($exc->getMessage());
            Alert::add($exc->getMessage(), Alert::ERROR);
            $this->deteleFile($this->file->filename);
        }
        $this->deteleFile($this->file->oldFilename);
    }

    public function validate()
    {
        $servers = Image::getServers();

        if (!$this->server) {
            $this->setError('No server name in post request');
        }
        if (!isset($servers[$this->server])) {
            $this->setError('No server name found');
        }
        if (!$this->scene) {
            $this->setError('No scene number in post request');
        }
        if ($this->scene < 1 || $this->scene > $servers[$this->server]) {
            $this->setError("Wrong number of scene");
        }
        if (!$this->unit->name) {
            $this->setError("Unit name is null");
        }
        if ($this->isRecordExist()) {
            $this->setError("Image exist");
        }
        $this->checkFileSize($this->file);
        $this->checkMimeType($this->file, ['image/png']);
        $this->checkResolution($this->file->filename);

        if ($this->isErrors()) {
            foreach ($this->getErrors() as $error) {
                Alert::add($error, Alert::ERROR);
            }
            return false;
        }
        return true;
    }

    public function isErrors()
    {
        return (bool) $this->errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setError($message)
    {
        $this->errors[] = $message;
    }

    private function isRecordExist()
    {
        $imagesList = $this->unit->ownImagesList;
        foreach ($imagesList as $image) {
            if ($image->scene == $this->scene && $image->server == $this->server) {
                return true;
            }
        }
        return false;
    }

    private function transaction()
    {
        $image         = R::dispense(Image::tableName());
        $image->md5    = md5_file($this->file->filename);
        $image->server = $this->server;
        $image->scene  = $this->scene;

        $this->unit->ownImagesList[] = $image;

        R::store($this->unit);

        $this->imageBean = $image;
        $this->id        = $image->getID();
    }

    private function deteleFile($filename)
    {
        return (is_file($filename) && is_executable($filename)) ? unlink($filename) : false;
    }

    private function checkResolution($filename)
    {
        list($width, $height) = getimagesize($filename);
        if (empty($width) || empty($height)) {
            return $this->setError('Image has no width or height');
        }
        if ($width > self::MAX_WIDTH) {
            $this->setError(sprintf('Image width is to large. Your image has %dpx. Max is %dpx', $width, self::MAX_WIDTH));
        }
        if ($height > self::MAX_HEIGHT) {
            $this->setError(
                sprintf('Image height is to large. Your image has %dpx. Max is %dpx'), $height, self::MAX_HEIGHT);
        }
        if ($width < self::MIN_WIDTH) {
            $this->setError(
                sprintf('Image width is to low. Your image has %dpx. Min is %dpx', $width, self::MIN_WIDTH));
        }
        if ($height < self::MIN_HEIGHT) {
            $this->setError(
                sprintf('Image height is to low. Your image has %dpx. Min is %dpx'), $height, self::MIN_HEIGHT);
        }
    }

    private function checkMimeType(Upload $file, array $mime)
    {
        if (!$file->mimeType) {
            $this->setError('Target file have no mimeType');
        }
        if (!in_array($file->mimeType, $mime)) {
            $this->setError("File don't have correct type. Avaiable are: " . implode('|', $this->mimes));
        }
    }

    private function checkFileSize(Upload $file)
    {
        if ($file->filesize < self::MIN_FILESIZE) {
            $this->setError('File is too small');
        }
        if ($file->filesize > self::MAX_FILESIZE) {
            $this->setError('File is too large');
        }
    }
}