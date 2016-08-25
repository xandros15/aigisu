<?php

namespace Aigisu\Components\Upload;

use Aigisu\Components\Upload\Helpers\MimeTypeExtensionGuesser as Extension;
use RuntimeException;

abstract class Upload
{
    const MODE_FILE_DIR = 755;

    /** @var string */
    public $filename = '';

    /** @var string */
    public $oldFilename = '';

    /** @var int */
    public $filesize;

    /** @var string */
    public $mimeType;

    /** @var string */
    public $extension;

    /** @var string */
    public $destination;

    /** @var string */
    public $root;

    /** @var string */
    public $md5;

    /** @var int */
    public $width;

    /** @var int */
    public $height;

    /** @var array */
    protected $errors = [];

    abstract public function upload($name);

    abstract public function setFile($fileOrUrl);

    public function setDirectory($destination, $root = false)
    {
        if ($root) {
            $this->root = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
        } else {
            $this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
        }
        if (!$this->setDestination($destination)) {
            throw new RuntimeException("Upload: Can't create destination: {$this->destination}");
        }
    }

    protected function setDestination($destination)
    {
        $this->destination = $this->root . $destination . DIRECTORY_SEPARATOR;

        return $this->isDestinationExist() ? true : $this->createDestination();
    }

    private function isDestinationExist()
    {
        return is_writable(dirname($this->destination));
    }

    private function createDestination()
    {
        return mkdir($this->root . $this->destination, self::MODE_FILE_DIR, true);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function setFileResolution()
    {
        list($this->width, $this->height) = getimagesize($this->filename);
    }

    protected function setFileSize()
    {
        return $this->filesize = filesize($this->filename);
    }

    protected function setFileMime()
    {
        $this->mimeType  = image_type_to_mime_type(exif_imagetype($this->filename));
        $this->extension = Extension::guess($this->mimeType);
        return $this->mimeType;
    }

    protected function setFileMd5()
    {
        $this->md5 = md5_file($this->filename);
    }

    protected function setError($message)
    {
        $this->errors[] = $message;
    }

    protected function generateFilename()
    {
        return sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time() . '.tmp';
    }
}