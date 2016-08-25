<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-13
 * Time: 17:30
 */

namespace Aigisu\Common\Middlewares;


use Aigisu\Common\Components\View\LayoutExtension;
use Aigisu\Core\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property LayoutExtension $view
 * @property string siteUrl
 */
class FormAssets extends Middleware
{
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $this->view->append(function () {
            return sprintf('<script src="%s"></script>', $this->siteUrl . '/js/form.js');
        }, LayoutExtension::PH_BODY_END);
        return $next($request, $response);
    }
}