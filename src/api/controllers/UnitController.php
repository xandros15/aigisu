<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Models\Unit;
use Slim\Http\Request;
use Slim\Http\Response;

class UnitController extends Controller
{
    const VIEW_ROUTE_NAME = 'api.unit.view';
    const INDEX_ROUTE_NAME = 'api.unit.index';
    const CREATE_ROUTE_NAME = 'api.unit.create';
    const UPDATE_ROUTE_NAME = 'api.unit.update';
    const DELETE_ROUTE_NAME = 'api.unit.delete';

    public function actionIndex(Request $request, Response $response)
    {
        $units = Unit::with($this->getExtendedParam($request))->get();

        return $response->withJson($units->toArray(), self::STATUS_OK);
    }

    public function actionView(Request $request, Response $response)
    {
        $unit = Unit::with($this->getExtendedParam($request))->findOrFail($this->getID($request));

        return $response->withJson($unit->toArray(), self::STATUS_OK);
    }

    public function actionCreate(Request $request, Response $response)
    {
        $unit = new Unit($request->getParams());

        $unit->saveOrFail();

        return $this->created($response, $this->router->pathFor(self::VIEW_ROUTE_NAME, ['id' => $unit->getKey()]));
    }

    public function actionUpdate(Request $request, Response $response)
    {
        $unit = Unit::findOrFail($this->getID($request));

        $unit->fill($request->getParams());
        $unit->saveOrFail();

        return $response->withStatus(self::STATUS_OK);
    }

    public function actionDelete(Request $request, Response $response)
    {
        $unit = Unit::findOrFail($this->getID($request));
        $unit->delete();

        return $response->withStatus(self::STATUS_OK);
    }
}