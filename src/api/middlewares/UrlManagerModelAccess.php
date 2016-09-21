<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-21
 * Time: 01:59
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Components\Url\UrlManager;
use Aigisu\Core\Model;
use Slim\Http\Request;
use Slim\Http\Response;

class UrlManagerModelAccess extends Middleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        Model::setUrlManager(new UrlManager($this->get('router'), $this->get('siteUrl')));
        return $next($request, $response);
    }
}