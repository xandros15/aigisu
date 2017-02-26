<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 19:35
 */

namespace Aigisu\Components\Http;


use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AttributesAttachMiddleware implements MiddlewareInterface
{

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $body = $request->getParams();
        $body['_attributes'] = $request->getAttribute('route')->getArguments();

        return $next($request->withParsedBody($body), $response);
    }
}
