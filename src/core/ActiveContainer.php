<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 19:05
 */

namespace Aigisu\Core;

use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * @property Response $response
 * @property Request $request
 * @property Router $router
 * @property Connection $connection
 * @property string $siteUrl
 * @property string $locale
 */
abstract class ActiveContainer
{
    /** @var ContainerInterface */
    private $container;

    /**
     * ActiveContainer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function get(string $name)
    {
        if (!$this->container->has($name)) {
            throw new InvalidArgumentException("Attribute {$name} doesn't exist");
        }
        return $this->container->get($name);
    }

    /**
     * @param string $name
     * @param $value
     * @return void
     */
    protected function set(string $name, $value)
    {
        $this->container[$name] = $value;
    }
}