<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-01
 * Time: 19:14
 */

namespace Aigisu\Common\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response): Response
    {
        return $this->render($response, 'home/index');
    }
}