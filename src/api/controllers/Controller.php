<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 18:45
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Api\Messages;
use Aigisu\Core\ActiveContainer;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class Controller extends ActiveContainer implements Messages
{
    const EXTENDED = 'extended';
    const HEADER_LOCATION = 'Location';
    const INDEX = 'id';

    /**
     * @param Request $request
     * @return mixed
     */
    protected function getExtendedParam(Request $request)
    {
        return $request->getQueryParam(self::EXTENDED, []);
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getID(Request $request) : int
    {
        return $request->getAttribute(self::INDEX, 0);
    }

    /**
     * Creating response with header location
     *
     * @see http://www.restapitutorial.com/lessons/httpmethods.html
     * @param Response $response
     * @param string $path
     * @return Response
     */
    protected function created(Response $response, string $path): Response
    {
        return $response
            ->withStatus(self::STATUS_CREATED)
            ->withHeader(self::HEADER_LOCATION, $path);
    }
}