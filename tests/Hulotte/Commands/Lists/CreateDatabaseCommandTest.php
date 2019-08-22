<?php

namespace Tests\Hulotte\Commands\Lists;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\{
    Application,
    Tester\CommandTester
};
use Hulotte\{
    Commands\Lists\CreateDatabaseCommand,
    Database\Database
};
use Psr\Container\ContainerInterface;

/**
 * Class CreateDatabaseCommandTest
 * @package Tests\Hulotte\Commands\Lists
 */
class CreateDatabaseCommandTest extends TestCase
{
    private $command;
    private $commandTester;

    public function setUp()
    {
        $application = new Application();
        $application->add(new CreateDatabaseCommand());
        $this->command = $application->find('database:create');
    }

    public function testExecuteWithArgument()
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())
            ->method('query');

        $container = $this->createContainer();
        $container->expects($this->never())->method('get');

        $this->createCommandTester($database, $container);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'databaseName' => 'test',
        ]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Database test is created\r\n", $output);
    }

    public function testExecuteWithoutArgument()
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->never())
            ->method('query');

        $container = $this->createContainer();
        $container->expects($this->never())->method('get');

        $this->createCommandTester($database, $container);

        $this->commandTester->execute([
            'command' => $this->command->getName()
        ]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("No database name specified !\r\n", $output);
    }

    public function testExecuteWithContainer()
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())->method('query');

        $container = $this->createContainer(true);
        $container->expects($this->once())->method('get');

        $this->createCommandTester($database, $container);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Database test is created\r\n", $output);
    }

    private function createCommandTester($database, $container)
    {
        $this->command->setDatabase($database);

        if ($container) {
            $this->command->setContainer($container);
        }

        $this->commandTester = new CommandTester($this->command);
    }

    private function createContainer($ifMethodHas = false)
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn($ifMethodHas);
        $container->method('get')->willReturn('test');

        return $container;
    }
}
