<?php

namespace app\upload;

use app\google\GoogleFile;
use app\imgur\Imgur;
use app\upload\ExtedndetServer;
use app\upload\FileFromClient;
use app\upload\FileFromUrl;
use models\Image;
use Exception;

class Rely
{
    public $extendedServers = [];

    public function uploadFromServer($url, array &$errors)
    {
        $upload = new FileFromUrl();
        $upload->setDirectory(Image::IMAGE_DIRECTORY, ROOT_DIR);
        $upload->setFile($url);
        if ($upload->getErrors()) {
            $errors = $upload->getErrors();
        }

        return $upload;
    }

    public function uploadFromClient($file, array &$errors)
    {
        $upload = new FileFromClient();
        $upload->setDirectory(Image::IMAGE_DIRECTORY, ROOT_DIR);
        $upload->setFile($file);

        if ($upload->getErrors()) {
            $errors = $upload->getErrors();
        }

        return $upload;
    }

    public function setExtendedServer($name, ExtedndetServer $extentionServer, $method)
    {
        $this->extendedServers[$name] = [ 'server' => $extentionServer, 'callback' => [$this, $method]];
    }

    public function uploadOnGoogleDrive(SingleFile $image)
    {
        try {
            /* @var $google GoogleFile */
            $google = $this->getExtendednServer('google');
            $google->setMimeType($image->file->mimeType);
            $google->setExtension($image->file->extension);
            $google->setDescription('R18');
            $google->setName($image->server . $image->scene);
            $google->setCatalog($image->unit->name);
            $google->setFilename($image->file->filename);
            return $google->uploadFile()->resultOfUpload;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function uploadOnImgur(SingleFile $image)
    {
        try {
            /* @var $imgur Imgur */
            $imgur = $this->getExtendednServer('imgur');
            $imgur->setFilename($image->file->filename);
            $imgur->setName($image->server . ': ' . $image->unit->name);
            $imgur->setDescription('R18');
            $imgur->setCatalog($image->server);
            return $imgur->uploadFile();
        } catch (Exception $e) {
            return $e;
        }
    }

    protected function getExtendednServer($name)
    {
        if (!isset($this->extendedServers[$name])) {
            throw new Exception("Server: '{$name}' is no implemented");
        }
        return $this->extendedServers[$name]['server'];
    }
}