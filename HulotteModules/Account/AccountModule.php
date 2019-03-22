<?php

namespace HulotteModules\Account;

use Psr\Container\ContainerInterface;
use Hulotte\{
    Module,
    Renderer\RendererInterface,
    Router
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
    }
}
