<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Transformers\UnitTransformerFacade;
use Aigisu\Models\Unit;
use Illuminate\Support\Collection;
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
        $transformer = $this->getTransformer($request);

        return $this->read($response, $transformer->transformAll($this->findOrFailUnit($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $transformer = $this->getTransformer($request);

        return $this->read($response, $transformer->transformOne($this->findOrFailUnit($request)));
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
        $transformer = $this->getTransformer($request);
        $location = $this->get('router')->pathFor('api.unit.view', [
            'id' => $unit->getKey(),
        ]);

        return $this->create($response, $location, $transformer->transformOne($unit));
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
        $transformer = $this->getTransformer($request);

        return $this->update($response, $transformer->transformOne($unit));
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
     * @param Request $request
     *
     * @return UnitTransformerFacade
     */
    private function getTransformer(Request $request): UnitTransformerFacade
    {
        return new UnitTransformerFacade($this->get('router'), $request);
    }
}
