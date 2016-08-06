<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-06
 * Time: 02:57
 */

namespace Middlewares;


use Aigisu\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

class ShowQueries extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->connection->enableQueryLog();
        $response = $next($request, $response);
        dump($this->connection->getQueryLog());

        return $response;
    }
}