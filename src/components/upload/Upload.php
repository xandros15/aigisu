<?php

namespace app\upload;

use app\upload\DirectServer;
use Exception;

abstract class Upload implements DirectServer
{
    const MODE_FILE_DIR = 755;

    /** @var UploadedFile */
    public $file;

    /** @var string */
    protected $mimeType;

    /** @var array */
    protected $errors = [];

    /** @var string */
    protected $destination;

    /** @var string */
    protected $root;

    abstract public function upload();

    abstract public function setFile($fileOrUrl);

    public function setValidator(array $callback)
    {
        
    }

    public function setDirectory($destination, $root = false)
    {
        if ($root) {
            $this->root = $root;
        } else {
            $this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
        }
        if (!$this->setDestination($destination)) {
            throw new Exception("Upload: Can't create destination: {$this->root}{$this->destination}");
        }
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function setError($message)
    {
        $this->errors[] = $message;
    }

    protected function setDestination($destination)
    {
        $this->destination = $destination . DIRECTORY_SEPARATOR;

        return $this->isDestinationExist() ? true : $this->createDestination();
    }

    private function isDestinationExist()
    {
        return is_writable($this->root . $this->destination);
    }

    private function createDestination()
    {
        return mkdir($this->root . $this->destination, self::MODE_FILE_DIR, true);
    }
}