<?php
/*
    This file contains the necessary configurations to Hulotte
*/

use \Psr\Container\ContainerInterface;
use function \DI\autowire;
use function \DI\env;
use function \DI\factory;
use function \DI\get;
use Hulotte\{
    Commands\CreateDatabaseCommand,
    Commands\InitCommand,
    Commands\ModuleCommand,
    Middlewares\CsrfMiddleware,
    Middlewares\ForbiddenMiddleware,
    Renderer\RendererInterface,
    Renderer\TwigRendererFactory,
    Router,
    Router\RouterFactory,
    Services\Dictionary,
    Session\PhpSession,
    Session\SessionInterface,
    Twig\CsrfExtension,
    Twig\MessageFlashExtension,
    Twig\FormExtension,
    Twig\PaginatorExtension,
    Twig\ResourceManagerExtension,
    Twig\RouterExtension,
    Twig\TextExtension
};

return [
    // Class definitions
    \PDO::class => function (ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name') . ';charset=utf8',
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    },
    CsrfMiddleware::class => autowire()->constructor(get(SessionInterface::class)),
    Dictionary::class => function (ContainerInterface $c) {
        $locale = $c->get(SessionInterface::class)->get('locale') ?? $c->get('locale');

        return new Dictionary($locale);
    },
    ForbiddenMiddleware::class => autowire()
        ->constructorParameter('loginPath', get('account.auth.login'))
        ->constructorParameter('dashboardPath', get('account.dashboard')),
    RendererInterface::class => factory(TwigRendererFactory::class),
    Router::class => factory(RouterFactory::class),
    SessionInterface::class => autowire(PhpSession::class),

    // Twig extensions
    'twig.extensions' => [
        get(CsrfExtension::class),
        get(MessageFlashExtension::class),
        get(FormExtension::class),
        get(PaginatorExtension::class),
        get(ResourceManagerExtension::class),
        get(RouterExtension::class),
        get(TextExtension::class)
    ],

    // URLs
    'crud.paths.suffix.create' => '/create',
    'crud.paths.suffix.delete' => '/delete',
    'crud.paths.suffix.read' => '/read',
    'crud.paths.suffix.update' => '/update',

    // Variables
    'accepted.locales' => [
        'en', 'fr'
    ],
    'commands' => [
        InitCommand::class,
        ModuleCommand::class,
        CreateDatabaseCommand::class,
    ],
    'account.auth.login' => '/login',
    'account.dashboard' => '/dashboard',
    'database.host' => 'localhost',
    'database.name' => '',
    'database.password' => '',
    'database.username' => 'root',
    'env' => env('PROJECT_ENV', 'prod'),
    'locale' => 'fr',
    'restricted.paths' => []
];
