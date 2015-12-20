<?php

namespace controller;

use Main;
use models\Unit;
use models\UnitSearch;
use app\core\Controller;
use Slim\Http\Request;
use RedBeanPHP\R;
use app\alert\Alert;
use Illuminate\Database\Eloquent\Collection;

class UnitController extends Controller
{

    public function actionIndex(Request $request)
    {

        $model   = new UnitSearch();
        /* @var $results Collection */

        return $this->render('unit/index',
                [
                'model' => $model->search($request->getParams()),
                'units' => null,
                'maxPages' => $model->maxPages
        ]);
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

    public static function getSearchQuery()
    {
        return Main::$app->request->getParam('q', '');
    }

    public static function generateLink(array $options)
    {
        $request = Main::$app->request;
        $query   = $request->getParams();

        if (isset($options['sort']) && ($options['sort'] === $request->getParam('sort', ''))) {
            $options['sort'] = '-' . $options['sort'];
        }

        return '?' . http_build_query(array_merge($query, $options));
    }
}