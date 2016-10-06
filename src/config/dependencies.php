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
use Aigisu\Components\Oauth\ClientRepository;
use Aigisu\Components\Oauth\RefreshTokenRepository;
use Aigisu\Components\Oauth\ScopeRepository;
use Aigisu\Components\Oauth\UserRepository;
use Aigisu\Components\Url\UrlManager;
use Interop\Container\ContainerInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;

return [
    View::class => function (ContainerInterface $container) {
        $callbackManager = new CallbackManager();
        $callbackManager->addClassCallbacks(new UrlExtension($container));
        $callbackManager->addClassCallbacks(new LayoutExtension());
        return new View($container->get('viewPath'), $callbackManager);
    },
    FilesystemManager::class => function () {
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
    AuthorizationServer::class => function () {
        // Setup the authorization server
        $server = new AuthorizationServer(
            new ClientRepository(),                 // instance of ClientRepositoryInterface
            new AccessTokenRepository(),            // instance of AccessTokenRepositoryInterface
            new ScopeRepository(),                  // instance of ScopeRepositoryInterface
            'file://' . __DIR__ . '/oauth/private.key',    // path to private key
            'file://' . __DIR__ . '/oauth/public.key'      // path to public key
        );
        $grant = new PasswordGrant(
            new UserRepository(),           // instance of UserRepositoryInterface
            new RefreshTokenRepository()    // instance of RefreshTokenRepositoryInterface
        );
        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
        // Enable the password grant on the server with a token TTL of 1 hour
        $server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );
        return $server;
    },
];