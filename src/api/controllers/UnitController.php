<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Models\Unit;
use Aigisu\Common\Models\UnitSort;
use Slim\Http\Request;
use Slim\Http\Response;

class UnitController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $unitSort = new UnitSort($request, $this->router);
        $unitSearch = Unit::with(['images', 'tags']);
        $max = $unitSearch->count();
        $unitSearch->forPage($request->getParam('page', 1), Unit::UNITS_PER_PAGE);
        foreach ($unitSort->getOrders() as $order => $direction) {
            $unitSearch->orderBy($order, $direction);
        }
        $list = $unitSearch->get();

        return $response;
    }

    public function actionView(Request $request, Response $response)
    {
        $unit = Unit::firstOrNew(['id' => $request->getAttribute('id')]);

        return $response;
    }

    public function actionCreate(Request $request, Response $response)
    {
        $unit = new Unit($request->getParams());
        if ($request->isPost()) {
            if ($unit->validate() && $unit->save()) {
                return $response;
            }
        }

        return $response;
    }

    public function actionUpdate(Request $request, Response $response)
    {
        /* @var $unit Unit */
        $unit = Unit::find($request->getAttribute('id'));

        if ($request->isPost()) {
            $unit->addTagsToUnit($request->getParam('tags'));
            if ($unit->fill($request->getParams())->validate() && $unit->save()) {
                return $response;
            }
        }

        return $response;
    }

    public function actionDelete(Request $request, Response $response)
    {
        /* @var $model Unit */
        $model = Unit::find($request->getAttribute('id'));

        return $response;
    }

    public function actionShowImages(Request $request, Response $response)
    {
        $unit = Unit::find($request->getAttribute('id'));
        return $response;
    }
}