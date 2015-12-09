<?php

namespace app\upload;

use app\google\GoogleFile;
use app\imgur\Imgur;
use app\upload\ExtedndetServer;
use app\upload\DirectFiles;
use app\upload\UrlFiles;
use app\upload\validators\FileValidator;
use RedBeanPHP\OODBBean;
use Symfony\Component\HttpFoundation\File\File;

class Rely
{
    const TEMPORARY_FOLDER = 'tmp';

    protected $mimeType = 'image/png';
    protected $extendedServers = [];

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function uploadFromServer($url, array &$errors)
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

        if ($upload->getErrors()) {
            $errors = $upload->getErrors();
        }

        return $results;
    }

    public function uploadFromClient(array $file, array &$errors)
    {
        $upload = new DirectFiles();
        $upload->setDirectory(self::TEMPORARY_FOLDER, ROOT_DIR);
        $upload->setMimeTypes([$this->mimeType]);
        $upload->file($file);

        $validator = new FileValidator();
        $upload->setValidator([$validator, 'checkFileSize']);
        $upload->setValidator([$validator, 'checkResolution']);

        $results = $upload->upload();

        if ($upload->getErrors()) {
            $errors = $upload->getErrors();
        }

        return $results;
    }

    public function setExtendedServer($name, ExtedndetServer $extentionServer)
    {
        $this->extendedServers[$name] = $extentionServer;
    }

    public function uploadOnGoogleDrive(OODBBean $image, File $file)
    {
        /* @var $google GoogleFile */
        $google = $this->getExtendednServer('google');
        $google->setMimeType($file->getMimeType());
        $google->setExtension($file->guessExtension());
        $google->setDescription('R18');
        $google->setName($image->type);
        $google->setCatalog($image->units->name);
        $google->setFilename($file->getFilename());
        return $google->uploadFile()->resultOfUpload;
    }

    public function uploadOnImgur(OODBBean $image, File $file)
    {
        /* @var $imgur Imgur */
        $imgur = $this->getExtendednServer('imgur');
        $imgur->setFilename($file->getFilename());
        $imgur->setName(rtrim($image->type, '12') . ': ' . $image->units->name);
        $imgur->setDescription('R18');
        $imgur->setCatalog(rtrim($image->type, '12'));
        return $imgur->uploadFile();
    }

    protected function getExtendednServer($name)
    {
        if (!isset($this->extendedServers[$name])) {
            throw new Exception("Server: '{$name}' is no implemented");
        }
        return $this->extendedServers[$name];
    }
}