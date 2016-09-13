<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 00:03
 */

namespace Aigisu\Components\Url;


use Interop\Container\ContainerInterface;
use Slim\Router;

class UrlManager
{
    /** @var Router */
    private $router;

    /** @var string */
    private $url;

    /**
     * UrlManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->url = (string) $container->get('siteUrl');
        $this->router = $container->get('router');
    }

    /**
     * @param string $name
     * @param array $params
     * @param array $query
     * @return string
     */
    public function to(string $name, array $params = [], array $query = []) : string
    {
        return $this->router->setBasePath($this->url)->pathFor($name, $params, $query);
    }

}