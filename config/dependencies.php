<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:26
 */

use Aigisu\Components\{
    Google\GoogleClientManager, Google\GoogleDriveManager, Imgur\Client, Imgur\Imgur, Mailer, TokenSack
};
use Aigisu\Components\ACL\{
    AccessManager, AdminAccessMiddleware, ModeratorAccessMiddleware, OwnerAccessMiddleware
};
use Aigisu\Components\Validators\{
    ChangeRoleValidator, CreateCGValidator, CreateUnitValidator, CreateUserValidator, PasswordResetRequestValidator, PasswordResetValidator, UpdateCGValidator, UpdateUnitValidator, UpdateUserValidator, ValidatorManager
};
use Aigisu\Core\Response;
use Illuminate\{
    Container\Container as LaravelContainer, Database\Capsule\Manager as CapsuleManager, Database\Connection, Database\Connectors\ConnectionFactory
};
use Interop\Container\ContainerInterface;
use Knlv\Slim\Views\TwigMessages;
use League\Flysystem\{
    Adapter\Local, Filesystem
};
use Slim\{
    Flash\Messages, Http\Uri, Views\Twig, Views\TwigExtension
};

return [
    Connection::class => function (ContainerInterface $container) {
        $factory = new ConnectionFactory(new LaravelContainer());

        return $factory->make($container->get('settings')->get('database'));
    },
    CapsuleManager::class => function (ContainerInterface $container) {
        $database = new CapsuleManager();
        $database->addConnection($container->get('settings')->get('database'));
        $database->setAsGlobal();
        $database->bootEloquent();

        return $database;
    },
    Filesystem::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')->get('flysystem');
        $adapter = new Local($settings['local']);

        return new Filesystem($adapter);
    },
    GoogleDriveManager::class => function (ContainerInterface $container) {

        $config = $container->get('settings')->get('google');
        $client = new GoogleClientManager($container->get(TokenSack::class), $config['client']);
        $drive = new GoogleDriveManager($client, $config['drive']['rootId']);

        return $drive;
    },
    Imgur::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')->get('imgur');
        $client = new Client($container->get(TokenSack::class), $settings['client']['auth']);

        return new Imgur($client, $settings);
    },
    TokenSack::class => function (ContainerInterface $container) {
        return new TokenSack($container->get(Connection::class));
    },
    Twig::class => function (ContainerInterface $container) {
        $siteUrl = $container->get('request')->getUri()->withUserInfo('');
        $settings = $container->get('settings')->get('twig');

        $view = new Twig($settings['templates'], $settings);

        $view->addExtension(new TwigMessages($container->get(Messages::class)));
        $view->addExtension(new TwigExtension($container->get('router'), $siteUrl));

        if ($container->get('debug')) {
            $view->getEnvironment()->addFunction(new Twig_Function('dump', 'dump'));
        }

        return $view;
    },
    'response' => function (ContainerInterface $container) {
        $basePath = Uri::createFromEnvironment($container->get('environment'))->getBasePath();
        $response = new Response($basePath);

        return $response->withProtocolVersion($container->get('settings')->get('httpVersion'))
                        ->withHeader('Content-Type', 'text/html; charset=UTF-8');
    },
    ValidatorManager::class => function (ContainerInterface $container) {
        $access = $container->get('settings')->get('access');

        return new ValidatorManager([
            'user.create' => new CreateUserValidator(),
            'user.update' => new UpdateUserValidator(),
            'user.role' => new ChangeRoleValidator($access),
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
        $params = $container->get('settings')->get('mailer');
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
