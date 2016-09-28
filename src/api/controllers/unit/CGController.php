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
        $images = CG::where(CG::UNIT_RELATION_COLUMN, $this->getUnitID($request))
            ->with($this->getExtendedParam($request))
            ->get();

        return $response->withJson($images->toArray(), self::STATUS_OK);
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
        $image = CG::where(CG::UNIT_RELATION_COLUMN, $this->getUnitID($request))
            ->with($this->getExtendedParam($request))
            ->findOrFail($this->getID($request));

        return $response->withJson($image->toArray(), self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        $cg = new CG(array_merge([CG::UNIT_RELATION_COLUMN => $this->getUnitID($request)], $request->getParams()));
        $cg->uploadCG($request, $this->get(FilesystemManager::class));
        $cg->saveOrFail();

        return $response->withStatus(self::STATUS_CREATED);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        return $response->withStatus(self::STATUS_METHOD_NOT_ALLOWED);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        return $response->withStatus(self::STATUS_METHOD_NOT_ALLOWED);
    }
}