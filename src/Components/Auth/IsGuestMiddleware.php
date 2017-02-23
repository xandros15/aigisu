<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-23
 * Time: 01:01
 */

namespace Aigisu\Components\Auth;


use Aigisu\Core\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

class IsGuestMiddleware extends Middleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $request = $request->withAttribute('is_guest', true);
        return $next($request, $response);
    }
}
