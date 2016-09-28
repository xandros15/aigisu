<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 23:31
 */

namespace Aigisu\Api\Controllers\Unit;


use Aigisu\Api\Controllers\Controller;
use Slim\Http\Request;
use Slim\Http\Response;

class ExtendedUploader extends Controller
{
    const
        SERVER_GOOGLE = 'google',
        SERVER_IMGUR = 'imgur';

    public function actionUpload(Request $request, Response $response) : Response
    {
        switch ($request->getAttribute('server')) {
            case self::SERVER_GOOGLE:
            case self::SERVER_IMGUR:
        }

        return $response->withStatus(self::STATUS_BAD_REQUEST);
    }
}