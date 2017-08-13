<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:32
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Form;
use Aigisu\Models\Unit;
use Aigisu\Web\Components\MultipartStream;
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
        $units = $this->api->request('/units?expand=missing_cg,cg')->getArrayBody();

        $manager = new UnitManager($request->getQueryParams());
        $units = $manager->filter($units);
        $units = $manager->sort($units);

        $request = $request->withQueryParams($manager->getQuery());

        return $this->get(Twig::class)->render($response, 'unit/index.twig', [
            'units' => $units,
            'form' => new Form($request),
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
        $id = $request->getAttribute('id');
        $unit = $this->api->request('/units/' . $id . '?expand=cg')->getArrayBody();

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
        $id = $request->getAttribute('unitId');;
        $cgs = $this->api->request('/units/' . $id . '/cg')->getArrayBody();

        $map = [];
        foreach ($cgs as $cg) {
            $map[$cg['server']][] = $cg;
        }

        return $this->get(Twig::class)->render($response, 'unit/bedroom.twig', ['cgs' => $map]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        if ($request->isPost()) {
            $api = $this->api->request('/units', 'POST', new MultipartStream($request));
            if ($api->hasError()) {
                $request = $request->withAttribute('errors', $api->getErrors());
            } else {
                $this->flash->addSuccess('Successful created unit.');

                return $this->redirect($response, 'web.unit.view', [
                    'arguments' => ['id' => $api->getArrayBody()['id']],
                ]);
            }
        }

        $form = new Form($request);

        return $this->get(Twig::class)->render($response, 'unit/create.twig', [
            'form' => $form,
            'rarities' => Unit::getRarities(),
            'genders' => Unit::getGenders(),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        if ($request->isPost()) {
            $api = $this->api->request('/units/' . $id, 'POST', new MultipartStream($request));
            if ($api->hasError()) {
                $request = $request->withAttribute('errors', $api->getErrors());
            } else {
                $this->flash->addSuccess('Successful updated unit.');

                return $this->redirect($response, 'web.unit.view', [
                    'arguments' => ['id' => $api->getArrayBody()['id']],
                ]);
            }
        }

        $responseApi = $this->api->request('/units/' . $id)->getArrayBody();
        $responseApi['link_seesaw'] = $responseApi['links']['seesaw'];
        $responseApi['link_gc'] = $responseApi['links']['gc'];

        $form = new Form($request->withQueryParams(array_merge($responseApi, $request->getParams())));

        return $this->get(Twig::class)->render($response, 'unit/create.twig', [
            'form' => $form,
            'rarities' => Unit::getRarities(),
            'genders' => Unit::getGenders(),
        ]);
    }
}
