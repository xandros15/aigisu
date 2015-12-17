<?php

namespace app\upload;

use Symfony\Component\HttpFoundation\File\File;
use Slim\Http\UploadedFile;
use models\Image;

class SingleFile
{
    public $name;
    public $file;
    public $url;
    public $status;
    public $size_in_bytes;
    public $full_path;
    public $filename;
    public $path;
    public $scene;
    public $server;

    /** @var File */
    public $object;

    public static function loadFile(UploadedFile $file, array $info)
    {
        if (empty($file->getClientFilename()) && empty($info['url'])) {
            return false;
        }
        return new SingleFile($file, $info);
    }

    public function __construct(UploadedFile $file, array $info)
    {
        if ($file->getError() === UPLOAD_ERR_OK) {
            $this->name = $file->getClientFilename();
            $this->file = $file->file;
        } elseif ($file->getError() !== UPLOAD_ERR_NO_FILE) {
            Alert::add("File: '{$file['name']}' got error. Maby is to big.", Alert::ERROR);
            return;
        } elseif (!empty($info['url'])) {
            $this->name = $info['url'];
            $this->file = $info['url'];
        }
        $this->setInfo($info);
    }

    public function setInfo($info)
    {
        $servers = Image::getServers();
        if (!isset($info['server'])) {
            throw new Exception('No server name in post request');
        }
        $this->server = $info['server'];
        if (!isset($servers[$this->server])) {
            throw new Exception('No server name found');
        }
        if (!isset($info['scene'])) {
            throw new Exception('No scene number in post request');
        }
        $this->scene = (int) $info['scene'];
        if ($this->scene < 1 || $this->scene > $servers[$this->server]) {
            throw new Exception("Wrong number of scene");
        }
    }

    public function setResults($results)
    {
        $this->status        = $results['status'];
        $this->size_in_bytes = $results['size_in_bytes'];
        $this->full_path     = $results['full_path'];
        $this->filename      = $results['filename'];
        $this->path          = $results['path'];
    }

    public function setObject()
    {
        if (!is_file($this->full_path)) {
            throw new Exception("File: '{$this->full_path}' no exist");
        }
        $this->object = new File($this->full_path);
    }
}