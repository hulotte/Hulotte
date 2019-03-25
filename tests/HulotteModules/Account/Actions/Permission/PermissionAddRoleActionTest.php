<?php

namespace Tests\HulotteModules\Account\Actions\Permission;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Hulotte\{
    Database\Database,
    Exceptions\NoAuthException,
    Router,
    Services\Dictionary,
    Session\MessageFlash
};
use HulotteModules\Account\{
    Actions\Permission\PermissionAddRoleAction,
    Auth,
    Entity\PermissionEntity,
    Entity\RoleEntity,
    Table\PermissionTable,
    Table\RoleTable
};
use Tests\DatabaseTestCase;

/**
 * Class PermissionAddRoleActionTest
 *
 * @package Tests\HulotteModules\Account\Actions\Permission
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PermissionAddRoleActionTest extends DatabaseTestCase
{
    private $container;
    private $dictionary;
    private $messageFlash;
    private $permissionTable;
    private $roleTable;
    private $router;
    
    public function setUp()
    {
        $statementRole = 'CREATE TABLE role (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255))';
        $statementPermission = 'CREATE TABLE permission (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255))';
        $statementRolePermission = 'CREATE TABLE role_permission '
            . '(role_id INTEGER PRIMARYKEY, permission_id INTEGER PRIMARYKEY)';
        
        $this->getPdo()->exec($statementRole);
        $this->getPdo()->exec($statementPermission);
        $this->getPdo()->exec($statementRolePermission);
        $this->database = new Database($this->getPdo());

        $this->auth = $this->createMock(Auth::class);
        $this->auth->method('hasPermission')->willReturn(true);
        $this->dictionary = $this->createMock(Dictionary::class);
        $this->messageFlash = $this->createMock(MessageFlash::class);
        $this->permissionTable = new PermissionTable(
            $this->getContainer()->reveal(),
            $this->database
        );
        $this->roleTable = new RoleTable(
            $this->getContainer()->reveal(),
            $this->database
        );
        $this->router = $this->createMock(Router::class);
    }

    public function testAddRole()
    {
        $this->insertRolePermission();

        $action = new PermissionAddRoleAction(
            $this->auth,
            $this->dictionary,
            $this->messageFlash,
            $this->permissionTable,
            $this->roleTable,
            $this->router
        );

        $request = (new ServerRequest('POST', '/account-manager/permission'))
            ->withAttribute('id', 1)
            ->withParsedBody(['role' => 2]);

        call_user_func_array($action, [$request]);

        $result = $this->database->query('SELECT * FROM role_permission WHERE role_id = 2 AND permission_id = 1');

        $this->assertCount(1, $result);
    }

    public function testAddRoleWithoutPermission()
    {
        $this->insertRolePermission();

        $this->auth->method('hasPermission')->willThrowException(new NoAuthException);

        $action = new PermissionAddRoleAction(
            $this->auth,
            $this->dictionary,
            $this->messageFlash,
            $this->permissionTable,
            $this->roleTable,
            $this->router
        );

        $request = (new ServerRequest('POST', '/account-manager/permission'))
            ->withAttribute('id', 1)
            ->withParsedBody(['role' => 2]);

        $this->expectException(NoAuthException::class);

        call_user_func_array($action, [$request]);
    }

    public function testAddRoleFailRelationAlreadyExists()
    {
        $this->insertRolePermission();

        $action = new PermissionAddRoleAction(
            $this->auth,
            $this->dictionary,
            $this->messageFlash,
            $this->permissionTable,
            $this->roleTable,
            $this->router
        );

        $request = (new ServerRequest('POST', '/account-manager/permission'))
            ->withAttribute('id', 1)
            ->withParsedBody(['role' => 1]);
        ;

        $this->messageFlash->expects($this->once())->method('error');

        call_user_func_array($action, [$request]);
    }

    public function testAddRoleFailRoleNotExists()
    {
        $this->insertRolePermission();

        $action = new PermissionAddRoleAction(
            $this->auth,
            $this->dictionary,
            $this->messageFlash,
            $this->permissionTable,
            $this->roleTable,
            $this->router
        );

        $request = (new ServerRequest('POST', '/account-manager/permission'))
            ->withAttribute('id', 1)
            ->withParsedBody(['role' => 4]);
        ;

        $this->messageFlash->expects($this->once())->method('error');

        call_user_func_array($action, [$request]);
    }

    private function getContainer()
    {
        if (!$this->container) {
            $this->container = $this->prophesize(ContainerInterface::class);
            $this->container->get(PermissionEntity::class)
                ->willReturn(new PermissionEntity(
                    $this->createMock(RoleTable::class)
                ));
            $this->container->get(RoleEntity::class)
                ->willReturn(new RoleEntity());
        }
       
        return $this->container;
    }

    private function insertRolePermission()
    {
        $statementRole = 'INSERT INTO role(id, label) VALUES (1, "first role"), (2, "second role")';
        $statementPermission = 'INSERT INTO permission(id, label) VALUES (1, "first permission")';
        $statementRolePermission = 'INSERT INTO role_permission(role_id, permission_id) VALUES (1, 1)';
        $this->database->query($statementRole);
        $this->database->query($statementPermission);
        $this->database->query($statementRolePermission);
    }
}
