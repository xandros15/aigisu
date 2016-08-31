<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 13:38
 */

namespace Aigisu\Components\File;


use Slim\Http\UploadedFile;

class FileUploader
{
    /** @var string */
    private $targetDir;

    /**
     * FileUploader constructor.
     * @param string $targetDir
     */
    public function __construct(string $targetDir)
    {
        $this->targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);
    }

    /**
     * @param UploadedFile $file
     * @param string $filename
     * @return UploadedFile
     */
    public function upload(UploadedFile $file, string $filename)
    {
        $filename = trim($filename, DIRECTORY_SEPARATOR);
        $file->moveTo($this->targetDir . DIRECTORY_SEPARATOR . $filename);
        return $file;
    }
}