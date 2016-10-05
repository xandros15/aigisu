<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-07
 * Time: 23:24
 */

namespace Aigisu\Core;


use InvalidArgumentException;
use Slim\App;
use Slim\Interfaces\RouteGroupInterface;

class RouteProvider
{
    /** @var App */
    private $app;

    /** @var array */
    private $middlewares;

    /**
     * RouteProvider constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->middlewares = $app->getContainer()->get('middlewares');
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
        $this->mapStorageRoutes();
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
        $web = $this->app->group('', function () {
            /** @var $this Main */
            /** @noinspection PhpIncludeInspection */
            require $this->getContainer()->get('root') . '/routes/web.php';
        });

        $this->applyMiddlewares($web, $this->middlewares['web']);
    }

    /**
     * @param RouteGroupInterface $group
     * @param array $middlewares
     * @throws InvalidArgumentException
     */
    protected function applyMiddlewares(RouteGroupInterface $group, array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            $group->add(new $middleware($this->app->getContainer()));
        }
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
        $api = $this->app->group('/api', function () {
            /** @var $this Main */
            /** @noinspection PhpIncludeInspection */
            require $this->getContainer()->get('root') . '/routes/api.php';
        });

        $this->applyMiddlewares($api, $this->middlewares['api']);
    }

    /**
     * Define the "storage" routes for the application.
     *
     * @return void
     */
    protected function mapStorageRoutes()
    {
        $storage = $this->app->group('/storage', function () {
            /** @var $this Main */
            /** @noinspection PhpIncludeInspection */
            require $this->getContainer()->get('root') . '/routes/storage.php';
        });

        $this->applyMiddlewares($storage, $this->middlewares['storage']);
    }
}