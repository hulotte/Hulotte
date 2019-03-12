<?php

namespace HulotteModules\Account\Actions\Permission;

use Hulotte\Renderer\RendererInterface;
use HulotteModules\Account\{
    Table\PermissionTable,
    Table\RoleTable
};

/**
 * Class PermissionAction
 *
 * @package HulotteModules\Account\Actions\Permission
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PermissionAction
{
    /**
     * @var PermissionTable
     */
    private $permissionTable;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var RoleTable
     */
    private $roleTable;

    /**
     * LoginAction constructor
     * @param PermissionTable $permissionTable
     * @param RendererInterface $renderer
     * @param RoleTable $roleTable
     */
    public function __construct(PermissionTable $permissionTable, RendererInterface $renderer, RoleTable $roleTable)
    {
        $this->permissionTable = $permissionTable;
        $this->renderer = $renderer;
        $this->roleTable = $roleTable;
    }

    /**
     * @return string
     */
    public function __invoke(): string
    {
        $permissions = $this->permissionTable->all();
        $roles = $this->roleTable->allList('id', 'label');

        return $this->renderer->render('@account/permission/index', compact('permissions', 'roles'));
    }
}
