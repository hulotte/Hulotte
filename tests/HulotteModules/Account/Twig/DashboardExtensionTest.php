<?php

namespace Tests\HulotteModules\Account\Twig;

use PHPUnit\Framework\TestCase;
use Hulotte\Module\WidgetInterface;
use HulotteModules\Account\Twig\DashboardExtension;

/**
 * Class DashboardExtensionTest
 *
 * @package Tests\HulotteModules\Account\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class DashboardExtensionTest extends TestCase
{
    public function testRenderMenu()
    {
        $firstWidgets = $this->createMock(WidgetInterface::class);
        $firstWidgets->method('renderMenu')->willReturn('<li>Test</li>');
        $secondWidgets = $this->createMock(WidgetInterface::class);
        $secondWidgets->method('renderMenu')->willReturn('<li>Test2</li>');
        $widgets = [
            $firstWidgets,
            $secondWidgets
        ];
        $dashboardExtension = new DashboardExtension($widgets);

        $result = $dashboardExtension->renderMenu();

        $this->assertEquals('<li>Test</li><li>Test2</li>', $result);
    }
}
