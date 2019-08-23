<?php

namespace Tests\Hulotte\Commands\Lists;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\{
    Application,
    Tester\CommandTester
};
use Hulotte\{Commands\Lists\CreateDatabaseCommand, Database\Database};

/**
 * Class CreateDatabaseCommandTest
 * @package Tests\Hulotte\Commands\Lists
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
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

    public function testExecuteWithoutDatabaseName()
    {
        $this->commandTester = new CommandTester($this->command);

        $this->expectException(\Exception::class);
        $this->commandTester->setInputs(['localhost', '', 'root', '']);
        $this->commandTester->execute(['command' => $this->command->getName()]);
    }

    public function testExecuteWithDatabaseName()
    {
        $database = $this->createMock(Database::class);
        $database->expects($this->once())->method('query');
        $this->command->setDatabase($database);

        $this->commandTester = new CommandTester($this->command);

        $this->commandTester->setInputs(['localhost', 'test', 'root', '']);

        $this->commandTester->execute(['command' => $this->command->getName()]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Database test is created", $output);
    }
}
