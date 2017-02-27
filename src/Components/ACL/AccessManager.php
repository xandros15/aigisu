<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-27
 * Time: 20:45
 */

namespace Aigisu\Components\ACL;


use Aigisu\Components\ACL\Exceptions\AccessNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AccessManager implements ContainerInterface
{
    private $accessMiddlewares = [];

    public function __construct(array $middlewares)
    {
        foreach ($middlewares as $name => $middleware) {
            $this->addMiddleware($name, $middleware);
        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->accessMiddlewares);
    }

    /******************************************
     * PSR-11
     ******************************************/

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new AccessNotFoundException("Not found `{$id}`");
        }

        return $this->accessMiddlewares[$id];
    }

    private function addMiddleware(string $name, AbstractAccessMiddleware $middleware)
    {
        $this->accessMiddlewares[$name] = $middleware;
    }
}
