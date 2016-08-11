<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-10
 * Time: 00:55
 */

namespace Middlewares;


use Aigisu\Middleware;
use Aigisu\view\LayoutExtension;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property LayoutExtension $view
 * @property string siteUrl
 */
class HomeAssets extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->view->append(function () {
            return sprintf('<link rel="stylesheet" href="%s" />', $this->siteUrl . '/css/main.css');
        }, LayoutExtension::PH_HEAD);
        $this->view->append(function () {
            return sprintf('<link rel="stylesheet" href="%s" />', $this->siteUrl . '/css/vendor.css');
        }, LayoutExtension::PH_HEAD, 1);
        $this->view->append(function () {
            return sprintf('<script src="%s"></script>', $this->siteUrl . '/js/main.js');
        }, LayoutExtension::PH_BODY_END);
        $this->view->append(function () {
            return sprintf('<script src="%s"></script>', $this->siteUrl . '/js/vendor.js');
        }, LayoutExtension::PH_BODY_END, 1);
        return $next($request, $response);
    }
}