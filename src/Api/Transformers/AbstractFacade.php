<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-04-18
 * Time: 12:28
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Core\Model;
use Slim\Http\Request;
use Slim\Interfaces\RouterInterface;

abstract class AbstractFacade
{
    const EXPAND_PARAM = 'expand';

    /** @var RouterInterface */
    protected $router;
    /** @var Request */
    private $request;

    /**
     * AbstractFacade constructor.
     *
     * @param RouterInterface $router
     * @param Request $request
     */
    public function __construct(RouterInterface $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @param iterable $models
     *
     * @return array
     */
    public abstract function transformAll(iterable $models): array;

    /**
     * @param Model $model
     *
     * @return array
     */
    public abstract function transformOne(Model $model): array;

    /**
     * @return mixed
     */
    protected function getExpandParam()
    {
        return $this->request->getQueryParam(self::EXPAND_PARAM, '');
    }
}
