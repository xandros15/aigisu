<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-25
 * Time: 14:26
 */

namespace Aigisu\Common\Middlewares;


use Aigisu\Common\Components\View\View as ViewComponent;
use Aigisu\Core\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

class View extends Middleware
{
    const DIR_VIEW = __DIR__ . '/../view/';

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $this->set('view', new ViewComponent(self::DIR_VIEW));
        return $next($request, $response);
    }
}