<?php

namespace app\upload;

class Rely
{
    protected $mimeType = 'image/png';

    public function setMimeType($mimeType)
    {
            $this->mimeType = $mimeType;
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
            $this->errors = $errors;
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
            $this->errors = $errors;
        }

        return $results;
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
}