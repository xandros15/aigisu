<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 18:50
 */

namespace Aigisu;


use Slim\Http\Request;
use Slim\Http\Response;

abstract class Middleware
{
    use AwareContainer;

    abstract public function __invoke(Request $request, Response $response, callable $next);
}