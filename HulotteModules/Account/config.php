<?php

use function DI\add;
use function DI\autowire;
use function DI\get;
use Hulotte\Auth\AuthInterface;
use HulotteModules\Account\{
    Auth,
    Actions\Dashboard\DashboardAction,
    Middlewares\ForbiddenMiddleware,
    Widgets\AccountWidget,
    Widgets\PermissionWidget,
    Twig\DashboardExtension,
    Twig\AuthExtension
};

return [
    // Class definitions
    AuthInterface::class => autowire(Auth::class),
    DashboardAction::class => autowire()->constructorParameter('widgets', get('account.dashboard.widgets')),
    DashboardExtension::class => autowire()->constructor(get('account.dashboard.widgets')),

    // Permissions
    'restricted.paths' => add([
        get('account.auth.logout'),
        get('account.auth.update'),
        get('account.dashboard'),
        'accessPermissionManager' => get('account.manager.permission')
    ]),

    // Twig extensions
    'twig.extensions' => add([
        get(AuthExtension::class),
    ]),

    // URLs
    'account.auth.password.update' => '/account/password',
    'account.auth.login' => '/login',
    'account.auth.logout' => '/logout',
    'account.auth.update' => '/account',
    'account.dashboard' => '/dashboard',
    'account.manager.permission' => '/account-manager/permission',

    // Variables
    'account.dashboard.widgets' => add([
        get(AccountWidget::class),
        get(PermissionWidget::class)
    ])
];
