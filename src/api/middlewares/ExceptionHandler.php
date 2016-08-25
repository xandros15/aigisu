<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 19:35
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Core\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ExceptionHandler extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        try {
            return $next($request, $response);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundException($request, $response);
        }
    }
}