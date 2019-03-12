<?php

namespace HulotteModules\Account\Twig;

use Hulotte\Module\WidgetInterface;

/**
 * Class DashboardExtension
 *
 * @package HulotteModules\Account\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class DashboardExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $widgets;

    /**
     * DashboardExtension constructor
     * @param array $widgets
     */
    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('dashboard_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Render the dashboard menu
     * @return string
     */
    public function renderMenu(): string
    {
        return array_reduce(
            $this->widgets,
            function (string $html, WidgetInterface $widget) {
                return $html . $widget->renderMenu();
            },
            ''
        );
    }
}
