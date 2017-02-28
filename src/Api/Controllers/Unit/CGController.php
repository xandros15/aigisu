<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-20
 * Time: 12:53
 */

namespace Aigisu\Api\Controllers\Unit;


use Aigisu\Api\Controllers\AbstractController;
use Aigisu\Api\Transformers\CGTransformerFacade;
use Aigisu\Models\Unit\CG;
use Slim\Http\Request;
use Slim\Http\Response;

class CGController extends AbstractController
{
    const UNIT_INDEX = 'unitId';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        $expand = $this->getExtendedParam($request);
        $cgs = CG::where('unit_id', $this->getUnitID($request))->with($expand)->get();
        $cgs = CGTransformerFacade::transformAll($cgs, $this->get('router'), $expand);

        return $this->read($response, $cgs);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $expand = $this->getExtendedParam($request);
        $cg = $this->findCGOrFail($request);
        $cg = CGTransformerFacade::transform($cg, $this->get('router'), $expand);

        return $this->read($response, $cg);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $cg = new CG(array_merge(['unit_id' => $this->getUnitID($request)], $request->getParams()));
        $cg->uploadCG($request);
        $cg->saveOrFail();

        return $this->create($response, $this->get('router')->pathFor('api.unit.cg.view', [
            'id' => $cg->getKey(),
            'unitId' => $this->getUnitID($request),
        ]));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        /** @var $cg CG */
        $cg = $this->findCGOrFail($request)->fill($request->getParams());
        $cg->uploadCG($request);
        $cg->saveOrFail();

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        $this->findCGOrFail($request)->delete();
        return $this->delete($response);
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getUnitID(Request $request) : int
    {
        return $request->getAttribute(self::UNIT_INDEX);
    }

    /**
     * @param Request $request
     * @return CG
     */
    protected function findCGOrFail(Request $request) : CG
    {
        return CG::where('unit_id', $this->getUnitID($request))
            ->with($this->getExtendedParam($request))
            ->findOrFail($this->getID($request));
    }
}
