<?php


namespace Aigisu\Components;


use Aigisu\Components\Http\Exceptions\RuntimeException;
use Aigisu\Core\MiddlewareInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiHandlerMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     * @throws RuntimeException
     * @throws NotFoundException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        try {
            return $next($request, $response);
        } catch (ServerException $exception) {
            throw new RuntimeException('Api server exception', 500);
        } catch (ClientException $exception) {
            throw new NotFoundException($request, $response);
        }
    }
}
