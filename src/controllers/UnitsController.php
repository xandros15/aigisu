<?php

namespace controller;

use models\Units;
use app\core\Controller;
use Slim\Http\Response;
use Slim\Http\Request;
use RedBeanPHP\R;
use app\alert\Alert;

class UnitsController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $model = Units::load();
        $units = $model->getUnits();
        $response->write($this->render('unit/units', ['model' => $model, 'units' => $units]));
        return $response;
    }

    public function actionUpdate(Request $request, Response $response)
    {

        $unitPost = (object) $request->getParam('unit');
        $id       = (int) $request->getAttribute('id');
        if (!($errors   = Units::validate($unitPost))) {
            R::freeze();
            $unit = R::load(Units::tableName(), $id);
            if (!$unit->isEmpty()) {
                $unit->name      = $unitPost->name;
                $unit->rarity    = $unitPost->rarity;
                $unit->isOnlyDmm = (bool) isset($unitPost->isOnlyDmm) && $unitPost->isOnlyDmm;
                $unit->isMale    = (bool) isset($unitPost->isMale) && $unitPost->isMale;
                R::store($unit);
                Alert::add("Unit {$unit->original} successful updated.");
            } else {
                Alert::add("Unit not found", Alert::ERROR);
            }
        } else {
            foreach ($errors as $error) {
                Alert::add($error, Alert::ERROR);
            }
        }
        return $response->withRedirect('/');
    }
}