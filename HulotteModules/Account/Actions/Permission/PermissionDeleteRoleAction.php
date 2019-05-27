<?php

namespace HulotteModules\Account\Actions\Permission;

use Psr\Http\Message\ServerRequestInterface;
use Hulotte\{
    Actions\RouterAwareAction,
    Router,
    Services\Dictionary,
    Session\MessageFlash
};
use HulotteModules\Account\{
    AccountModule,
    Table\RoleTable
};

/**
 * Class PermissionDeleteRoleAction
 *
 * @package HulotteModules\Account\Actions\Permission
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PermissionDeleteRoleAction
{
    use RouterAwareAction;

    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var MessageFlash
     */
    private $messageFlash;

    /**
     * @var RoleTable
     */
    private $roleTable;

    /**
     * @var Router
     */
    private $router;

    /**
     * PermissionDeleteRoleAction constructor
     * @param Dictionary $dictionary
     * @param MessageFlash $messageFlash
     * @param RoleTable $roleTable
     * @param Router $router
     */
    public function __construct(
        Dictionary $dictionary,
        MessageFlash $messageFlash,
        RoleTable $roleTable,
        Router $router
    ) {
        $this->dictionary = $dictionary;
        $this->messageFlash = $messageFlash;
        $this->roleTable = $roleTable;
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $roleId = $request->getAttribute('role');
        $permissionId = $request->getAttribute('permission');

        $this->roleTable->deleteRolePermission($roleId, $permissionId);
        $this->messageFlash->success(
            $this->dictionary->translate('PermissionDeleteRoleAction:deleteSuccess', AccountModule::class)
        );

        return $this->redirect('account.manager.permission');
    }
}
