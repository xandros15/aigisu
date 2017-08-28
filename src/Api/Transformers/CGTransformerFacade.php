<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 01:23
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Core\Model;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Interfaces\RouterInterface;

class CGTransformerFacade extends AbstractFacade
{
    private $router;

    /**
     * CGTransformerFacade constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param iterable $cgs
     * @param string $expand
     *
     * @return array
     */
    public function transformAll(iterable $cgs, string $expand = ''): array
    {
        $collection = new Collection($cgs, new CGTransformer($this->router));

        return $this->parse($collection, $expand);
    }

    /**
     * @param Model $cg
     * @param string $expand
     *
     * @return array
     */
    public function transformOne(Model $cg, string $expand = ''): array
    {
        $item = new Item($cg, new CGTransformer($this->router));

        return $this->parse($item, $expand);
    }
}
