<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 01:53
 */

namespace Aigisu\Components\Api;


use Slim\Exception\SlimException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouteInterface;

class Api
{
    /** @var RouteInterface */
    private $route;

    /**
     * Api constructor.
     * @param RouteInterface $route
     */
    public function __construct(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return ApiResponse
     */
    public function send(Request $request, Response $response) : ApiResponse
    {
        try {
            $response = $this->route->run($request, $response);
        } catch (SlimException $e) {
            $response = $e->getResponse();
        }

        return new ApiResponse($response);
    }

}
