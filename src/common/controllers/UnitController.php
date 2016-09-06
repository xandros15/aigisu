<?php

namespace Aigisu\Common\Controllers;

use Aigisu\Api\Models\Unit;
use Aigisu\Common\Models\UnitSort;
use finfo;
use Illuminate\Database\Eloquent\Collection;
use Slim\Exception\NotFoundException;
use Slim\Http\Body;
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

        $clientResponse = $this->makeClient($response)->get($path, [
            'query' => ['extended' => ['images', 'tags']],
        ]);

        $units = new Collection(json_decode($clientResponse->getBody(), true));
        $max = $units->count();

        $unitSort = new UnitSort($request, $this->router);
        foreach ($unitSort->getOrders() as $order => $direction) {
            $units = $units->sortBy($order, SORT_REGULAR, $direction === UnitSort::SORT_DESC);
        }

        $units = $units->forPage($request->getParam('page', 1), Unit::UNITS_PER_PAGE);

        $pagination = new Pagination($request, $this->router, [
            Pagination::OPT_TOTAL => $max,
            Pagination::OPT_PER_PAGE => Unit::UNITS_PER_PAGE
        ]);

        $response = $this->render($response, 'unit/index', [
            'units' => $units,
            'pagination' => $pagination,
            'unitSort' => $unitSort
        ]);


        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response) : Response
    {
        return $response->withStatus(503);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response) : Response
    {
        return $response->withStatus(503);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response) : Response
    {
        $path = $this->router->pathFor('api.unit.view', ['id' => $this->getID($request)]);

        $clientResponse = $this->makeClient($response)->get($path, [
            'query' => ['extended' => ['images', 'tags']],
        ]);

        $unit = json_decode($clientResponse->getBody(), true);

        return $this->render($response, 'unit/view', ['unit' => $unit]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDelete(Request $request, Response $response) : Response
    {
        return $response->withStatus(503);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionShowImages(Request $request, Response $response) : Response
    {
        $path = $this->router->pathFor('api.unit.view', ['id' => $this->getID($request)]);

        $clientRequest = $this->makeClient($response)->get($path, ['query' => ['extended' => ['images']]]);
        $unit = json_decode($clientRequest->getBody(), true);

        if ($unit['images']) {
            $unit['images'] = new Collection($unit['images']);
            return $this->render($response, 'image/index', ['unit' => $unit]);
        }
        throw new NotFoundException($request, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionGetIcon(Request $request, Response $response) : Response
    {
        $iconPath = realpath($this->get('upload') . '/public/icons/');
        $iconFileName = $iconPath . DIRECTORY_SEPARATOR . $request->getAttribute('name');

        return $this->getImage($iconFileName, $request, $response);

    }

    /**
     * @param string $imageFileName
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    protected function getImage(string $imageFileName, Request $request, Response $response) : Response
    {
        if (!$image = @fopen($imageFileName, 'rb')) {
            throw new NotFoundException($request, $response);
        }

        $body = new Body($image);
        $finfo = new finfo();;

        return $response->withBody($body)
            ->withHeader('Content-Type', $finfo->buffer($body, FILEINFO_MIME_TYPE));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionGetHelpImage(Request $request, Response $response) : Response
    {
        $imagePath = realpath($this->get('upload') . '/public/images/');
        $imageFileName = $imagePath . DIRECTORY_SEPARATOR . $request->getAttribute('id');

        return $this->getImage($imageFileName, $request, $response);
    }
}