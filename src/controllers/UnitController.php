<?php

namespace controller;

use Main;
use models\Unit;
use models\UnitSearch;
use app\core\Controller;
use Slim\Http\Request;
use app\alert\Alert;

class UnitController extends Controller
{

    public function actionIndex(Request $request)
    {
        $model = new UnitSearch();

        return $this->render('unit/index',
                ['model' => $model->search($request->getParams()), 'maxPages' => $model->maxPages]);
    }

    public function actionUpdate(Request $request)
    {
        /* @var $model Unit */
        $model = Unit::find($request->getAttribute('id'));
        $model->fill($request->getParams());

        if ($model->validate()) {
            $model->save();
            Alert::add("Successful update {$model->name}");
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

    public static function getPage()
    {
        return Main::$app->request->getParam('page', 1);
    }
}