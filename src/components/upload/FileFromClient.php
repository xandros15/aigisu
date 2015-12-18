<?php

namespace app\upload;

use Slim\Http\UploadedFile;
use Exception;

class FileFromClient extends Upload
{
    /** @var UploadedFile */
    protected $file;

    public function upload($filename)
    {
        $this->file->moveTo($filename);
        $this->oldFilename = $this->filename;
        $this->filename    = $filename;
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
    }
}