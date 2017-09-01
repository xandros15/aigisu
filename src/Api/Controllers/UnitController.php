<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Transformers\UnitTransformerFacade;
use Aigisu\Components\Http\Exceptions\BadRequestException;
use Aigisu\Models\Unit;
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;
use Slim\Http\Request;
use Slim\Http\Response;

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
        $transformer = new UnitTransformerFacade($this->get('router'));

        return $this->read($response, $transformer->transformAll(Unit::all(), $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $transformer = new UnitTransformerFacade($this->get('router'));

        return $this->read($response,
            $transformer->transformOne($this->findOrFailUnit($request), $this->getExpandParam($request)));
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
        $unit->saveUnitModel($request);
        $location = $this->get('router')->pathFor('api.unit.view', [
            'id' => $unit->getKey(),
        ]);

        $transformer = new UnitTransformerFacade($this->get('router'));

        return $this->create($response, $location, $transformer->transformOne($unit, $this->getExpandParam($request)));
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
        $unit->saveUnitModel($request);
        $transformer = new UnitTransformerFacade($this->get('router'));

        return $this->update($response, $transformer->transformOne($unit, $this->getExpandParam($request)));
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
}
