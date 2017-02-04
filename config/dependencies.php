<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:26
 */
use Aigisu\Components\Dispatcher;
use Aigisu\Components\Google\GoogleDriveFilesystem;
use Aigisu\Components\Imgur\Imgur;
use Aigisu\Components\Oauth\AccessTokenRepository;
use Aigisu\Components\Oauth\BearerTokenResponse;
use Aigisu\Components\Oauth\ClientRepository;
use Aigisu\Components\Oauth\RefreshTokenRepository;
use Aigisu\Components\Oauth\ScopeRepository;
use Aigisu\Components\Oauth\UserRepository;
use Aigisu\Core\Response;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Events\Dispatcher as EloquentDispatcher;
use Interop\Container\ContainerInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Slim\Http\Headers;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

return [
    Connection::class => function (ContainerInterface $container) {
        $factory = new ConnectionFactory(new LaravelContainer());
        return $factory->make($container->get('database'));
    },
    CapsuleManager::class => function (ContainerInterface $container) {
        $database = new CapsuleManager();
        $database->addConnection($container->get('database'));
        $database->setEventDispatcher(new EloquentDispatcher(new LaravelContainer()));
        $database->setAsGlobal();
        $database->bootEloquent();
        return $database;
    },
    Filesystem::class => function (ContainerInterface $container) {
        $adapter = new Local($container->get('upload'));
        return new Filesystem($adapter);
    },
    GoogleDriveFilesystem::class => function () {
        return new GoogleDriveFilesystem(require __DIR__ . '/google.php');
    },
    Imgur::class => function () {
        return new Imgur(require __DIR__ . '/imgur.php');
    },
    AuthorizationServer::class => function (ContainerInterface $container) {
        $connection = $container->get(Connection::class);

        // Setup the authorization server
        $server = new AuthorizationServer(
            new ClientRepository($container->get('siteUrl')),
            new AccessTokenRepository($connection),
            new ScopeRepository([]),
            'file://' . __DIR__ . '/oauth/private.key',
            'file://' . __DIR__ . '/oauth/public.key',
            new BearerTokenResponse()
        );

        $grants = [
            new PasswordGrant(new UserRepository(), new RefreshTokenRepository($connection)),
            new RefreshTokenGrant(new RefreshTokenRepository($connection)),
        ];

        foreach ($grants as $grant) {
            /** @var $grant \League\OAuth2\Server\Grant\GrantTypeInterface */
            $grant->setRefreshTokenTTL(new \DateInterval('P999Y')); //Refresh token should no expired
            $server->enableGrantType($grant, new \DateInterval('P1D'));
        }

        return $server;
    },
    ResourceServer::class => function (ContainerInterface $container) {
        $server = new ResourceServer(
            new AccessTokenRepository($container->get(Connection::class)),
            'file://' . __DIR__ . '/oauth/public.key'
        );

        return $server;
    },
    Dispatcher::class => function (ContainerInterface $container) {
        $callbacks = require_once __DIR__ . '/callbacks.php';
        return new Dispatcher($callbacks, $container);
    },
    Twig::class => function (ContainerInterface $container) {
        $settings = ($container->get('isDebug')) ? [] : [
            'cache' => $container->get('cache')
        ];

        $view = new Twig($container->get('templates'), $settings);

        $view->addExtension(new TwigExtension($container->get('router'), $container->get('siteUrl')));

        return $view;
    },
    'response' => function (ContainerInterface $container) {
        $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
        $basePath = Uri::createFromEnvironment($container->get('environment'))->getBasePath();
        $response = new Response([
            'status' => 200,
            'headers' => $headers
        ], $basePath);

        return $response->withProtocolVersion($container->get('settings')['httpVersion']);
    }
];
