<?php

namespace Tests\HulotteModules\Account;

use PHPUnit\Framework\TestCase;
use Hulotte\{
    Exceptions\NoAuthException,
    Services\Dictionary,
    Session\PhpSession,
    Session\SessionInterface
};
use HulotteModules\Account\{
    Auth,
    Entity\UserEntity,
    Table\UserTable
};

/**
 * Class AuthTest
 *
 * @package Tests\HulotteModules\Account
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \HulotteModules\Account\Auth
 */
class AuthTest extends TestCase
{
    /**
     * @var PhpSession
     */
    private $session;

    /**
     * @var UserEntity
     */
    private $userEntity;

    public function setUp(): void
    {
        $userHash = 1 . '---' . password_hash('email@email.comclement', PASSWORD_DEFAULT);
        $this->session = new PhpSession();
        $this->session->set('auth.user', $userHash);

        $this->userEntity = $this->createMock(UserEntity::class);
        $this->userEntity->email = 'email@email.com';
        $this->userEntity->name = 'clement';
        $this->userEntity->password = password_hash('test', PASSWORD_DEFAULT);
    }

    /**
     * @covers ::getUser
     * @throws NoAuthException
     */
    public function testGetUser(): void
    {
        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);
        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertInstanceOf(UserEntity::class, $auth->getUser());
    }

    /**
     * @covers ::login
     */
    public function testLogin(): void
    {
        $userTable = $this->createMock(UserTable::class);
        $userTable->method('findBy')->willReturn($this->userEntity);

        $session = $this->createMock(SessionInterface::class);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $session, $userTable);
        $user = $auth->login('email@email.com', 'test');

        $this->assertInstanceOf(UserEntity::class, $user);
    }

    /**
     * @covers ::logout
     */
    public function testLogout(): void
    {
        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);
        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);
        $auth->logout();

        $this->assertNull($this->session->get('auth.user'));
    }

    /**
     * @covers ::hasRole
     * @throws NoAuthException
     */
    public function testHasRole(): void
    {
        $this->userEntity->method('hasRole')
            ->willReturn(true);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertTrue($auth->hasRole('test'));
    }

    /**
     * @covers ::hasRole
     * @throws NoAuthException
     */
    public function testHasNoRole(): void
    {
        $this->userEntity->method('hasRole')
            ->willReturn(false);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertFalse($auth->hasRole('test'));
    }

    /**
     * @covers ::hasRole
     * @expectedException NoAuthException
     * @throws NoAuthException
     */
    public function testHasNoRoleAndNotConnected(): void
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

    /**
     * @covers ::hasPermission
     * @throws NoAuthException
     */
    public function testHasPermission(): void
    {
        $this->userEntity->method('hasPermission')
            ->willReturn(true);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertTrue($auth->hasPermission('test'));
    }

    /**
     * @covers ::hasPermission
     * @throws NoAuthException
     */
    public function testHasMultiplePermission(): void
    {
        $this->userEntity->method('hasPermission')
            ->willReturn(true);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertTrue($auth->hasPermission(['test', 'test2']));
    }

    /**
     * @covers ::hasPermission
     * @throws NoAuthException
     */
    public function testHasNoPermission(): void
    {
        $this->userEntity->method('hasPermission')
            ->willReturn(false);

        $userTable = $this->createMock(UserTable::class);
        $userTable->method('find')->willReturn($this->userEntity);

        $dictionary = $this->createMock(Dictionary::class);

        $auth = new Auth($dictionary, $this->session, $userTable);

        $this->assertFalse($auth->hasPermission('test'));
    }

    /**
     * @covers ::hasPermission
     * @expectedException NoAuthException
     * @throws NoAuthException
     */
    public function testHasNoPermissionAndNotConnected(): void
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
