<?php

namespace app;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UrlFiles
 *
 * @author user
 */
use Exception;

class UrlFiles
{
    const REGEX_ERROR = '~^HTTP/[12]\.[0-9] [54][0-9]{2}.*$~i';
    const REGEX_URL   = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
    const MAX_WIDTH   = 961;
    const MAX_HEIGHT  = 641;

    public $file = [];
    public $tempFilePath;
    public $filename;
    public $mimeTypes;
    private $url;
    private $fileResource;
    private $headers;
    private $ctx;
    private $errors = [];
    private $root;
    private $destination;

    public function __construct($destination, $root = false)
    {

        if ($root) {
            $this->root = $root;
        } else {
            $this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
        }
        // set & create destination path
        if (!$this->setDestination($destination)) {
            throw new Exception('Upload: Can\'t create destination. ' . $this->root . $this->destination);
        }
        $this->setCtx();
    }

    public function loadFile($url, $opts = false)
    {
        $ctx = ($opts !== false) ? stream_context_create($opts) : $this->ctx;
        try {
            $this->setUrl($url);
            $this->fileResource = @fopen($this->url, 'r', false, $ctx);
            if (empty($this->fileResource)) {
                throw new Exception('Can\'t download file');
            }
            if (empty($http_response_header)) {
                throw new Exception('Target server no response');
            }
            $this->headers = self::httpParseHeaders($http_response_header);
            $this->validate();
            $this->saveFile();
        } catch (Exception $exc) {
            if (is_resource($this->fileResource)) {
                fclose($this->fileResource);
            }
            $this->errors[] = ['address' => $url, 'message' => $exc->getMessage()];
        }
    }

    public function setMimeTypes($mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setDestination($destination)
    {
        $this->destinatio = $destination . DIRECTORY_SEPARATOR;

        return $this->isDestinationExist() ? true : $this->createDestination();
    }

    public function setCtx($login = [])
    {
        $opts = [
            'http' =>
            [
                'timeout' => 15,
                'header' => implode("\r\n",
                    [
                    'Accept-Language: en-US,en;q=0.8',
                    'Accept-Charset:UTF-8,*;q=0.5',
                    'User-Agent: Mozilla/5.0 (X11; Linux x86_64) ' .
                    'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                    'Ubuntu Chromium/36.0.1985.125 ' .
                    'Chrome/36.0.1985.125 Safari/537.36'
                ]),
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false
            ]
        ];
        if ($login) {
            $auth                   = sprintf('%s:%s', $login['login'], $login['key']);
            $encodedAuth            = base64_encode($auth);
            $opts['http']['header'] = $encodedAuth . "\r\n" . $opts['http']['header'];
        }

        $this->ctx = stream_context_create($opts);
    }

    /**
     * Save file on server
     */
    protected function saveFile()
    {

        //create & set new filename
        if (empty($this->filename)) {
            $this->generateFilename();
        }

        //set filename
        $this->file['filename'] = $this->filename;

        //set full path
        $this->file['full_path'] = $this->root . $this->destination . $this->filename;
        $this->file['path']      = $this->destination . $this->filename;

        $status = copy($this->url,  $this->file['full_path'], $this->ctx);

        //checks whether upload successful
        if (!$status) {
            throw new Exception('Upload: Can\'t upload file.');
        }

        //done
        $this->file['status'] = true;
    }

    private function validate()
    {
        $this->checkResponse($this->headers);
        $this->checkMimeType($this->headers);
        $this->checkFileSize($this->fileResource);
    }

    private function isDestinationExist()
    {
        return is_writable($this->root . $this->destination);
    }

    private function createDestination()
    {
        return mkdir($this->root . $this->destination, 755, true);
    }

    private function checkMimeType(array $headers)
    {
        if (!isset($headers['content-type'])) {
            throw new Exception('Target server no response mimeType');
        }
        if ($this->mimeTypes && !in_array($headers['content-type'], $this->mimeTypes)) {
            throw new Exception('Wrong mime types');
        }
    }

    private function checkResponse(array $headers)
    {
        if (!$headers) {
            throw new Exception('Target server no response');
        }
        foreach ($headers as $key => $option) {
            if (is_array($option) || !is_int($key)) {
                continue;
            }
            if (preg_match(self::REGEX_ERROR, $option)) {
                throw new Exception('Target server no response');
            }
        }

        return true;
    }

    private static function httpParseHeaders(array $header)
    {
        $parse = [];
        foreach ($header as $line) {
            if (strpos($line, ': ') !== false) {
                $option                        = explode(': ', $line, 2);
                $parse[strtolower($option[0])] = trim($option[1]);
            } else {
                $parse[] = trim($line);
            }
        }
        ksort($parse, SORT_NATURAL);
        return $parse;
    }

    private function checkFileSize($file)
    {
        $stream = stream_get_contents($file, 1024 * 1024 * 1);
        list($width, $height) = getimagesize('data://application/octet-stream;base64,' . base64_encode($stream));
        if ($width > self::MAX_WIDTH) {
            throw new Exception("Wrong image width");
        }
        if ($height > self::MAX_HEIGHT) {
            var_dump($width, $height);
            throw new Exception("Wrong image height");
        }
    }

    private function generateFilename()
    {
        $this->filename = sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time();
    }

    public function setUrl($url)
    {
        if (!preg_match(self::REGEX_URL, $url)) {
            throw new Exception('This isn\'t url adress');
        }
        $this->url = $url;
    }
}