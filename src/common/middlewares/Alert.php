<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-25
 * Time: 16:36
 */

namespace Aigisu\Common\Middlewares;


use Aigisu\Common\Components\Alert\Alert as AlertComponent;
use Aigisu\Core\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

class Alert extends Middleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (session_status() == PHP_SESSION_NONE && !session_id()) {
            session_start();
        }

        $alert = new AlertComponent();
        $alert->init();
        $this->set('alert', $alert);
        return $next($request, $response);
    }
}