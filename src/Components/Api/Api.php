<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 01:53
 */

namespace Aigisu\Components\Api;


use Aigisu\Components\Http\Exceptions\HttpException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouteInterface;

class Api
{
    /** @var RouteInterface */
    private $route;

    /**
     * Api constructor.
     *
     * @param RouteInterface $route
     */
    public function __construct(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ApiResponse
     * @throws NotFoundException
     */
    public function send(Request $request, Response $response): ApiResponse
    {
        $request = $request->withHeader('Accept', 'application/json');
        try {
            $response = $this->route->run($request, $response);
        } catch (HttpException $e) {
            $response = $e->getResponse();
        } catch (NotFoundException $e) {
            $request = $e->getRequest()->withHeader('Accept', 'text/html');
            throw new NotFoundException($request, $e->getResponse());
        }

        return new ApiResponse($response);
    }

}
