<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:32
 */

namespace Aigisu\Web\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;

class UnitController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        return $this->render($request, $response, 'unit/index.twig');
    }
}
