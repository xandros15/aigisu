<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 01:23
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Core\Model;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class CGTransformerFacade extends AbstractFacade
{
    /**
     * @param iterable $cgs
     *
     * @return array
     */
    public function transformAll(iterable $cgs): array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $collection = new Collection($cgs, new CGTransformer($this->router));
        $fractal->parseIncludes($this->getExpandParam());

        return $fractal->createData($collection)->toArray();
    }

    /**
     * @param Model $cg
     *
     * @return array
     */
    public function transformOne(Model $cg): array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $item = new Item($cg, new CGTransformer($this->router));
        $fractal->parseIncludes($this->getExpandParam());

        return $fractal->createData($item)->toArray();
    }
}
