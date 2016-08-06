<?php

namespace Controllers;

use Aigisu\Alert\Alert;
use Aigisu\Controller;
use Models\Unit;
use Models\UnitSort;
use Slim\Http\Request;
use Xandros15\SlimPagination\Pagination;

class UnitController extends Controller
{

    public function actionIndex(Request $request)
    {
        $unitSort   = new UnitSort($request, $this->router);
        $unitSearch = Unit::with(['images', 'tags']);
        $max        = $unitSearch->count();
        $unitSearch->forPage($request->getParam('page', 1), Unit::UNITS_PER_PAGE);
        foreach ($unitSort->getOrders() as $order => $direction) {
            $unitSearch->orderBy($order, $direction);
        }
        $list = $unitSearch->get();

        return $this->render('unit/index', [
            'unitList'   => $list,
            'pagination' => new Pagination($request, $this->router, [
                Pagination::OPT_TOTAL    => $max,
                Pagination::OPT_PER_PAGE => Unit::UNITS_PER_PAGE
            ]),
            'unitSort'   => $unitSort
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
        /* @var $model Unit */
        $model = Unit::find($request->getAttribute('id'));

        if ($model->delete()) {
            Alert::add("Successful delete {$model->name}");
        }

        return $this->goBack();
    }
}