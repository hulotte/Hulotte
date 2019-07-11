<?php
namespace Hulotte\Renderer;

use Psr\Container\ContainerInterface;
use Twig\{
    Environment,
    Extension\DebugExtension,
    Loader\FilesystemLoader
};

/**
 * Class TwigRendererFactory
 *
 * @package Hulotte\Renderer
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TwigRendererFactory
{
    /**
     * @param ContainerInterface $container
     * @return TwigRenderer
     */
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $debug = $container->get('env') !== 'prod';
        $viewPath = $container->get('views.path');
        $loader = new FilesystemLoader($viewPath);
        $twig = new Environment($loader, [
            'debug' => $debug,
            'cache' => $debug ? false : 'tmp/views',
            'auto_reload' => $debug
        ]);
        $twig->addExtension(new DebugExtension());

        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        return new TwigRenderer($twig);
    }
}
