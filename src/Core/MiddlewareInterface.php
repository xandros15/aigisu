<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 15:06
 */

namespace Aigisu\Core;


use Slim\Http\Request;
use Slim\Http\Response;

interface MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, callable $next) : Response;
}