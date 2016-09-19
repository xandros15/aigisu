<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-10
 * Time: 00:55
 */

namespace Aigisu\Common\Middlewares;


use Aigisu\Common\Components\View\LayoutExtension;
use Aigisu\Common\Components\View\View;
use Aigisu\Core\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property LayoutExtension $view
 * @property string siteUrl
 */
class HomeAssets extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $renderer = $this->get(View::class);
        $renderer->append(function () {
            return sprintf('<link rel="stylesheet" href="%s" />', $this->siteUrl . '/css/main.css');
        }, LayoutExtension::PH_HEAD, 11);
        $renderer->append(function () {
            return sprintf('<script src="%s"></script>', $this->siteUrl . '/js/main.js');
        }, LayoutExtension::PH_BODY_END, 10);
        return $next($request, $response);
    }
}