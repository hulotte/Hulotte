<?php

return <<< 'EOD'
<?php

namespace %MODULE_NAME%;

use Psr\Container\ContainerInterface;
use Hulotte\{
    Module,
    Renderer\RendererInterface,
    Router
};

/**
 * Class %MODULE_NAME%Module
 *
 * @package %MODULE_NAME%
 */
class %MODULE_NAME%Module extends Module
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
     * %MODULE_NAME%Module constructor
     * @param ContainerInterface $container
     * @param Router $router
     * @param RendererInterface $renderer
     */
    public function __construct(ContainerInterface $container, Router $router, RendererInterface $renderer)
    {
        // Define view path
        $renderer->addPath('app', $container->get('views.path'));
    }
}

EOD;
