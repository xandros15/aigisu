<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:32
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Form;
use Aigisu\Web\Components\UnitManager;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class UnitController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionIndex(Request $request, Response $response): Response
    {
        $apiRequest = $request->withQueryParams(array_merge($request->getQueryParams(), [
            'expand' => 'missing_cg,cg',
        ]));

        $units = $this->callApi('api.unit.index', $apiRequest, $response)->getJson();

        $manager = new UnitManager($request->getQueryParams());
        $units   = $manager->filter($units);
        $units   = $manager->sort($units);

        $request = $request->withQueryParams($manager->getQuery());

        return $this->get(Twig::class)->render($response, 'unit/index.twig', [
            'units' => $units,
            'form'  => new Form($request),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $unit = $this->callApi('api.unit.view', $request, $response)->getJson();

        return $this->get(Twig::class)->render($response, 'unit/view.twig', ['unit' => $unit]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionBedroom(Request $request, Response $response): Response
    {
        $cgs = $this->callApi('api.unit.cg.index', $request, $response)->getJson();

        $map = [];
        foreach ($cgs as $cg) {
            $map[$cg['server']][] = $cg;
        }

        return $this->get(Twig::class)->render($response, 'unit/bedroom.twig', ['cgs' => $map]);
    }

}
