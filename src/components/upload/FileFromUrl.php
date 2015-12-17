<?php

namespace app\upload;

use app\upload\validators\FileValidator;
use Exception;

class FileFromUrl implements DirectServer
{
    public $file          = [];
    public $url;
    public $filename;
    public $mimes         = [];
    protected $errors     = [];
    protected $validators = [];
    protected $destination;
    protected $root;
    protected $ctx;

    public function __construct()
    {
        $this->setCtx();
    }

    public function setMimeTypes(array $mimeTypes)
    {
        $this->mimes = array_merge($this->mimes, $mimeTypes);
    }

    public function setDestination($destination)
    {
        $this->destination = $destination . DIRECTORY_SEPARATOR;

        return $this->isDestinationExist() ? true : $this->createDestination();
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

    public function file($url)
    {
        try {
            $this->url = $url;
            $validator = new FileValidator();
            $validator->validateUrl($this);
            $this->fileOpen();
            $this->setFileData();
        } catch (Exception $e) {
            (!$e->getMessage()) || $this->set_error($e->getMessage());
        }
    }

    public function upload()
    {
        if ($this->getErrors()) {
            return $this->file;
        }
        try {
            $this->validate();
            if ($this->getErrors()) {
                throw new Exception();
            }

            $this->file['filename']  = $this->generateFilename();
            $this->file['full_path'] = $this->root . $this->destination . $this->file['filename'];
            $this->file['path']      = $this->destination . $this->file['filename'];

            $status = rename($this->file['tmp_name'], $this->file['full_path']);
            if (!$status) {
                $this->set_error("Upload: Can't upload file.");
            } else {
                $this->file['status'] = true;
            }

            if ($this->getErrors()) {
                throw new Exception();
            }
        } catch (Exception $e) {
            if (is_file($this->file['tmp_name']) && is_executable($this->file['tmp_name'])) {
                unlink($this->file['tmp_name']);
            }
        }
        return $this->file;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function set_error($message)
    {
        $this->errors[] = $message;
    }

    public function setValidator(array $callback)
    {
        if (is_callable($callback)) {
            $this->validators[] = $callback;
        }
    }

    protected function validate()
    {
        foreach ($this->validators as $validator) {
            call_user_func($validator, $this);
        }
    }

    protected function setFileData()
    {
        $this->file['mime']          = $this->getFileMime();
        $this->file['status']        = false;
        $this->file['destination']   = $this->destination;
        $this->file['size_in_bytes'] = $this->getFileSize();
    }

    protected function getFileSize()
    {
        return filesize($this->file['tmp_name']);
    }

    protected function getFileMime()
    {
        return image_type_to_mime_type(exif_imagetype($this->file['tmp_name']));
    }

    protected function fileOpen()
    {
        if ($this->getErrors()) {
            throw new Exception();
        }
        $file = @fopen($this->url, 'r', false, $this->ctx);
        if (!$file) {
            throw new Exception("Can't open file from url");
        }
        $content = stream_get_contents($file, FileValidator::MAX_FILESIZE + 1);
        $headers = self::httpParseHeaders($http_response_header);
        if (isset($headers['content-encoding'])) {
            $content = $this->contentEncode($content, $headers['content-encoding']);
        }
        fclose($file);
        $this->file['tmp_name'] = tempnam(sys_get_temp_dir(), $this->generateFilename());
        return file_put_contents($this->file['tmp_name'], $content);
    }

    protected function setCtx()
    {
        $header = [
            'Accept-Language: en-US,en;q=0.8',
            'Accept-Charset:UTF-8,*;q=0.5',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) ' .
            'Ubuntu Chromium/36.0.1985.125 ' .
            'Chrome/36.0.1985.125 Safari/537.36'
        ];
        $opts   = [
            'http' => [
                'timeout' => 15,
                'header' => implode("\r\n", $header),
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false
            ]
        ];

        $this->ctx = stream_context_create($opts);
    }

    private function isDestinationExist()
    {
        return is_writable($this->root . $this->destination);
    }

    private function createDestination()
    {
        return mkdir($this->root . $this->destination, 755, true);
    }

    private static function httpParseHeaders(array $header)
    {
        $parse = [];
        foreach ($header as $line) {
            if (strpos($line, ': ') !== false) {
                list($name, $value) = explode(': ', $line, 2);
                $parse[strtolower($name)] = trim($value);
            } else {
                $parse[] = trim($line);
            }
        }
        ksort($parse, SORT_NATURAL);
        return $parse;
    }

    private function generateFilename()
    {
        return sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time();
    }

    private function contentEncode($content, $type)
    {
        switch (trim($type)) {
            case 'gzip' :
                return gzdecode($content);
            case 'deflate':
                return gzinflate($content);
            case 'compress':
                return gzuncompress($content);
            case 'identity':
                return $content;
        }
    }
}