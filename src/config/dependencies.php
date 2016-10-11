<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:26
 */
use Aigisu\Common\Components\View\CallbackManager;
use Aigisu\Common\Components\View\LayoutExtension;
use Aigisu\Common\Components\View\UrlExtension;
use Aigisu\Common\Components\View\View;
use Aigisu\Components\Google\GoogleDriveFilesystem;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Aigisu\Components\Imgur\Imgur;
use Aigisu\Components\Oauth\AccessTokenRepository;
use Aigisu\Components\Oauth\BearerTokenResponse;
use Aigisu\Components\Oauth\ClientRepository;
use Aigisu\Components\Oauth\RefreshTokenRepository;
use Aigisu\Components\Oauth\ScopeRepository;
use Aigisu\Components\Oauth\UserRepository;
use Aigisu\Components\Url\UrlManager;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Events\Dispatcher;
use Interop\Container\ContainerInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;

return [
    Connection::class => function (ContainerInterface $container) {
        $factory = new ConnectionFactory(new LaravelContainer());
        return $factory->make($container->get('database'));
    },
    CapsuleManager::class => function (ContainerInterface $container) {
        $database = new CapsuleManager();
        $database->addConnection($container->get('database'));
        $database->setEventDispatcher(new Dispatcher(new LaravelContainer()));
        return $database;
    },
    View::class => function (ContainerInterface $container) {
        $callbackManager = new CallbackManager();
        $callbackManager->addClassCallbacks(new UrlExtension($container));
        $callbackManager->addClassCallbacks(new LayoutExtension());
        return new View($container->get('viewPath'), $callbackManager);
    },
    FilesystemManager::class => function (ContainerInterface $container) {
        return new FilesystemManager(require __DIR__ . '/filesystems.php');
    },
    UrlManager::class => function (ContainerInterface $container) {
        return new UrlManager($container->get('router'), $container->get('siteUrl'));
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
];