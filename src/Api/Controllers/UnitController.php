<?php

namespace Aigisu\Api\Controllers;

use Aigisu\Api\Transformers\UnitTransformerFacade;
use Aigisu\Models\Unit;
use Aigisu\Models\Unit\MissingCG;
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
        $units = UnitTransformerFacade::transformAll(
            $this->findOrFailUnit($request),
            $this->get('router'),
            $this->getExpandParam($request)
        );

        return $this->read($response, $units);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $unit = UnitTransformerFacade::transform(
            $this->findOrFailUnit($request),
            $this->get('router'),
            $this->getExpandParam($request)
        );

        return $this->read($response, $unit);
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

        return $this->create($response, $this->get('router')->pathFor('api.unit.view', [
            'id' => $unit->getKey()
        ]));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        $this->findOrFailUnit($request)->saveUnitModel($request);

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
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
     * @return Response
     */
    public function actionMissing(Request $request, Response $response): Response
    {
        $unit = Unit::with('cg')->findOrFail($this->getID($request));

        $missing = new MissingCG($unit['cg']);
        $missing = $missing->filter([
            'is_male' => $unit['gender'] == Unit::GENDER_MALE,
            'is_dmm' => $unit['dmm'],
            'is_nutaku' => $unit['nutaku'],
            'is_special_cg' => $unit['special_cg'],
        ]);


        return $this->read($response, $missing);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionRarities(Request $request, Response $response): Response
    {
        return $this->read($response, Unit::getRarities());
    }

    /**
     * @param Request $request
     * @return Unit|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static|static[]
     */
    private function findOrFailUnit(Request $request)
    {
        $expand = $this->getExpandParam($request);
        $unit = new Unit();
        if (in_array($expand, ['cg'])) { //edger loader for less queries
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
