<?php

namespace controller;

use models\Units;
use app\core\Controller;
use Slim\Http\Response;
use Slim\Http\Request;

class UnitsController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $model = Units::load();
        $units = $model->getUnits();
        $response->write($this->render('unit/units', ['model' => $model, 'units' => $units]));
        return $response;
    }
}