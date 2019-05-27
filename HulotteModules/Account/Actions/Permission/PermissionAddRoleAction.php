<?php

namespace HulotteModules\Account\Actions\Permission;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Hulotte\{
    Actions\RouterAwareAction,
    Router,
    Services\Dictionary,
    Services\Validator,
    Session\MessageFlash
};
use HulotteModules\Account\{
    Auth,
    AccountModule,
    Table\PermissionTable,
    Table\RoleTable
};

/**
 * Class PermissionAddRoleAction
 *
 * @package HulotteModules\Account\Actions\Permission
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PermissionAddRoleAction
{
    use RouterAwareAction;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var MessageFlash
     */
    private $messageFlash;

    /**
     * @var PermissionTable
     */
    private $permissionTable;

    /**
     * @var RoleTable
     */
    private $roleTable;

    /**
     * @var Router
     */
    private $router;

    /**
     * PermissionAddRoleAction constructor
     * @param Auth $auth
     * @param Dictionary $dictionary
     * @param MessageFlash $messageFlash
     * @param PermissionTable $permissionTable
     * @param RoleTable $roleTable
     * @param Router $router
     */
    public function __construct(
        Auth $auth,
        Dictionary $dictionary,
        MessageFlash $messageFlash,
        PermissionTable $permissionTable,
        RoleTable $roleTable,
        Router $router
    ) {
        $this->auth = $auth;
        $this->dictionary = $dictionary;
        $this->messageFlash = $messageFlash;
        $this->permissionTable = $permissionTable;
        $this->roleTable = $roleTable;
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if (!$this->auth->hasPermission('accessPermissionManager')) {
            $this->messageFlash->error(
                $this->dictionary->translate('ForbiddenMiddleware:errorNotPermission', AccountModule::class)
            );

            return $this->redirect('account.dashboard');
        }

        $params = $request->getParsedBody();
        $permissionId = $request->getAttribute('id');

        if (!$this->permissionTable->isExists($permissionId)) {
            $this->messageFlash->error(
                $this->dictionary->translate('PermissionAddRoleAction:notExists', AccountModule::class)
            );

            return $this->redirect('account.manager.permission');
        }

        $validator = (new Validator($this->dictionary, $params))
            ->required('role')
            ->exists(['id', 'role'], $this->roleTable);

        if (!$validator->isValid()) {
            $this->messageFlash->error($validator->getErrors()['role']);

            return $this->redirect('account.manager.permission');
        }

        if ($this->roleTable->isRolePermissionExists($params['role'], $permissionId)) {
            $this->messageFlash->error(
                $this->dictionary->translate('PermissionAddRoleAction:rolePermissionExists', AccountModule::class)
            );

            return $this->redirect('account.manager.permission');
        }

        $this->roleTable->createRolePermission($params['role'], $permissionId);
        $this->messageFlash->success(
            $this->dictionary->translate('PermissionAddRoleAction:associationSuccess', AccountModule::class)
        );

        return $this->redirect('account.manager.permission');
    }
}
