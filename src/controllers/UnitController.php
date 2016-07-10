<?php

namespace Controllers;

use Aigisu\Alert\Alert;
use Aigisu\Controller;
use Controllers\OauthController as Oauth;
use models\Unit;
use models\UnitSearch;
use Models\UnitSort;
use Slim\Http\Request;
use Xandros15\SlimPagination\Pagination;

class UnitController extends Controller
{

    public function actionIndex(Request $request)
    {
        $model = new UnitSearch();
        $unitSort = new UnitSort($request, $this->router);
        $search = $model->search([
            'search' => $request->getParam(Unit::SEARCH_PARAM),
            'page' => $request->getParam('page', 1),
            'order' => $unitSort->getOrders()
        ]);


        return $this->render('unit/index', [
            'unitList' => $search->get(),
            'pagination' => new Pagination($request, $this->router, [
                Pagination::OPT_TOTAL => $model->count,
                Pagination::OPT_PER_PAGE => UnitSearch::UNITS_PER_PAGE
            ]),
            'unitSort' => $unitSort
        ]);
    }

    public function actionView(Request $request)
    {
        $unit = Unit::firstOrNew(['id' => $request->getAttribute('id')]);

        return $this->render('unit/view', ['unit' => $unit]);
    }

    public function actionCreate(Request $request)
    {
        $model = new Unit($request->getParams());

        if ($model->validate() && $model->save()) {
            Alert::add('Successful added ' . $model->name);
        }
        return $this->render('unit/unit');
    }

    public function actionUpdate(Request $request)
    {
        if (!Oauth::isLogged()) {
            return $this->goBack();
        }
        /* @var $model Unit */
        $model = Unit::find($request->getAttribute('id'));

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