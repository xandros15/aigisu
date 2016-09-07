<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-07
 * Time: 23:24
 */

namespace Aigisu\Core;


use Aigisu\Api\Middlewares\ExceptionHandler;
use Aigisu\Common\Middlewares\Alert;
use Aigisu\Common\Middlewares\HomeAssets;
use Aigisu\Common\Middlewares\ShowQueries;
use Aigisu\Common\Middlewares\TrailingSlash;
use Aigisu\Common\Middlewares\View;
use Slim\App;

class RouteProvider extends ActiveContainer
{
    /** @var App */
    private $app;

    /**
     * RouteProvider constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app->getContainer());
        $this->app = $app;
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        $container = $this->app->getContainer();
        $this->app->group('', function () {
            /** @var $this Main */
            /** @noinspection PhpIncludeInspection */
            require $this->getContainer()->get('root') . '/routes/web.php';
        })
            ->add(new TrailingSlash($container))
            ->add(new HomeAssets($container))
            ->add(new ShowQueries($container))
            ->add(new View($container))
            ->add(new Alert($container));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $container = $this->app->getContainer();
        $this->app->group('/api', function () {
            /** @var $this Main */
            /** @noinspection PhpIncludeInspection */
            require $this->getContainer()->get('root') . '/routes/api.php';
        })->add(new ExceptionHandler($container));
    }
}