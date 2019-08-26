<?php

namespace Hulotte\Commands\Lists;

use Symfony\Component\Console\{Application,
    Command\Command,
    Input\ArrayInput,
    Input\InputInterface,
    Output\OutputInterface,
    Question\Question};

/**
 * Class InitCommand
 *
 * @package Hulotte\Commands\Lists
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class InitCommand extends Command
{
    /**
     * @var array
     */
    private $databaseConfig = [];

    /**
     * Configures the current command
     */
    protected function configure(): void
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
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $output->writeln('<question>----------------------------------------------</question>');
        $output->writeln('<question>| Welcome to the project initialization tool |</question>');
        $output->writeln('<question>----------------------------------------------</question>');
        $output->writeln('');

        // Database
        $databaseQuestion = new Question('Do you want to create a database ? <comment>[no]</comment> ', 'no');
        $isCreateDatabase = $helper->ask($input, $output, $databaseQuestion);

        $output->writeln('');

        if ($isCreateDatabase === 'yes') {
            $this->callCreateDatabaseCommand($output);
            $output->writeln('');

            $phinxQuestion = new Question(
                'Do you want to use Phinx library for migrations ? <comment>[yes]</comment> ',
                'yes'
            );
            $isPhinx = $helper->ask($input, $output, $phinxQuestion);

            if ($isPhinx === 'yes') {
                $this->createPhinxFile($output);
            }
        }

        // Project default folders and files
        $output->writeln('');
        $output->writeln('<info>Creating default folders and files... </info>');
        $output->writeln('');

        $this->createHtaccessFile($output);
        $this->createPublicFolderAndIndexFile($output);
        $this->createTmpFolder($output);
        $this->createSrcFolder($output);

        // Creating App module
        $this->callCreateModuleCommand($output);

        $output->writeln('');
        $output->writeln('<info>Your project is initialized</info>');
    }

    /**
     * Call create database command and save database config
     * @param OutputInterface $output
     * @throws \Throwable
     */
    private function callCreateDatabaseCommand(OutputInterface $output): void
    {
        $application = new Application();
        $application->add(new CreateDatabaseCommand());
        $input = new ArrayInput(['command' => 'database:create']);
        $application->doRun($input, $output);
        /** @var CreateDatabaseCommand $command */
        $command = $application->get('database:create');

        $this->databaseConfig = [
            'host' => $command->getDatabaseHost(),
            'name' => $command->getDatabaseName(),
            'userName' => $command->getDatabaseUserName(),
            'password' => $command->getDatabasePassword(),
        ];
    }

    /**
     * Call create module command
     * @param OutputInterface $output
     * @throws \Throwable
     */
    private function callCreateModuleCommand(OutputInterface $output): void
    {
        $application = new Application();
        $moduleCommand = new ModuleCommand();
        $moduleCommand->setDatabaseConfig($this->databaseConfig);
        $application->add($moduleCommand);
        $input = new ArrayInput(['command' => 'module:create', 'moduleName' => 'app']);
        $application->doRun($input, $output);
    }

    /**
     * Create .htaccess file
     * @param OutputInterface $output
     */
    private function createHtaccessFile(OutputInterface $output): void
    {
        if (!file_exists('.htaccess')) {
            $indexFile = fopen('.htaccess', 'a+');
            $content = require dirname(__DIR__) . '/templates/htaccess.php';
            fputs($indexFile, $content);
            fclose($indexFile);
            $output->writeln('.htaccess file is created');
        }
    }

    /**
     * Create phinx.php config file with database config
     * @param OutputInterface $output
     */
    private function createPhinxFile(OutputInterface $output): void
    {
        if (!file_exists('phinx.php')) {
            $phinxFile = fopen('phinx.php', 'a+');
            $content = require dirname(__DIR__) . '/templates/phinx.php';
            $content = str_replace('%DATABASE_HOST%', $this->databaseConfig['host'], $content);
            $content = str_replace('%DATABASE_NAME%', $this->databaseConfig['name'], $content);
            $content = str_replace('%DATABASE_USERNAME%', $this->databaseConfig['userName'], $content);
            $content = str_replace('%DATABASE_PASSWORD%', $this->databaseConfig['password'], $content);
            fputs($phinxFile, $content);
            fclose($phinxFile);
            $output->writeln('phinx.php file is created');
        }
    }

    /**
     * Create public folder and index.php file
     * @param OutputInterface $output
     */
    public function createPublicFolderAndIndexFile(OutputInterface $output): void
    {
        if (!file_exists('public')) {
            mkdir('public');
            $output->writeln('public folder is created');
        }

        if (!file_exists('public/index.php')) {
            $indexFile = fopen('public/index.php', 'a+');
            $content = require dirname(__DIR__) . '/templates/index.php';
            fputs($indexFile, $content);
            fclose($indexFile);
            $output->writeln('index.php file is created');
        }
    }

    /**
     * Create src folder
     * @param OutputInterface $output
     */
    private function createSrcFolder(OutputInterface $output): void
    {
        if (!file_exists('src')) {
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
            mkdir('tmp');
            $output->writeln('tmp folder is created');
        }
    }
}
