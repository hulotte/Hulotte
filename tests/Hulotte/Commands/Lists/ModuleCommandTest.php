<?php

namespace Tests\Hulotte\Commands\Lists;

use Hulotte\Commands\Lists\ModuleCommand;
use PHPUnit\Framework\TestCase;
use Symfony\{
    Component\Console\Application,
    Component\Console\Tester\CommandTester

};

/**
 * Class ModuleCommandTest
 * @package Tests\Hulotte\Commands\Lists
 */
class ModuleCommandTest extends TestCase
{
    private $command;
    private $commandTester;

    public function setUp()
    {
        $application = new Application();
        $application->add(new ModuleCommand());
        $this->command = $application->find('module:create');
        $this->commandTester = new CommandTester($this->command);
    }

    public function tearDown(): void
    {
        if (file_exists('src/Test/TestModule.php')) {
            unlink('src/Test/TestModule.php');
        }

        if (file_exists('src/Test/config.php')) {
            unlink('src/Test/config.php');
        }

        if (file_exists('src/Test/database/migrations')) {
            rmdir('src/Test/database/migrations');
        }

        if (file_exists('src/Test/database/seeds')) {
            rmdir('src/Test/database/seeds');
        }

        if (file_exists('src/Test/database')) {
            rmdir('src/Test/database');
        }

        if (file_exists('src/Test/dictionary/dictionary_fr.toml')) {
            unlink('src/Test/dictionary/dictionary_fr.toml');
        }

        if (file_exists('src/Test/dictionary/dictionary_en.toml')) {
            unlink('src/Test/dictionary/dictionary_en.toml');
        }

        if (file_exists('src/Test/dictionary')) {
            rmdir('src/Test/dictionary');
        }

        if (file_exists('src/Test/Actions/IndexAction.php')) {
            unlink('src/Test/Actions/IndexAction.php');
        }

        if (file_exists('src/Test/Actions')) {
            rmdir('src/Test/Actions');
        }

        if (file_exists('src/Test/views/index.twig')) {
            unlink('src/Test/views/index.twig');
        }

        if (file_exists('src/Test/views')) {
            rmdir('src/Test/views');
        }

        if (file_exists('src/Test')) {
            rmdir('src/Test');
        }

        if (file_exists('src')) {
            rmdir('src');
        }
    }

    public function testExecuteIfModuleAlreadyExists()
    {
        mkdir('src');
        mkdir('src/Test');
        $this->expectException(\Exception::class);
        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'moduleName' => 'test',
        ]);
    }

    public function testExecute()
    {
        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'moduleName' => 'test',
        ]);

        $this->assertFileExists('src');
        $this->assertFileExists('src/Test');
        $this->assertFileExists('src/Test/config.php');
        $this->assertFileExists('src/Test/database');
        $this->assertFileExists('src/Test/database/migrations');
        $this->assertFileExists('src/Test/database/seeds');
        $this->assertFileExists('src/Test/dictionary/dictionary_en.toml');
        $this->assertFileExists('src/Test/dictionary/dictionary_fr.toml');
        $this->assertFileExists('src/Test/TestModule.php');
        $this->assertFileExists('src/Test/Actions');
        $this->assertFileExists('src/Test/Actions/IndexAction.php');
        $this->assertFileExists('src/Test/views');
        $this->assertFileExists('src/Test/views/index.twig');
    }
}
