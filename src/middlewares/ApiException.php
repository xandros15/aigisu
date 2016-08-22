<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-22
 * Time: 20:23
 */

namespace Middlewares;


use Aigisu\Middleware;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiException extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        try {
            return $next($request, $response);
        } catch (Exception $exception) {
            return $response->withJson(['error' => 'Server error'], 500);
        }
    }
}