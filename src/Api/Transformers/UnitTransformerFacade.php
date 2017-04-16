<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 21:57
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Models\Unit;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Interfaces\RouterInterface;

class UnitTransformerFacade
{
    /**
     * @param $units
     * @param RouterInterface $router
     * @param array $expand
     *
     * @return array
     */
    public static function transformAll($units, RouterInterface $router, $expand = []): array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $collection = new Collection($units, new UnitTransformer($router));
        $fractal->parseIncludes($expand);

        return $fractal->createData($collection)->toArray();
    }

    /**
     * @param Unit $unit
     * @param RouterInterface $router
     * @param array $expand
     *
     * @return array
     */
    public static function transform(Unit $unit, RouterInterface $router, $expand = []): array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $item = new Item($unit, new UnitTransformer($router));
        $fractal->parseIncludes($expand);

        return $fractal->createData($item)->toArray();
    }
}
