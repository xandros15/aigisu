<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 21:57
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Core\Model;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class UserTransformerFacade extends AbstractFacade
{
    /**
     * @param iterable $users
     *
     * @return array
     */
    public function transformAll(iterable $users): array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $collection = new Collection($users, new UserTransformer());
        $fractal->parseIncludes($this->getExpandParam());

        return $fractal->createData($collection)->toArray();
    }

    /**
     * @param Model $unit
     *
     * @return array
     */
    public function transformOne(Model $unit): array
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());
        $item = new Item($unit, new UserTransformer());
        $fractal->parseIncludes($this->getExpandParam());

        return $fractal->createData($item)->toArray();
    }
}
