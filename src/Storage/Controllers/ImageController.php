<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 18:15
 */

namespace Aigisu\Storage\Controllers;


use finfo;
use League\Flysystem\Util;
use Slim\Exception\NotFoundException;
use Slim\Http\Body;
use Slim\Http\Request;
use Slim\Http\Response;

class ImageController extends AbstractController
{
    const CACHE_LIFETIME = 60 * 60 * 24; // sec * min * h = day

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response) : Response
    {
        return $this->getImage($request->getAttribute('path'), $request, $response);
    }

    /**
     * @param string $imageFileName
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    protected function getImage(string $imageFileName, Request $request, Response $response) : Response
    {
        try {
            $imageFileName = $this->get('public') . DIRECTORY_SEPARATOR . Util::normalizePath($imageFileName);
        } catch (\LogicException $e) {
            throw new NotFoundException($request, $response);
        }

        if (!$image = @fopen($imageFileName, 'rb')) {
            throw new NotFoundException($request, $response);
        }

        $body = new Body($image);
        $mimeType = (new finfo())->buffer($body, FILEINFO_MIME_TYPE);

        if (!$this->isImage($mimeType)) {
            throw new NotFoundException($request, $response);
        }

        return $response->withBody($body)
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Cache-Control', 'max-age=' . self::CACHE_LIFETIME . ', public')
            ->withHeader('Etag', md5($body));
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
