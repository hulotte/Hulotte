<?php

namespace HulotteModules\Account\Widgets;

use Hulotte\{
    Module\WidgetInterface,
    Renderer\RendererInterface
};

/**
 * Class AccountWidget
 * @package HulotteModules\Account\Widgets
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AccountWidget implements WidgetInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * BlogWidget constructor
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Render the dashboard
     * @return null|string
     */
    public function render(): ?string
    {
        return $this->renderer->render('@account/manager/accountWidget');
    }

    /**
     * Render the dashboard menu
     * @return null|string
     */
    public function renderMenu(): ?string
    {
        return $this->renderer->render('@account/manager/accountMenu');
    }
}
