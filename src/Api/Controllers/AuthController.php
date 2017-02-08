<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 00:11
 */

namespace Aigisu\Api\Controllers;


use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionCreate(Request $request, Response $response) : Response
    {
        throw new NotFoundException($request, $response);
    }
}
