<?php

namespace HulotteModules\Account\Widgets;

use Hulotte\{
    Module\WidgetInterface,
    Renderer\RendererInterface
};
use HulotteModules\Account\Auth;

/**
 * Class PermissionWidget
 * @package HulotteModules\Account\Widgets
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PermissionWidget implements WidgetInterface
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * UserWidget constructor
     * @param Auth $auth
     * @param RendererInterface $renderer
     */
    public function __construct(Auth $auth, RendererInterface $renderer)
    {
        $this->auth = $auth;
        $this->renderer = $renderer;
    }

    /**
     * Render the dashboard
     * @return null|string
     * @throws \HulotteModules\Account\Exceptions\NoAuthException
     */
    public function render(): ?string
    {
        if ($this->auth->hasPermission('accessPermissionManager')) {
            return $this->renderer->render('@account/manager/permissionWidget');
        }

        return null;
    }

    /**
     * Render the dashboard menu
     * @return null|string
     * @throws \HulotteModules\Account\Exceptions\NoAuthException
     */
    public function renderMenu(): ?string
    {
        if ($this->auth->hasPermission('accessPermissionManager')) {
            return $this->renderer->render('@account/manager/permissionMenu');
        }

        return null;
    }
}
