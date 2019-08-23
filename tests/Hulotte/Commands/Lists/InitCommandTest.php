<?php

namespace Tests\Hulotte\Commands\Lists;

use Hulotte\Commands\Lists\InitCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class InitCommandTest
 * @package Tests\Hulotte\Commands\Lists
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class InitCommandTest extends TestCase
{
    private $command;
    private $commandTester;

    public function setUp()
    {
        $application = new Application();
        $application->add(new InitCommand());
        $this->command = $application->find('init');
        $this->commandTester = new CommandTester($this->command);
    }

    public function tearDown(): void
    {
        if (file_exists('.htaccess')) {
            unlink('.htaccess');
        }

        if (file_exists('public/index.php')) {
            unlink('public/index.php');
            rmdir('public');
        }

        if (file_exists('tmp')) {
            rmdir('tmp');
        }

        if (file_exists('src/App/AppModule.php')) {
            unlink('src/App/AppModule.php');
        }

        if (file_exists('src/App/config.php')) {
            unlink('src/App/config.php');
        }

        if (file_exists('src/App/database/migrations')) {
            rmdir('src/App/database/migrations');
        }

        if (file_exists('src/App/database/seeds')) {
            rmdir('src/App/database/seeds');
        }

        if (file_exists('src/App/database')) {
            rmdir('src/App/database');
        }

        if (file_exists('src/App/dictionary/dictionary_fr.toml')) {
            unlink('src/App/dictionary/dictionary_fr.toml');
        }

        if (file_exists('src/App/dictionary/dictionary_en.toml')) {
            unlink('src/App/dictionary/dictionary_en.toml');
        }

        if (file_exists('src/App/dictionary')) {
            rmdir('src/App/dictionary');
        }

        if (file_exists('src/App/Actions/IndexAction.php')) {
            unlink('src/App/Actions/IndexAction.php');
        }

        if (file_exists('src/App/Actions')) {
            rmdir('src/App/Actions');
        }

        if (file_exists('src/App/views/index.twig')) {
            unlink('src/App/views/index.twig');
        }

        if (file_exists('src/App/views')) {
            rmdir('src/App/views');
        }

        if (file_exists('src/App')) {
            rmdir('src/App');
        }

        if (file_exists('src')) {
            rmdir('src');
        }
    }

    public function testExecute()
    {
        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute(['command' => $this->command->getName()]);

        $this->assertFileExists('.htaccess');
        $this->assertFileExists('public/index.php');
        $this->assertFileExists('tmp');
        $this->assertFileExists('src');
    }
}
