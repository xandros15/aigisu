<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Models\Unit;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Slim\Http\Request;
use Slim\Http\Response;

class UnitController extends Controller
{

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        $units = Unit::with($this->getExtendedParam($request))->get();

        return $response->withJson($units->toArray(), self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $unit = Unit::with($this->getExtendedParam($request))->findOrFail($this->getID($request));

        return $response->withJson($unit->toArray(), self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $unit = new Unit($request->getParams());
        $unit->uploadIcon($request, $this->get(FilesystemManager::class));
        $unit->saveOrFail();
        $unit->syncTags();

        return $this->created($response, $this->router->pathFor('unit.view', ['id' => $unit->getKey()]));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        /** @var $unit Unit */
        $unit = Unit::findOrFail($this->getID($request))->fill($request->getParams());
        $unit->uploadIcon($request, $this->get(FilesystemManager::class));
        $unit->saveOrFail();
        $unit->syncTags();

        return $response->withStatus(self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        Unit::findOrFail($this->getID($request))->delete();

        return $response->withStatus(self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionRarities(Request $request, Response $response): Response
    {
        return $response->withJson(Unit::getRarities(), self::STATUS_OK);
    }
}