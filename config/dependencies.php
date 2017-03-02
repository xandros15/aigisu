<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:26
 */
use Aigisu\Components\ACL\AccessManager;
use Aigisu\Components\ACL\AdminAccessMiddleware;
use Aigisu\Components\ACL\ModeratorAccessMiddleware;
use Aigisu\Components\ACL\OwnerAccessMiddleware;
use Aigisu\Components\Google\GoogleDriveFilesystem;
use Aigisu\Components\Imgur\Client;
use Aigisu\Components\Imgur\Imgur;
use Aigisu\Components\Mailer;
use Aigisu\Components\TokenSack;
use Aigisu\Components\Validators\ChangeRoleValidator;
use Aigisu\Components\Validators\CreateCGValidator;
use Aigisu\Components\Validators\CreateUnitValidator;
use Aigisu\Components\Validators\CreateUserValidator;
use Aigisu\Components\Validators\PasswordResetRequestValidator;
use Aigisu\Components\Validators\PasswordResetValidator;
use Aigisu\Components\Validators\UpdateCGValidator;
use Aigisu\Components\Validators\UpdateUnitValidator;
use Aigisu\Components\Validators\UpdateUserValidator;
use Aigisu\Components\Validators\ValidatorManager;
use Aigisu\Core\Response;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Interop\Container\ContainerInterface;
use Knlv\Slim\Views\TwigMessages;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Slim\Flash\Messages;
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
        $database->setAsGlobal();
        $database->bootEloquent();
        return $database;
    },
    Filesystem::class => function (ContainerInterface $container) {
        $adapter = new Local($container->get('upload'));
        return new Filesystem($adapter);
    },
    GoogleDriveFilesystem::class => function (ContainerInterface $container) {
        $settings = require __DIR__ . '/google.php';
        $settings['client'][TokenSack::class] = $container->get(TokenSack::class);
        return new GoogleDriveFilesystem($settings);
    },
    Imgur::class => function (ContainerInterface $container) {
        $settings = $container->get('imgur.settings');
        $client = new Client($container->get(TokenSack::class), $settings['client']['auth']);
        return new Imgur($client, $settings);
    },
    TokenSack::class => function (ContainerInterface $container) {
        return new TokenSack($container->get(Connection::class));
    },
    Twig::class => function (ContainerInterface $container) {
        $settings = ($container->get('isDebug')) ? [] : [
            'cache' => $container->get('cache')
        ];

        $view = new Twig($container->get('templates'), $settings);

        $view->addExtension(new TwigMessages($container->get(Messages::class)));
        $view->addExtension(new TwigExtension($container->get('router'), $container->get('siteUrl')));

        if ($container->get('isDebug')) {
            $view->getEnvironment()->addFunction(new Twig_Function('dump', 'dump'));
        }

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
    },
    ValidatorManager::class => function (ContainerInterface $container) {
        return new ValidatorManager([
            'user.create' => new CreateUserValidator(),
            'user.update' => new UpdateUserValidator(),
            'user.role' => new ChangeRoleValidator($container->get('access')),
            'user.password.reset' => new PasswordResetValidator(),
            'user.password.reset.request' => new PasswordResetRequestValidator(),
            'unit.create' => new CreateUnitValidator(),
            'unit.update' => new UpdateUnitValidator(),
            'cg.create' => new CreateCGValidator(),
            'cg.update' => new UpdateCGValidator(),
        ]);
    },
    AccessManager::class => function (ContainerInterface $container) {
        return new AccessManager([
            'moderator' => new ModeratorAccessMiddleware($container),
            'admin' => new AdminAccessMiddleware($container),
            'owner' => new OwnerAccessMiddleware($container),
        ]);
    },
    Messages::class => function () {
        return new Messages();
    },
    Mailer::class => function (ContainerInterface $container) {
        $params = require __DIR__ . '/mailer/mailer.php';

        $transporter = new Swift_SmtpTransport($params['host'], $params['port'], $params['encryption']);
        $transporter->setUsername($params['username']);
        $transporter->setPassword($params['password']);

        $twig = $container->get(Twig::class)->getEnvironment();
        $swift = new Swift_Mailer($transporter);

        $mailer = new Mailer($swift, $twig, [
            'from' => $params['username'],
            'prefix' => $container->get('app.name'),
        ]);

        return $mailer;
    },
];
