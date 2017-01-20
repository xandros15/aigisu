<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 00:03
 */

namespace Aigisu\Components\Url;


use Slim\Router;

class UrlManager
{
    /** @var Router */
    private $router;

    /** @var string */
    private $url;

    /**
     * UrlManager constructor.
     * @param Router $router
     * @param string $url
     */
    public function __construct(Router $router, string $url)
    {
        $this->router = $router;
        $this->url = $url;
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
