<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-12
 * Time: 02:11
 */

namespace Aigisu\Components\Http;


use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Aigisu\Core\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile as SlimUploadedFile;

class UploadedFilesMiddleware extends Middleware
{
    const
        METHOD_POST = 'POST',
        METHOD_PUT = 'PUT';

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        switch (strtoupper($request->getMethod())) {
            case self::METHOD_PUT:
                $request = $this->withPutFiles($request);
                break;
            case self::METHOD_POST:
                $request = $this->withPostFiles($request);
                break;
        }

        return $next($request, $response);
    }


    /**
     * @param Request $request
     * @return Request
     */
    private function withPutFiles(Request $request) :Request
    {
        $tempFilename = tempnam(sys_get_temp_dir(), 'php-upload');
        register_shutdown_function(function () use ($tempFilename) {
            @unlink($tempFilename);
        });


        $basename = basename($tempFilename);
        $content = (string) $request->getBody();
        $filesize = mb_strlen($content);
        $maxFilesize = ini_get('upload_max_filesize');

        if ($filesize > $maxFilesize && $maxFilesize > 0) {
            //Too large file
            $file = new UploadedFile($tempFilename, $basename, null, $filesize, UPLOAD_ERR_INI_SIZE);
        } elseif ($filesize && file_put_contents($tempFilename, $content)) {
            //Correct file
            $mimeType = (new \finfo())->file($tempFilename, FILEINFO_MIME_TYPE);
            $file = new UploadedFile($tempFilename, $basename, $mimeType, $filesize);
            $file->setManager($this->get(FilesystemManager::class));
        } else {
            //File no exists
            $file = new UploadedFile($tempFilename, $basename, null, $filesize, UPLOAD_ERR_NO_FILE);
        }

        return $request->withUploadedFiles([
            $file
        ]);
    }

    /**
     * @param Request $request
     * @return Request
     */
    private function withPostFiles(Request $request) : Request
    {
        $oldFiles = $request->getUploadedFiles(); //$_FILES works only in post
        $newFiles = [];
        $manager = $this->get(FilesystemManager::class);

        foreach ($oldFiles as $name => $file) {
            /** @var $file SlimUploadedFile */
            $newFile = new UploadedFile(
                $file->file,
                $file->getClientFilename(),
                $file->getClientMediaType(),
                $file->getSize(),
                $file->getError()
            );
            $newFile->setManager($manager);
            $newFiles[$name] = $newFile;
        }

        return $request->withUploadedFiles($newFiles);
    }
}