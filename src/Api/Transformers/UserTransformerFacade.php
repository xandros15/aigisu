<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 21:57
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Core\Model;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class UserTransformerFacade extends AbstractFacade
{
    /**
     * @param iterable $models
     * @param string $expand
     *
     * @return array
     */
    public function transformAll(iterable $models, string $expand = ''): array
    {
        return $this->parse(new Collection($models, new UserTransformer()), $expand);
    }

    /**
     * @param Model $model
     * @param string $expand
     *
     * @return array
     */
    public function transformOne(Model $model, string $expand = ''): array
    {
        return $this->parse(new Item($model, new UserTransformer()), $expand);
    }
}
