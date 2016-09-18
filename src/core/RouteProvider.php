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
use InvalidArgumentException;
use Slim\App;
use Slim\Interfaces\RouteGroupInterface;

class RouteProvider
{
    /** @var  array */
    protected $webMiddlewares = [
        TrailingSlash::class,
        HomeAssets::class,
        ShowQueries::class,
        Alert::class,
    ];
    /** @var  array */
    protected $apiMiddlewares = [
        ExceptionHandler::class,
    ];

    /** @var  array */
    protected $storageMiddlewares = [];


    /** @var App */
    private $app;

    /**
     * RouteProvider constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
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

        $this->applyMiddlewares($web, $this->webMiddlewares);
    }

    /**
     * @param RouteGroupInterface $group
     * @param array $middlewares
     * @throws InvalidArgumentException
     */
    protected function applyMiddlewares(RouteGroupInterface $group, array $middlewares)
    {
        $middlewareProvider = $this->app->getContainer()->get('middlewares');
        foreach ($middlewares as $middleware) {
            $group->add($middlewareProvider->createMiddleware($middleware));
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

        $this->applyMiddlewares($api, $this->apiMiddlewares);
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

        $this->applyMiddlewares($storage, $this->storageMiddlewares);
    }
}