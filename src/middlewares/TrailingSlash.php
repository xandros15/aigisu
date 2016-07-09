<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 19:25
 */

namespace Middlewares;


use Aigisu\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

class TrailingSlash extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            $uri = $uri->withPath(substr($path, 0, -1));
            return $response->withRedirect((string)$uri, 301);
        }

        return $next($request, $response);
    }
}