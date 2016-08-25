<?php

namespace Aigisu\Components\Upload;

use Exception;
use Slim\Http\UploadedFile;

class FileFromClient extends Upload
{
    /** @var UploadedFile */
    protected $file;

    public function upload($name)
    {
        $name = sprintf('%s%s.%s', $this->destination, $name, $this->extension);

        $this->file->moveTo($name);

        $this->oldFilename = $this->filename;
        $this->filename    = $name;

        return $this->filename;
    }

    public function setFile($fileOrUrl)
    {
        if (!$fileOrUrl instanceof UploadedFile) {
            throw new Exception("The File isn't instance of Slim\\UploadedFile. '" . gettype($fileOrUrl) . "' given.");
        }

        $this->file = $fileOrUrl;

        $this->filename = $fileOrUrl->file;
        $this->setFileSize();
        $this->setFileMime();
        $this->setFileMd5();
        $this->setFileResolution();
    }
}