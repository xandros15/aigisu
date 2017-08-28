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
use Slim\Interfaces\RouterInterface;

class UnitTransformerFacade extends AbstractFacade
{
    /** @var RouterInterface */
    private $router;

    /**
     * UnitTransformerFacade constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param iterable $units
     * @param string $expand
     *
     * @return array
     */
    public function transformAll(iterable $units, string $expand = ''): array
    {
        return $this->parse(new Collection($units, new UnitTransformer($this->router)), $expand);
    }

    /**
     * @param Model $unit
     * @param string $expand
     *
     * @return array
     */
    public function transformOne(Model $unit, string $expand = ''): array
    {
        return $this->parse(new Item($unit, new UnitTransformer($this->router)), $expand);
    }
}
