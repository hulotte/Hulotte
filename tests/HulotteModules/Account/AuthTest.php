<?php

namespace Tests\HulotteModules\Account;

use PHPUnit\Framework\TestCase;
use Hulotte\{
    Services\Dictionary,
    Session\PhpSession,
    Session\SessionInterface
};
use HulotteModules\Account\{
    Auth,
    Entity\UserEntity,
    Exceptions\NoAuthException,
    Table\UserTable
};

/**
 * Class AuthTest
 *
 * @package Tests\HulotteModules\Account
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AuthTest extends TestCase
{
    private $session;
    private $userHash;

    public function setUp()
    {
        $this->userHash = 1 . '---' . password_hash('email@email.comclement', PASSWORD_DEFAULT);
        $this->session = new PhpSession();
        $this->session->set('auth.user', $this->userHash);
        $this->userEntity = $this->createMock(UserEntity::class);
        $this->userEntity->email = 'email@email.com';
        $this->userEntity->name = 'clement';
        $this->userEntity->password = password_hash('test', PASSWORD_DEFAULT);
    }

    public function testGetUser()
    {
        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);
        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertInstanceOf(UserEntity::class, $auth->getUser());
    }

    public function testLogin()
    {
        $userTable = $this->createMock(UserTable::class);
        $userTable->method('findBy')->willReturn($this->userEntity);

        $session = $this->createMock(SessionInterface::class);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $session, $userTable);
        $user = $auth->login('email@email.com', 'test');

        $this->assertInstanceOf(UserEntity::class, $user);
    }

    public function testLogout()
    {
        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);
        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);
        $auth->logout();

        $this->assertNull($this->session->get('auth.user'));
    }

    public function testHasRole()
    {
        $this->userEntity->method('hasRole')
            ->willReturn(true);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertTrue($auth->hasRole('test'));
    }

    public function testHasNoRole()
    {
        $this->userEntity->method('hasRole')
            ->willReturn(false);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertFalse($auth->hasRole('test'));
    }

    public function testHasNoRoleAndNotConnected()
    {
        $this->userEntity->method('hasRole')
            ->willReturn(false);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->willReturn(null);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $session, $userTable);

        $this->expectException(NoAuthException::class);

        $auth->hasRole('test');
    }

    public function testHasPermission()
    {
        $this->userEntity->method('hasPermission')
            ->willReturn(true);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertTrue($auth->hasPermission('test'));
    }

    public function testHasMultiplePermission()
    {
        $this->userEntity->method('hasPermission')
            ->willReturn(true);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertTrue($auth->hasPermission(['test', 'test2']));
    }

    public function testHasNoPermission()
    {
        $this->userEntity->method('hasPermission')
            ->willReturn(false);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertFalse($auth->hasPermission('test'));
    }

    public function testHasNoPermissionAndNotConnected()
    {
        $this->userEntity->method('hasPermission')
            ->willReturn(false);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->willReturn(null);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $session, $userTable);

        $this->expectException(NoAuthException::class);

        $auth->hasPermission('test');
    }
}
