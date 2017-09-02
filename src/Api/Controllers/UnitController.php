<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Transformers\UnitTransformer;
use Aigisu\Components\Http\Exceptions\BadRequestException;
use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Models\Unit;
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

class UnitController extends AbstractController
{

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionIndex(Request $request, Response $response): Response
    {
        $units = Unit::all();

        return $this->read($response, $this->transformUnit($units, $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $unit = $this->findOrFailUnit($request);

        return $this->read($response, $this->transformUnit($unit, $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $unit = new Unit();
        $unit->saveUnitModel($request->getParams());
        $location = $this->get('router')->pathFor('api.unit.view', [
            'id' => $unit->getKey(),
        ]);

        return $this->create($response, $location, $this->transformUnit($unit, $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws BadRequestException
     */
    public function actionUploadIcon(Request $request, Response $response): Response
    {
        $unit = $this->findOrFailUnit($request);
        if (!$unit->uploadIcon($request->getBody(), $this->get(Filesystem::class))) {
            throw new BadRequestException($request, $response->withJson([
                'message' => 'error with upload icon',
            ]));
        }

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        $unit = $this->findOrFailUnit($request);
        $unit->saveUnitModel($request->getParams());

        return $this->update($response, $this->transformUnit($unit, $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        $this->findOrFailUnit($request)->delete();

        return $this->delete($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionRarities(Request $request, Response $response): Response
    {
        return $this->read($response, Unit::getRarities());
    }

    /**
     * @param Request $request
     *
     * @return Unit|Collection
     */
    private function findOrFailUnit(Request $request)
    {
        /** @var  $unit Unit|Collection */
        $expand = $this->getExpandParam($request);
        $unit = new Unit();
        if (in_array($expand, ['cg', 'missing_cg'])) { //edger loader for less queries
            $unit = $unit->with('cg');
        }

        if ($id = $this->getID($request)) {
            $unit = $unit->findOrFail($id);
        } else {
            $unit = $unit->get();
        }

        return $unit;
    }

    /**
     * @param \Traversable|array|Model $data
     * @param string|array $expand
     *
     * @return array
     */
    private function transformUnit($data, $expand)
    {
        $storageHost = $this->get('settings')->get('flysystem')['host'];
        $fractal = new Manager();
        $transformer = new UnitTransformer();
        $transformer->setStorageUri(Uri::createFromString($storageHost));

        if ($data instanceof \Traversable || is_array($data)) {
            $data = new \League\Fractal\Resource\Collection($data, $transformer);
        } else {
            $data = new Item($data, $transformer);
        }

        return $fractal->setSerializer(new SimplyArraySerializer())
                       ->parseIncludes($expand)
                       ->createData($data)
                       ->toArray();
    }
}
