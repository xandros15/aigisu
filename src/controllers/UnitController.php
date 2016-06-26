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
            'pagination' => $pagination,
            'sort' => $this->unitSort($request)
        ]);
    }

    private function unitSort(Request $request) : string
    {
        $sort = $request->getQueryParam('sort', '');
        $query = $request->getQueryParams();
        $route = $request->getAttribute('route')->getName();
        $attributes = $request->getAttributes();
        $sortable = [
            'name' => '',
            'original' => '',
            'rarity' => ''
        ];

        foreach ($sortable as $name => &$value) {
            $newQuery = ['sort' => ($name == $sort) ? '-' . $name : $name];
            $mergedQuery = ($query) ? array_merge($query, $newQuery) : $newQuery;
            $value = $this->router->pathFor($route, $attributes, $mergedQuery);
        }

        return $this->view->render('unit/sort', ['sort' => $sortable]);
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
}