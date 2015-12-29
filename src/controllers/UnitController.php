<?php

namespace controller;

use Main;
use models\Unit;
use models\UnitSearch;
use app\core\Controller;
use Slim\Http\Request;
use app\alert\Alert;
use controller\OauthController as Oauth;

class UnitController extends Controller
{

    public function actionIndex(Request $request)
    {
        $model = new UnitSearch();

        return $this->render('unit/index',
                ['model' => $model->search($request->getParams()), 'maxPages' => $model->maxPages]);
    }

    public function actionCreate(Request $request)
    {
        if (!Oauth::isLogged()) {
            return $this->goBack();
        }

        $model = new Unit($request->getParams());

        if ($request->isXhr()) {
            return $this->renderAjax('unit/ajax/modal', ['model' => $model]);
        }

        if ($model->validate() && $model->save()) {
            Alert::add('Successful added ' . $model->name);
        }
        return $this->goBack();
    }

    public function actionUpdate(Request $request)
    {
        if (!Oauth::isLogged()) {
            return $this->goBack();
        }

        /* @var $model Unit */
        $model = Unit::find($request->getAttribute('id'));

        if ($request->isXhr()) {
            return $this->renderAjax('unit/ajax/modal', ['model' => $model]);
        }

        $model->addTagsToUnit($request->getParam('tags'));

        $model->fill($request->getParams());

        if ($model->validate() && $model->save()) {
            Alert::add("Successful update {$model->name}");
        }

        return $this->goBack();
    }

    public function actionDelete(Request $request)
    {
        if (!Oauth::isLogged()) {
            return $this->goBack();
        }

        /* @var $model Unit */
        $model = Unit::find($request->getAttribute('id'));

        if ($model->delete()) {
            Alert::add("Successful delete {$model->name}");
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