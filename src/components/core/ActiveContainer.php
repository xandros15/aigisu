<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 19:05
 */

namespace Aigisu;

use Aigisu\View\View;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * @property Response response
 * @property Request request
 * @property View view
 * @property Router router
 */
class ActiveContainer
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        if (!$this->container->has($name)) {
            throw new InvalidArgumentException("Attribute {$name} doesn't exist");
        }
        return $this->container->get($name);
    }
}