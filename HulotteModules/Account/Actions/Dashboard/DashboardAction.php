<?php

namespace HulotteModules\Account\Actions\Dashboard;

use Hulotte\{
    Module\WidgetInterface,
    Renderer\RendererInterface
};

/**
 * Class DashboardAction
 *
 * @package HulotteModules\Account\Actions\Dashboard
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class DashboardAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    
    /**
     * @var array
     */
    private $widgets;

    /**
     * LoginAction constructor
     * @param RendererInterface $renderer
     * @param array $widgets
     */
    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    /**
     * @return string
     */
    public function __invoke(): string
    {
        $widgets = array_reduce(
            $this->widgets,
            function (string $html, WidgetInterface $widget) {
                return $html . $widget->render();
            },
            ''
        );
        
        return $this->renderer->render('@account/dashboard/index', compact('widgets'));
    }
}
