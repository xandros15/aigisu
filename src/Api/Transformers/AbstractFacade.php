<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-04-18
 * Time: 12:28
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Core\Model;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\ResourceInterface;

abstract class AbstractFacade
{
    /**
     * @param iterable $models
     * @param string $expand
     *
     * @return array
     */
    public abstract function transformAll(iterable $models, string $expand = ''): array;

    /**
     * @param Model $model
     * @param string $expand
     *
     * @return array
     */
    public abstract function transformOne(Model $model, string $expand = ''): array;

    protected function parse(ResourceInterface $data, string $expand)
    {
        $fractal = new Fractal();
        $fractal->setSerializer(new SimplyArraySerializer());

        return $fractal->parseIncludes($expand)->createData($data)->toArray();
    }

}
