<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-11
 * Time: 20:13
 */

namespace Aigisu\Components\Http;


use Slim\Http\UploadedFile;

class Base64UploadedFile extends UploadedFile
{
    public function __construct(array $params)
    {
        $file = $this->putBase64ToFile($params['value']);
        parent::__construct(
            $file,
            $params['name'],
            (new \finfo())->file($file, FILEINFO_MIME_TYPE),
            filesize($file)
        );
    }

    /**
     * @param string $base64
     * @return string filename
     */
    public function putBase64ToFile(string $base64) : string
    {
        $file = tempnam(sys_get_temp_dir(), 'upload');
        if (!file_put_contents($file, base64_decode($base64))) {
            throw new \RuntimeException('No contents uploaded');
        }

        register_shutdown_function(function () use ($file) {
            @unlink($file);
        });

        return $file;
    }
}