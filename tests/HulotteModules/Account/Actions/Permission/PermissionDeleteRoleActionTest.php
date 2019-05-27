<?php

namespace Tests\HulotteModules\Account\Actions\Permission;

use GuzzleHttp\Psr7\ServerRequest;
use Hulotte\{
    Database\Database,
    Services\Dictionary,
    Session\MessageFlash,
    Router
};
use HulotteModules\Account\{
    Actions\Permission\PermissionDeleteRoleAction,
    Table\RoleTable
};
use Tests\DatabaseTestCase;

/**
 * Class PermissionDeleteRoleActionTest
 *
 * @package Tests\HulotteModules\Account\Actions\Permission
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \HulotteModules\Account\Actions\Permission\PermissionDeleteRoleAction
 */
class PermissionDeleteRoleActionTest extends DatabaseTestCase
{
    public function testDeleteRole(): void
    {
        $statementRole = 'CREATE TABLE role (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255))';
        $statementPermission = 'CREATE TABLE permission (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255))';
        $statementRolePermission = 'CREATE TABLE role_permission '
            . '(role_id INTEGER PRIMARYKEY, permission_id INTEGER PRIMARYKEY)';
        
        $this->getPdo()->exec($statementRole);
        $this->getPdo()->exec($statementPermission);
        $this->getPdo()->exec($statementRolePermission);
        $this->database = new Database($this->getPdo());

        $statementRole = 'INSERT INTO role(id, label) VALUES (1, "first role"), (2, "second role")';
        $statementPermission = 'INSERT INTO permission(id, label) VALUES (1, "first permission")';
        $statementRolePermission = 'INSERT INTO role_permission(role_id, permission_id) VALUES (1, 1)';
        $this->database->query($statementRole);
        $this->database->query($statementPermission);
        $this->database->query($statementRolePermission);

        $dictionary = $this->createMock(Dictionary::class);
        $messageFlash = $this->createMock(MessageFlash::class);
        $roleTable = $this->createMock(RoleTable::class);
        $router = $this->createMock(Router::class);

        $action = new PermissionDeleteRoleAction($dictionary, $messageFlash, $roleTable, $router);

        $request = (new ServerRequest('POST', '/account-manager/permission'))
            ->withAttribute('permission', 1)
            ->withAttribute('role', 1);

        call_user_func_array($action, [$request]);

        $result = $this->database->query('SELECT * FROM role_permission WHERE role_id = 2 AND permission_id = 1');

        $this->assertCount(0, $result);
    }
}
