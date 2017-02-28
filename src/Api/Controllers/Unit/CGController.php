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
        $cgs = CGTransformerFacade::transformAll(
            $this->findCGOrFail($request),
            $this->get('router'),
            $this->getExpandParam($request)
        );

        return $this->read($response, $cgs);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $cg = CGTransformerFacade::transform(
            $this->getExpandParam($request),
            $this->get('router'),
            $this->findCGOrFail($request)
        );

        return $this->read($response, $cg);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $cg = new CG();
        $cg->saveOrFailCG($request);

        return $this->create($response, $this->get('router')->pathFor('api.unit.cg.view', [
            'id' => $cg->getKey(),
        ]));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        $cg = $this->findCGOrFail($request);
        $cg->saveOrFailCG($request);

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
     * @return CG|\Illuminate\Database\Eloquent\Collection
     */
    private function findCGOrFail(Request $request)
    {
        $expand = $this->getExpandParam($request);
        $cg = new CG();
        if (in_array($expand, ['unit'])) { //edger loader for less queries
            $cg = $cg->with('unit');
        }

        if ($id = $this->getID($request)) {
            $cg = $cg->findOrFail($id);
        } else {
            $cg = $cg->where('unit_id', $request->getAttribute(self::UNIT_INDEX))->get();
        }

        return $cg;
    }
}
