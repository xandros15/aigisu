<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 16:31
 */

namespace Aigisu\Components\Http;


use Aigisu\Components\Http\Exceptions\HttpException;
use Aigisu\Components\Http\Handlers\HandlerInterface;
use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class MiddlewareHandler extends ActiveContainer implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        try {
            $response = $next($request, $response);
        } catch (HttpException $exception) {
            $handler = $this->determineHandler($exception);
            $response = $handler ? $handler($request, $response) : $exception->getResponse();
        }

        return $response;
    }

    /**
     * @param $exception
     * @return HandlerInterface|null
     * @throws SlimException
     */
    private function determineHandler($exception)
    {
        return $handler ?? null;
    }
}
