<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 01:23
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Models\Unit\CG;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Interfaces\RouterInterface;

class CGTransformerFacade
{
    /**
     * @param $cgs
     * @param RouterInterface $router
     * @param array $expand
     * @return array
     */
    public static function transformAll($cgs, RouterInterface $router, $expand = []) : array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $collection = new Collection($cgs, new CGTransformer($router));
        $fractal->parseIncludes($expand);
        return $fractal->createData($collection)->toArray();
    }

    /**
     * @param CG $cg
     * @param RouterInterface $router
     * @param array $expand
     * @return array
     */
    public static function transform(CG $cg, RouterInterface $router, $expand = []) : array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $item = new Item($cg, new CGTransformer($router));
        $fractal->parseIncludes($expand);
        return $fractal->createData($item)->toArray();
    }
}
