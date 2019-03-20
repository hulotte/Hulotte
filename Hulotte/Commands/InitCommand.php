<?php

namespace Hulotte\Commands;

use Symfony\Component\Console\{
    Application,
    Command\Command,
    Input\ArrayInput,
    Input\InputInterface,
    Output\NullOutput,
    Output\OutputInterface
};
use Hulotte\Commands\ModuleCommand;

/**
 * Class InitCommand
 *
 * @package Hulotte\Commands
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class InitCommand extends Command
{
    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize project')
            ->setHelp('Create basic folders and files for a web project');
    }

    /**
     * Executes the current command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->createHtaccessFile($output);
        $this->createTmpFolder($output);
        $this->createPublicFolder($output);
        $this->createIndexFile($output);
        $this->createSrcFolder($output);
        
        // Call module command to create App module
        $output->writeln('Creating App module');
        $application = new Application();
        $application->add(new ModuleCommand());
        $args = ['command' => 'module:create', 'moduleName' => 'app'];
        $input = new ArrayInput($args);
        $output = new NullOutput();
        $application->doRun($input, $output);
        $output->writeln('App module is created');
        $output->writeln('--------------------');
        $output->writeln('Your project is initialized');
    }

    /**
     * Create .htaccess file
     * @param OutputInterface $output
     */
    private function createHtaccessFile(OutputInterface $output): void
    {
        if (!file_exists('.htaccess')) {
            $output->writeln('Creating htaccess file');
            $indexFile = fopen('.htaccess', 'a+');
            $content = require __DIR__ . '/templates/htaccess.php';
            fputs($indexFile, $content);
            fclose($indexFile);
            $output->writeln('htaccess file is created');
        }
    }

    /**
     * Create index file
     * @param OutputInterface $output
     */
    private function createIndexFile(OutputInterface $output): void
    {
        if (!file_exists('public/index.php')) {
            $output->writeln('Creating index.php file');
            $indexFile = fopen('public/index.php', 'a+');
            $content = require __DIR__ . '/templates/index.php';
            fputs($indexFile, $content);
            fclose($indexFile);
            $output->writeln('index.php file is created');
        }
    }

    /**
     * Create public folder
     * @param OutputInterface $output
     */
    private function createPublicFolder(OutputInterface $output):void
    {
        if (!file_exists('public')) {
            $output->writeln('Creating public folder');
            mkdir('public');
            $output->writeln('Public folder is created');
        }
    }

    /**
     * Create src folder
     * @param OutputInterface $output
     */
    private function createSrcFolder(OutputInterface $output):void
    {
        if (!file_exists('src')) {
            $output->writeln('Creating src folder');
            mkdir('src');
            $output->writeln('src folder is created');
        }
    }

    /**
     * Create temporary folder
     * @param OutputInterface $output
     */
    private function createTmpFolder(OutputInterface $output): void
    {
        if (!file_exists('tmp')) {
            $output->writeln('Creating tmp folder');
            mkdir('tmp');
            $output->writeln('tmp folder is created');
        }
    }
}
