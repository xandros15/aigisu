<?php

namespace Aigisu\Common\Controllers;

use Aigisu\Api\Controllers\Controller as ApiController;
use Aigisu\Api\Messages;
use Aigisu\Api\Models\Unit;
use Aigisu\Common\Components\Alert\Alert;
use Aigisu\Common\Models\UnitSort;
use Illuminate\Database\Eloquent\Collection;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Xandros15\SlimPagination\Pagination;

class UnitController extends Controller
{

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        $path = $this->router->pathFor('api.unit.index');

        try {
            $clientResponse = $this->getClient()->get($path, [
                'query' => [ApiController::EXTENDED => ['images', 'tags']]
            ]);
            if ($clientResponse->getStatusCode() !== Messages::STATUS_OK) {
                throw new NotFoundException($request, $response);
            }

            $units = new Collection(\GuzzleHttp\json_decode($clientResponse->getBody(), true));
            $max = $units->count();

            $unitSort = new UnitSort($request, $this->router);
            foreach ($unitSort->getOrders() as $order => $direction) {
                $units = $units->sortBy($order, SORT_REGULAR, $direction === UnitSort::SORT_DESC);
            }
            $units = $units->forPage($request->getParam('page', 1), Unit::UNITS_PER_PAGE);

            return $this->render($response, 'unit/index', [
                'unitList' => $units,
                'pagination' => new Pagination($request, $this->router, [
                    Pagination::OPT_TOTAL => $max,
                    Pagination::OPT_PER_PAGE => Unit::UNITS_PER_PAGE
                ]),
                'unitSort' => $unitSort
            ]);
        } catch (\Exception $exception) {
            throw new NotFoundException($request, $response);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response) : Response
    {
        $unit = Unit::firstOrNew(['id' => $request->getAttribute('id')]);

        return $this->render($response, 'unit/view', ['unit' => $unit]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response) : Response
    {
        $unit = new Unit($request->getParams());
        if ($request->isPost()) {
            if ($unit->save()) {
                Alert::add('Successful added ' . $unit->name);
                return $this->goBack();
            }
        }

        return $this->render($response, 'unit/view', ['unit' => $unit]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response) : Response
    {
        /* @var $unit Unit */
        $unit = Unit::find($request->getAttribute('id'));

        if ($request->isPost()) {
            if ($unit->fill($request->getParams())->save()) {
                Alert::add("Successful update {$unit->name}");
                return $this->goBack();
            }
        }

        return $this->render($response, 'unit/view', ['unit' => $unit]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDelete(Request $request, Response $response) : Response
    {
        /* @var $model Unit */
        $model = Unit::find($request->getAttribute('id'));

        if ($model->delete()) {
            Alert::add("Successful delete {$model->name}");
            $indexPath = $this->router->pathFor('unit.index');
            return $response->withRedirect($indexPath);
        }

        return $this->goBack();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionShowImages(Request $request, Response $response) : Response
    {
        $unit = Unit::find($request->getAttribute('id'));
        return $this->render($response, 'image/index', ['unit' => $unit]);
    }
}