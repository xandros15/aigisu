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
use Slim\Views\Twig;

class UnitController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response): Response
    {
        return $this->get(Twig::class)->render($response, 'unit/index.twig');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $unit = $this->callApi('api.unit.view', $request, $response)->getResponse();
        return $this->get(Twig::class)->render($response, 'unit/view.twig', ['unit' => $unit]);
    }

}
