<?php

namespace Aigisu\Upload;

use Aigisu\Google\GoogleFile;
use Aigisu\Imgur\Imgur;
use Exception;
use InvalidArgumentException;
use Models\Image;

class Rely
{
    public $extendedServers = [];
    public $directory;

    public function uploadFromServer($url, array &$errors)
    {
        $upload = new FileFromUrl();
        $upload->setDirectory($this->directory, ROOT_DIR);
        $upload->setFile($url);
        if ($upload->getErrors()) {
            $errors = array_merge($errors, $upload->getErrors());
        }

        return $upload;
    }

    public function uploadFromClient($file, array &$errors)
    {
        $upload = new FileFromClient();
        $upload->setDirectory($this->directory, ROOT_DIR);
        $upload->setFile($file);

        if ($upload->getErrors()) {
            $errors = array_merge($errors, $upload->getErrors());
        }

        return $upload;
    }

    public function setExtendedServer($name, ExtendedServer $extensionServer, $method)
    {
        $this->extendedServers[$name] = ['server' => $extensionServer, 'callback' => [$this, $method]];
    }

    public function uploadOnGoogleDrive(Image $model, Upload $file)
    {
        try {
            /* @var $google GoogleFile */
            $google = $this->getExtendedServer('google');
            $google->setMimeType($file->mimeType);
            $google->setExtension($file->extension);
            $google->setDescription('R18');
            $google->setName($model->server . $model->scene);
            $google->setCatalog($model->unit->name);
            $google->setFilename($file->filename);
            return $google->uploadFile()->resultOfUpload;
        } catch (Exception $e) {
            return $e;
        }
    }

    protected function getExtendedServer($name)
    {
        if (!isset($this->extendedServers[$name])) {
            throw new InvalidArgumentException("Server: '{$name}' is no implemented");
        }
        return $this->extendedServers[$name]['server'];
    }

    public function uploadOnImgur(Image $model, Upload $file)
    {
        try {
            /* @var $imgur Imgur */
            $imgur = $this->getExtendedServer('imgur');
            $imgur->setFilename($file->filename);
            $imgur->setName($model->server . ': ' . $model->unit->name);
            $imgur->setDescription('R18');
            $imgur->setCatalog($model->server);
            return $imgur->uploadFile();
        } catch (Exception $e) {
            return $e;
        }
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }
}