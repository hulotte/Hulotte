<?php

namespace HulotteModules\Account;

use Psr\Container\ContainerInterface;
use Hulotte\{
    Module,
    Renderer\RendererInterface,
    Renderer\TwigRenderer,
    Router
};
use HulotteModules\Account\{
    Actions\Auth\AccountAction,
    Actions\Auth\AccountPasswordAction,
    Actions\Auth\AccountPasswordUpdateAction,
    Actions\Auth\AccountUpdateAction,
    Actions\Auth\LoginAction,
    Actions\Auth\LoginAttemptAction,
    Actions\Auth\LogoutAction,
    Actions\Dashboard\DashboardAction,
    Actions\Permission\PermissionAction,
    Actions\Permission\PermissionAddRoleAction,
    Actions\Permission\PermissionDeleteRoleAction,
    Twig\DashboardExtension
};

/**
 * Class AccountModule
 *
 * @package HulotteModules\Account
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AccountModule extends Module
{
    /**
     * @var string
     */
    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * @var string
     */
    const DICTIONARY = __DIR__ . '/dictionary/dictionary_';

    /**
     * @var string
     */
    const MIGRATIONS = __DIR__ . '/database/migrations';

    /**
     * @var string
     */
    const SEEDS = __DIR__ . '/database/seeds';

    /**
     * AccountModule constructor
     * @param ContainerInterface $container
     * @param RendererInterface $renderer
     * @param Router $router
     */
    public function __construct(
        ContainerInterface $container,
        RendererInterface $renderer,
        Router $router
    ) {
        // Define view path
        $renderer->addPath('account', __DIR__ . '/views');

        // Auth
        $router->get($container->get('account.auth.login'), LoginAction::class, 'account.auth.login');
        $router->post($container->get('account.auth.login'), LoginAttemptAction::class);
        $router->get($container->get('account.auth.logout'), LogoutAction::class, 'account.auth.logout');
        $router->get($container->get('account.auth.update'), AccountAction::class, 'account.auth.update');
        $router->post($container->get('account.auth.update'), AccountUpdateAction::class);
        $router->get(
            $container->get('account.auth.password.update'),
            AccountPasswordAction::class,
            'account.auth.password.update'
        );
        $router->post($container->get('account.auth.password.update'), AccountPasswordUpdateAction::class);

        // Dashboard
        $router->get($container->get('account.dashboard'), DashboardAction::class, 'account.dashboard');

        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($dashboardExtension);
        }

        // Permission
        $router->get(
            $container->get('account.manager.permission'),
            PermissionAction::class,
            'account.manager.permission'
        );
        $router->post(
            $container->get('account.manager.permission') . '/{id:\d+}',
            PermissionAddRoleAction::class,
            'account.manager.permission.addRole'
        );
        $router->delete(
            $container->get('account.manager.permission') . '/{permission:\d+}/{role:\d+}',
            PermissionDeleteRoleAction::class,
            'account.manager.permission.deleteRole'
        );
    }
}
