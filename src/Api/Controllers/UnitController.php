<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Transformers\UnitTransformerFacade;
use Aigisu\Models\Unit;
use Slim\Http\Request;
use Slim\Http\Response;

class UnitController extends AbstractController
{

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        $expand = $this->getExtendedParam($request);
        $units = Unit::with($expand)->get();
        $units = UnitTransformerFacade::transformAll($units, $this->get('router'), $expand);

        return $this->retrieve($response, $units);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $expand = $this->getExtendedParam($request);
        /** @var $unit Unit */
        $unit = Unit::with($expand)->findOrFail($this->getID($request));
        $transformedUnit = UnitTransformerFacade::transform($unit, $this->get('router'), $expand);

        return $this->retrieve($response, $transformedUnit);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $unit = new Unit();
        $unit->saveUnitModel($request);

        return $this->created($response, $this->get('router')->pathFor('api.unit.view', ['id' => $unit->getKey()]));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        /** @var $unit Unit */
        $unit = Unit::findOrFail($this->getID($request));
        $unit->saveUnitModel($request);

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        Unit::findOrFail($this->getID($request))->delete();

        return $this->delete($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionRarities(Request $request, Response $response): Response
    {
        return $this->retrieve($response, Unit::getRarities());
    }
}
