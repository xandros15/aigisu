<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:31
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Api\Api;
use Aigisu\Components\Flash;
use Aigisu\Core\ActiveContainer;
use Interop\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Router;

abstract class AbstractController extends ActiveContainer
{

    /** @var Flash */
    protected $flash;
    /** @var Api */
    protected $api;

    /**
     * AbstractController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->flash = new Flash($this->get(Messages::class));
        $this->api = $this->get(Api::class);
    }

    /**
     * @param Response $response
     *
     * @return Response
     */
    public function goHome(Response $response): Response
    {
        return $response->withRedirect('/');
    }

    /**
     * @param Response $response
     * @param string $name
     * @param array $params
     *
     * @return Response
     */
    protected function redirect(Response $response, string $name, array $params = [])
    {
        /** @var $router Router */
        $router = $this->get('router');
        $path = $router->relativePathFor($name, $params['arguments'] ?? [], $params['query'] ?? []);

        return $response->withRedirect($path);
    }
}
