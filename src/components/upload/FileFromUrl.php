<?php

namespace Aigisu\Components\Upload;

use Exception;
use InvalidArgumentException;
use RuntimeException;

class FileFromUrl extends Upload
{
    const MAX_FILE_SIZE = 2 * 1024 * 1024 + 1;
    public $url;
    protected $ctx;

    public function __construct()
    {
        $this->setCtx();
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
        $opts = [
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

    public function upload($name)
    {
        $name = sprintf('%s%s.%s', $this->destination, $name, $this->extension);

        if (!rename($this->filename, $name)) {
            throw new RuntimeException("Can't move file from: '{$this->filename}' to: '{$name}'");
        }

        $this->oldFilename = $this->filename;
        $this->filename    = $name;

        return $this->filename;
    }

    public function setFile($fileOrUrl)
    {
        try {
            if (!$this->validateUrl($fileOrUrl)) {
                throw new InvalidArgumentException("'{$fileOrUrl}' isn't url adress");
            }
            $this->url = $fileOrUrl;
            $this->setFileFromUrl();
            $this->setFileMime();
            $this->setFileSize();
            $this->setFileMd5();
            $this->setFileResolution();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }

    private function validateUrl($url)
    {
        $urlRegex = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})'
            . '(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3})'
            . '{2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])'
            . '(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))'
            . '|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)'
            . '*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
        return (preg_match($urlRegex, $url));
    }

    protected function setFileFromUrl()
    {
        $file = @fopen($this->url, 'r', false, $this->ctx);
        if (!$file) {
            throw new RuntimeException("Can't open file from url: '{$this->url}'");
        }
        $content = stream_get_contents($file, self::MAX_FILE_SIZE);
        $headers = $this->httpParseHeaders($http_response_header);
        if (isset($headers['content-encoding'])) {
            $content = $this->contentEncode($content, $headers['content-encoding']);
        }
        fclose($file);
        $filename = tempnam(sys_get_temp_dir(), $this->generateFilename());
        if (!file_put_contents($filename, $content)) {
            throw new RuntimeException("Uploaded file is empty");
        }
        return $this->filename = $filename;
    }

    private function httpParseHeaders(array $header)
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
            default:
                throw new InvalidArgumentException("Wrong type of content to encode. `{$type}` given.");
        }
    }
}