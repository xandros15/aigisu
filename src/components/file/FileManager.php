<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-03
 * Time: 15:01
 */

namespace Aigisu\Components\File;


use Slim\Http\Request;
use Slim\Http\Stream;
use Slim\Http\UploadedFile;

class FileManager
{
    /** @var UploadedFile[] */
    public $files = [];

    /**
     * UploadedFileManager constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if ($request->isPost() && $request->getUploadedFiles()) {
            $this->files = $request->getUploadedFiles();
        }
    }

    /**
     * @param Request $request
     * @param string $key
     * @return false|Stream
     */
    public static function getStream(Request $request, string $key)
    {
        $instance = new static($request);
        return $instance->stream($key);
    }

    /**
     * @param string $key
     * @return false|Stream
     */
    public function stream(string $key)
    {
        $stream = false;
        if (($file = $this->findFile($key)) && $file->getError() === UPLOAD_ERR_OK) {
            $stream = $file->getStream();
        }

        return $stream;
    }

    /**
     * @param string $key
     * @return null|UploadedFile
     */
    public function findFile(string $key)
    {

        $searchingFile = null;
        foreach ($this->files as $name => $file) {
            if ($name == $key) {
                $searchingFile = $file;
            } elseif (is_array($file)) {
                $searchingFile = $this->findFile($key);
            }

            if ($searchingFile) {
                break;
            }
        }

        return $searchingFile instanceof UploadedFile ? $searchingFile : null;
    }
}