<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 23:18
 */

namespace Aigisu\Middlewares;


use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AccessControlAllowMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        /** @var $response \Slim\Http\Response */
        $response = $next($request, $response);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers',
                'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    }
}
