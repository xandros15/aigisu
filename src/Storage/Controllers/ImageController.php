<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 18:15
 */

namespace Aigisu\Storage\Controllers;


use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Util;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

class ImageController extends AbstractController
{
    const CACHE_LIFETIME = 60 * 60 * 24; // sec * min * h = day

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionView(Request $request, Response $response) : Response
    {
        try {
            $file = $this->getImage($request->getAttribute('path'));
            $mime = $file->getMimetype();
            $body = new Stream($file->readStream());
        } catch (FileNotFoundException $e) {
            throw new NotFoundException($request, $response);
        }

        return $response->withBody($body)
            ->withHeader('Content-Type', $mime)
            ->withHeader('Cache-Control', 'max-age=' . self::CACHE_LIFETIME . ', public')
            ->withHeader('Etag', md5($body));
    }

    /**
     * @param string $path
     * @return \League\Flysystem\Directory|\League\Flysystem\File|\League\Flysystem\Handler
     * @throws FileNotFoundException
     */
    protected function getImage(string $path)
    {
        /** @var $filesystem Filesystem */
        $filesystem = $this->get(Filesystem::class);
        $file = $filesystem->get($path);
        if (!$file->isFile() || !$this->isImage($file->getMimetype())) {
            throw new FileNotFoundException($path);
        }

        return $file;
    }

    /**
     * @param string $mimeType
     * @return bool
     */
    protected function isImage(string $mimeType) : bool
    {
        return 0 === strpos($mimeType, 'image/');
    }
}
