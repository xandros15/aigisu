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
     *
     * @return Response
     */
    public function actionIndex(Request $request, Response $response): Response
    {
        $transformer = new CGTransformerFacade($this->get('router'));

        return $this->read($response,
            $transformer->transformAll($this->findCGOrFail($request), $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $transformer = new CGTransformerFacade($this->get('router'));

        return $this->read($response,
            $transformer->transformOne($this->findCGOrFail($request), $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $cg = new CG();
        $cg->saveOrFailCG($request);
        $location = $this->get('router')->pathFor('api.unit.cg.view', [
            'id' => $cg->getKey(),
        ]);
        $transformer = new CGTransformerFacade($this->get('router'));

        return $this->create($response, $location, $transformer->transformOne($cg, $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        $cg = $this->findCGOrFail($request);
        $cg->saveOrFailCG($request);
        $transformer = new CGTransformerFacade($this->get('router'));

        return $this->update($response, $transformer->transformOne($cg, $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        $this->findCGOrFail($request)->delete();

        return $this->delete($response);
    }

    /**
     * @param Request $request
     *
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
