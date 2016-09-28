<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-20
 * Time: 12:53
 */

namespace Aigisu\Api\Controllers\Unit;


use Aigisu\Api\Controllers\Controller;
use Aigisu\Api\Models\Unit\CG;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Slim\Http\Request;
use Slim\Http\Response;

class CGController extends Controller
{
    const UNIT_INDEX = 'unitId';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        $cgs = CG::where('unit_id', $this->getUnitID($request))
            ->with($this->getExtendedParam($request))
            ->get();

        return $response->withJson($cgs->toArray(), self::STATUS_OK);
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
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        return $response->withJson($this->findCGOrFail($request)->toArray(), self::STATUS_OK);
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

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $cg = new CG(array_merge(['unit_id' => $this->getUnitID($request)], $request->getParams()));
        $cg->uploadCG($request, $this->get(FilesystemManager::class));
        $cg->saveOrFail();

        return $this->created($response, $cg->getKey());
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
        $cg->uploadCG($request, $this->get(FilesystemManager::class));
        $cg->saveOrFail();

        return $response->withStatus(self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        $this->findCGOrFail($request)->delete();
        return $response->withStatus(self::STATUS_OK);
    }
}