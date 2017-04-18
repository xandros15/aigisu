<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:31
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Api\Api;
use Aigisu\Components\Api\ApiResponse;
use Aigisu\Components\Flash;
use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Core\ActiveContainer;
use Interop\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Router;

abstract class AbstractController extends ActiveContainer
{

    /** @var Flash */
    protected $flash;

    /**
     * AbstractController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->flash = new Flash($this->get(Messages::class));
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
     * @param $name
     * @param $request
     * @param $response
     *
     * @return ApiResponse
     * @throws ForbiddenException
     */
    protected function callApi($name, $request, $response): ApiResponse
    {
        $api = new Api($this->get('router')->getNamedRoute($name));
        $apiResponse = $api->send($request, $response);
        if ($apiResponse->isForbidden()) {
            throw new ForbiddenException($request, $response);
        }

        return $apiResponse;
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
