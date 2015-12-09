<?php

namespace app\upload;

use app\google\GoogleFile;
use app\imgur\Imgur;
use app\upload\ExtedndetServer;
use app\upload\DirectFiles;
use app\upload\UrlFiles;
use app\upload\validators\FileValidator;
use Exception;
use models\Images;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;

class UploadImages
{
    const TEMPORARY_FOLDER = 'tmp';

    public $newDir;
    protected $mimeType         = 'image/png';
    protected $errors           = [];
    protected $extention        = 'png';
    protected $destination;
    protected $extentionServers = [];

    /** @var OODBBean */
    private $image;

    public function uploadFiles()
    {
        global $query;
        $post                = $query->post;
        $files               = $query->files;
        $avaibleImagesFields = ['dmm1', 'dmm2'];
        //die();
        foreach ($files as $input => $file) {
            if (!in_array($input, $avaibleImagesFields)) {
                continue;
            }
            if ($post->{$input}) {
                $results = $this->uploadFromServer($post->{$input});
            } else {
                if ($file['error']) {
                    continue;
                }
                $results = $this->uploadFromClient($file);
            }
            if ($this->errors) {
                var_dump($this->errors);
            } else {
                //$this->addImageToDatabase($results, $input);
                //(!$this->image) || $this->uploadOnExtendedServers();
            }
            (!isset($results['full_patch'])) || $this->deteleFile($results['full_patch']);
        }
    }

    private function uploadFromServer($url)
    {
        $upload = new UrlFiles();
        $upload->setDirectory(self::TEMPORARY_FOLDER, ROOT_DIR);
        $upload->setMimeTypes([$this->mimeType]);
        $upload->file($url);

        $validator = new FileValidator();
        $upload->setValidator([$validator, 'checkFileSize']);
        $upload->setValidator([$validator, 'checkMimeType']);
        $upload->setValidator([$validator, 'checkResolution']);

        $results = $upload->upload();

        if (($errors = $upload->getErrors())) {
            $this->errors = array_merge($this->errors, $errors);
        }

        return $results;
    }

    private function uploadFromClient(array $file)
    {
        $upload = new DirectFiles();
        $upload->setDirectory(self::TEMPORARY_FOLDER, ROOT_DIR);
        $upload->setMimeTypes([$this->mimeType]);
        $upload->file($file);

        $validator = new FileValidator();
        $upload->setValidator([$validator, 'checkFileSize']);
        $upload->setValidator([$validator, 'checkResolution']);

        $results = $upload->upload();
        if (($errors  = $upload->getErrors())) {
            $this->errors = array_merge($this->errors, $errors);
        }

        return $results;
    }

    private function addImageToDatabase(array $results, $input)
    {
        $this->createDestination();

        R::begin();
        try {
            $this->transaction($results, $input);
            $newName = $this->getNewName($this->image->id);
            $this->moveFile($results['full_path'], $newName);
            R::commit();
        } catch (Exception $exc) {
            var_dump($exc->getMessage());
            R::rollback();
        }
    }

    private function transaction(array $results, $input)
    {
        $unit                  = R::load(TB_NAME, (int) $this->post->id);
        $this->validateBeforeCommit($unit, $input);
        $this->setImage($results['full_path']);
        $this->image->type     = $input;
        $unit->ownImagesList[] = $this->image;
        R::store($unit);
    }

    public function validateBeforeCommit(OODBBean $unit, $input)
    {
        if (!is_string($input)) {
            throw new Exception('input isn\'t string');
        }
        if (!$unit) {
            throw new Exception('wrong id');
        }
        $images = R::find(Images::tableName(), ' units_id = :id and type = :type ',
                [ ':id' => $unit->id, ':type' => $input]);
        if ($images) {
            throw new Exception('Image exists');
        }
        if (!$unit->name) {
            throw new Exception('Unit name is null');
        }
    }

    public function setExtentionServers()
    {
        $this->setExtentionServer('google', new GoogleFile());
        $this->setExtentionServer('imgur', Imgur::facade());
    }

    public function setExtentionServer($name, ExtedndetServer $extentionServer)
    {
        $this->extentionServers[$name] = $extentionServer;
    }

    public function getExtentionServer($name)
    {
        if (!isset($this->extentionServers[$name])) {
            throw new Exception("Server: '{$name}' is no implemented");
        }
        return $this->extentionServers[$name];
    }

    private function uploadOnExtendedServers()
    {

        R::begin();
        try {
            $this->uploadOnGoogleDrive();
            R::commit();
        } catch (Exception $exc) {
            R::rollback();
            error_log($exc->getMessage());
        }
        R::begin();
        try {
            $this->uploadOnImgur();
            R::commit();
        } catch (Exception $exc) {
            R::rollback();
            error_log($exc->getMessage());
        }
    }

    private function uploadOnGoogleDrive()
    {
        /* @var $google GoogleFile */
        $google   = $this->getExtentionServer('google');
        $google->setMimeType($this->mimeType);
        $google->setExtension($this->extention);
        $google->setDescription('R18');
        $google->setName($this->image->type);
        $google->setCatalog($this->image->units->name);
        $google->setFilename($this->getNewName($this->image->id));
        $response = $google->uploadFile()->resultOfUpload;
        if ($response) {
            $this->image->google = $response->id;
            R::store($this->image);
        }
    }

    private function uploadOnImgur()
    {
        /* @var $imgur Imgur */
        $imgur    = $this->getExtentionServer('imgur');
        $imgur->setFilename($this->getNewName($this->image->id));
        $imgur->setName(rtrim($this->image->type, '12') . ': ' . $this->image->units->name);
        $imgur->setDescription('R18');
        $imgur->setCatalog(rtrim($this->image->type, '12'));
        $response = $imgur->uploadFile();
        if (isset($response['data']['id']) && isset($response['data']['deletehash'])) {
            $this->image->imgur   = $response['data']['id'];
            $this->image->delhash = $response['data']['deletehash'];
            R::store($this->image);
        }
    }

    private function moveFile($current, $destiny)
    {
        if (!rename($current, $destiny)) {
            throw new Exception('Can\'t rename file');
        }
    }

    private function getNewName($id)
    {
        return $this->newDir . $id . '.' . $this->extention;
    }

    private function setImage($fullPatch)
    {
        $this->image      = R::dispense(TB_IMAGES);
        $this->image->md5 = md5_file($fullPatch);
    }

    private function createDestination()
    {
        $newDir = ROOT_DIR . $this->destination;
        if (!is_dir($newDir)) {
            mkdir($newDir, 755);
        }

        $this->newDir = $newDir . DIRECTORY_SEPARATOR;
    }

    private function deteleFile($filename)
    {
        return (is_file($filename) && is_executable($filename)) ? unlink($filename) : false;
    }
}