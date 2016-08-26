<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Models\Unit;
use Slim\Http\Request;
use Slim\Http\Response;

class UnitController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $units = Unit::with(['images', 'tags']);

        return $response->withJson($units->get()->toArray(), self::STATUS_OK);
    }

    public function actionView(Request $request, Response $response)
    {
        $unit = Unit::with(['images', 'tags'])->findOrFail($request->getAttribute('id'));

        return $response->withJson($unit->toArray(), self::STATUS_OK);
    }

    public function actionCreate(Request $request, Response $response)
    {
        $unit = new Unit($request->getParams());

        $unit->saveOrFail();

        return $response->withJson($unit->load(['images', 'tags'])->toArray(), self::STATUS_CREATED);
    }

    public function actionUpdate(Request $request, Response $response)
    {
        $unit = Unit::findOrFail($request->getAttribute('id'));

        $unit->fill($request->getParams());
        $unit->saveOrFail();;

        return $response->withJson($unit->load(['tags', 'images'])->toArray(), self::STATUS_OK);
    }

    public function actionDelete(Request $request, Response $response)
    {
        $unit = Unit::findOrFail($request->getAttribute('id'));
        $unit->delete();

        return $response->withJson($unit->toArray(), self::STATUS_OK);
    }
}