<?php

namespace Aigisu\Common\Controllers;

use Aigisu\Api\Models\Unit;
use Aigisu\Common\Components\Alert\Alert;
use Aigisu\Common\Exceptions\FormException;
use Aigisu\Common\Models\UnitSort;
use finfo;
use GuzzleHttp\RequestOptions;
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
        $unit = new \Slim\Collection($request->getParsedBody() ?? []);
        if ($unit->count()) {
            $path = $this->router->pathFor('api.unit.create');
            $textTags = $unit['tags'];
            $unit->set('tags', $textTags ? Unit::tagsToArray($textTags) : ['']);
            $clientResponse = $this->makeClient($response)->post($path, [
                RequestOptions::FORM_PARAMS => (array) $unit->getIterator()
            ]);


            if ($this->addAlertIfError($clientResponse)) {
                $unit->set('tags', $textTags);
            } else {
                Alert::add('Created unit ' . $unit['name']);
                return $response->withRedirect($clientResponse->getHeaderLine('Location'));
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
        $unit = new \Slim\Collection($request->getParsedBody() ?? []);
        if ($unit->count()) {
            $path = $this->router->pathFor('api.unit.update', ['id' => $this->getID($request)]);

            $textTags = $unit['tags'];
            $unit->set('tags', $textTags ? Unit::tagsToArray($textTags) : ['']);

            $clientResponse = $this->makeClient($response)->patch($path, [
                RequestOptions::FORM_PARAMS => (array) $unit->getIterator()
            ]);

            if ($this->addAlertIfError($clientResponse)) {
                $unit->set('tags', $textTags);
            } else {
                return $response->withRedirect($clientResponse->getHeaderLine('Location'));
            }
            if ($clientResponse->getStatusCode() === 200) {
                Alert::add("Successful update {$unit['name']}");
                $path = $this->router->pathFor('unit.view', ['id' => $this->getID($request)]);
                return $response->withRedirect($path);
            }
        }

        return $this->render($response, 'unit/view', ['unit' => $unit]);
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
        $path = $this->router->pathFor('api.unit.delete', ['id' => $this->getID($request)]);

        $clientResponse = $this->makeClient($response)->get($path);

        if ($clientResponse->getStatusCode() === 200) {
            Alert::add("Successful delete unit");
            $indexPath = $this->router->pathFor('unit.index');
            return $response->withRedirect($indexPath);
        }

        return $this->goBack();
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
        $iconFilename = $this->get('uploadDirectory') . '/icons/' . $this->getID($request);

        if (!$icon = @fopen($iconFilename, 'rb')) {
            throw new NotFoundException($request, $response);
        }

        $body = new Body($icon);
        $finfo = new finfo();;

        return $response->withBody($body)
            ->withHeader('Content-Type', $finfo->buffer($body, FILEINFO_MIME_TYPE));
    }
}