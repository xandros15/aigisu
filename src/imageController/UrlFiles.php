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
use app\validators\FileValidator;

class UrlFiles
{
    const REGEX_URL  = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
    const MAX_WIDTH  = 961;
    const MAX_HEIGHT = 641;

    public $file      = [];
    public $filename;
    public $mimeTypes = [];
    private $url;
    private $fileResource;
    private $headers;
    private $ctx;
    private $errors   = [];
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
            $this->setHeaders($this->url);
            $this->beforeValidate();
            $this->saveFile();
        } catch (Exception $exc) {
            if (isset($this->file['full_path']) && is_writable($this->file['full_path'])) {
                unlink($this->file['full_path']);
            }
            $this->errors[] = ['address' => $url, 'message' => $exc->getMessage()];
        }
    }

    public function setMimeTypes(array $mimeTypes)
    {
        $this->mimeTypes = array_merge($this->mimeTypes, $mimeTypes);
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
        $this->destination = $destination . DIRECTORY_SEPARATOR;

        return $this->isDestinationExist() ? true : $this->createDestination();
    }

    public function setHeaders($url)
    {
        $this->headers = self::httpParseHeaders(get_headers($url));
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
        $file                    = @fopen($this->url, 'r', false, $this->ctx);
        if (!$file) {
            throw new Exception('Can\'t open file from url');
        }
        $content = stream_get_contents($file);
        if (isset($this->headers['content-encoding'])) {
            $content = $this->contentEncode($content, $this->headers['content-encoding']);
        }
        fclose($file);
        $status = file_put_contents($this->file['full_path'], $content);
        if (!$status) {
            throw new Exception('Upload: Can\'t upload file.');
        }
        $this->validate();

        //done
        $this->file['status'] = true;
    }

    private function validate()
    {
        $validator = new FileValidator();
        $validator->checkResolution($this->file['full_path']);
    }

    private function beforeValidate()
    {
        $validator = new FileValidator();
        $validator->checkHttpResponse($this->headers);
        $validator->checkMimeType($this->headers['content-type'], $this->mimeTypes);
        $validator->checkFileSize($this->headers['content-length']);
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
                $option                        = explode(': ', $line, 2);
                $parse[strtolower($option[0])] = trim($option[1]);
            } else {
                $parse[] = trim($line);
            }
        }
        ksort($parse, SORT_NATURAL);
        return $parse;
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