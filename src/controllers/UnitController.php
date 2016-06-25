<?php

namespace controller;

use app\alert\Alert;
use app\core\Controller;
use controller\OauthController as Oauth;
use models\Unit;
use models\UnitSearch;
use Slim\Http\Request;
use Xandros15\SlimPagination\Pagination;

class UnitController extends Controller
{

    public function actionIndex(Request $request)
    {
        $model = new UnitSearch();
        $search = $model->search($request->getParams());
        $pagination = new Pagination($request, $this->router, [
            Pagination::OPT_TOTAL => $model->count,
            Pagination::OPT_PER_PAGE => UnitSearch::UNITS_PER_PAGE
        ]);

        $pagination = $this->view->render('unit/pagination', [
            'pagination' => $pagination
        ]);
        return $this->render('unit/index', [
            'unitList' => $search->get(),
            'pagination' => $pagination
        ]);
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

    public static function generateLink(array $options)
    {

//        $request = $this->request;
//        $request->
//        $request = Main::$app->request;
//        $query   = $request->getParams();
//
//        if (isset($options['sort']) && ($options['sort'] === $request->getParam('sort', ''))) {
//            $options['sort'] = '-' . $options['sort'];
//        }

        return ''; // '?' . http_build_query(array_merge($query, $options));
    }
}