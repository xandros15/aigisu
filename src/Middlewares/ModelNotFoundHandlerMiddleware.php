<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 23:01
 */

namespace Aigisu\Middlewares;


use Aigisu\Core\MiddlewareInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ModelNotFoundHandlerMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws NotFoundException
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        try {
            return $next($request, $response);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundException($request, $response);
        }
    }
}
