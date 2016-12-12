<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-14
 * Time: 21:07
 */

namespace Aigisu\Storage\Controllers;

use finfo;
use Slim\Exception\NotFoundException;
use Slim\Http\Body;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

class SpriteController extends Controller
{
    public function getIconsStylesheet(Request $request, Response $response)
    {
        $spriteCss = $this->get('sprite.icons') . '/sprite.css';
        if (!$file = @fopen($spriteCss, 'rb')) {
            throw new NotFoundException($request, $response);
        }

        return $response->withBody(new Stream($file))->withHeader('Content-type', 'text/css');
    }

    public function getIconsSprite(Request $request, Response $response)
    {
        $imageFileName = $this->get('sprite.icons') . '/sprite';
        if (!$image = @fopen($imageFileName, 'rb')) {
            throw new NotFoundException($request, $response);
        }

        $body = new Body($image);
        $mimeType = (new finfo())->buffer($body, FILEINFO_MIME_TYPE);

        return $response->withBody($body)
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Cache-Control', 'max-age=86400, public')
            ->withHeader('Etag', md5($body));
    }
}
