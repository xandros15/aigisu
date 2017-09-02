<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-20
 * Time: 12:53
 */

namespace Aigisu\Api\Controllers\Unit;


use Aigisu\Api\Controllers\AbstractController;
use Aigisu\Api\Transformers\CGTransformer;
use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Models\Unit\CG;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

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
        $cgs = $this->findCGOrFail($request);

        return $this->read($response, $this->transformCg($cgs, $this->getExpandParam($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        $cg = $this->findCGOrFail($request);

        return $this->read($response, $this->transformCg($cg, $this->getExpandParam($request)));
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

        return $this->create($response, $location, $this->transformCg($cg, $this->getExpandParam($request)));
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

        return $this->update($response, $this->transformCg($cg, $this->getExpandParam($request)));
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

    /**
     * @param \Traversable|array|Model $data
     * @param string|array $expand
     *
     * @return array
     */
    private function transformCg($data, $expand)
    {
        $storageHost = $this->get('settings')->get('flysystem')['host'];
        $fractal = new Manager();
        $transformer = new CGTransformer();
        $transformer->setStorageUri(Uri::createFromString($storageHost));

        if ($data instanceof \Traversable || is_array($data)) {
            $data = new Collection($data, $transformer);
        } else {
            $data = new Item($data, $transformer);
        }

        return $fractal->setSerializer(new SimplyArraySerializer())
                       ->parseIncludes($expand)
                       ->createData($data)
                       ->toArray();
    }
}
