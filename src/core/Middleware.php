<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 18:50
 */

namespace Aigisu\Core;


use Slim\Http\Request;
use Slim\Http\Response;

abstract class Middleware extends ActiveContainer
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    abstract public function __invoke(Request $request, Response $response, callable $next) : Response;
}
