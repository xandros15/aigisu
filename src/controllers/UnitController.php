<?php

namespace controller;

use models\Unit;
use app\core\Controller;
use Slim\Http\Request;
use RedBeanPHP\R;
use app\alert\Alert;

class UnitController extends Controller
{

    public function actionIndex()
    {
        $model = Unit::load();
        $units = $model->getUnits();

        return $this->render('unit/index', ['model' => $model, 'units' => $units]);
    }

    public function actionUpdate(Request $request)
    {

        $unitPost = (object) $request->getParam('unit');
        $id       = (int) $request->getAttribute('id');
        if (!($errors   = Unit::validate($unitPost))) {
            R::freeze();
            $unit = R::load(Unit::tableName(), $id);
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

        return $this->goBack();
    }
}