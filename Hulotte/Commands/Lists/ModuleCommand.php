<?php

namespace Hulotte\Commands\Lists;

use Symfony\Component\Console\{Command\Command, Input\InputArgument, Input\InputInterface, Output\OutputInterface};

/**
 * Class ModuleCommand
 *
 * @package Hulotte\Commands\Lists
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ModuleCommand extends Command
{
    /**
     * @var array
     */
    private $databaseConfig = [];

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var string
     */
    private $modulePath;

    /**
     * @param array $databaseConfig
     */
    public function setDatabaseConfig(array $databaseConfig): void
    {
        $this->databaseConfig = $databaseConfig;
    }

    /**
     * Configures the current command
     */
    protected function configure(): void
    {
        $this
            ->setName('module:create')
            ->setDescription('Create new module')
            ->setHelp('Create basic folders and files for a new module')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'The name of the module.');
    }

    /**
     * Executes the current command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->moduleName = ucfirst($input->getArgument('moduleName'));
        $this->modulePath = 'src/' . $this->moduleName;

        // Verify if module already exists
        if (file_exists($this->modulePath)) {
            throw new \Exception('This Module already exists !');
        }

        // Create src folder if not exists
        if (!file_exists('src')) {
            mkdir('src');
        }

        $this->createModuleFolder();
        $this->createConfigFile();
        $this->createDatabaseFolders();
        $this->createDictionaryFiles();
        $this->createModuleController();
        $this->createActionsFolder();
        $this->createIndexActionFile();
        $this->createViewsFolder();
        $this->createIndexView();

        $output->writeln('');
        $output->writeln('<info>Module ' . $this->moduleName . ' is created</info>');
    }

    /**
     * Create folder for Actions
     */
    private function createActionsFolder(): void
    {
        if (!file_exists($this->modulePath . '/Actions')) {
            mkdir($this->modulePath . '/Actions');
        }
    }

    /**
     * Create Config file
     */
    private function createConfigFile(): void
    {
        $config = fopen($this->modulePath . '/config.php', 'a+');
        $content = require dirname(__DIR__) . '/templates/config.php';
        $content = str_replace('%DATABASE_HOST%', $this->getConfigFromDatabase('host'), $content);
        $content = str_replace('%DATABASE_NAME%', $this->getConfigFromDatabase('name'), $content);
        $content = str_replace('%DATABASE_PASSWORD%', $this->getConfigFromDatabase('password'), $content);
        $content = str_replace('%DATABASE_USERNAME%', $this->getConfigFromDatabase('userName'), $content);
        fputs($config, $content);
        fclose($config);
    }

    /**
     * Create folders for database migrations
     */
    private function createDatabaseFolders(): void
    {
        mkdir($this->modulePath . '/database');
        mkdir($this->modulePath . '/database/migrations');
        mkdir($this->modulePath . '/database/seeds');
    }

    /**
     * Create dictionary folder and examples files
     */
    private function createDictionaryFiles(): void
    {
        mkdir($this->modulePath . '/dictionary');

        // Create english dictionary
        $dictionaryEn = fopen($this->modulePath . '/dictionary/dictionary_en.toml', 'a+');
        $content = require dirname(__DIR__) . '/templates/dictionary.php';
        fputs($dictionaryEn, $content);
        fclose($dictionaryEn);

        // Create french dictionary
        $dictionaryFr = fopen($this->modulePath . '/dictionary/dictionary_fr.toml', 'a+');
        $content = require dirname(__DIR__) . '/templates/dictionary.php';
        fputs($dictionaryFr, $content);
        fclose($dictionaryFr);
    }

    /**
     * Create the first action
     */
    private function createIndexActionFile(): void
    {
        $indexActionFile = fopen($this->modulePath . '/Actions/IndexAction.php', 'a+');
        $content = require dirname(__DIR__) . '/templates/indexAction.php';
        $content = str_replace('%MODULE_NAME%', $this->moduleName, $content);
        $content = str_replace('%LCFIRST_MODULE_NAME%', lcfirst($this->moduleName), $content);
        fputs($indexActionFile, $content);
        fclose($indexActionFile);
    }

    /**
     * Create the first view
     */
    private function createIndexView(): void
    {
        $index = fopen($this->modulePath . '/views/index.twig', 'a+');
        $content = '<h1>hello world</h1>';
        fputs($index, $content);
        fclose($index);
    }

    /**
     * Create the module controller
     */
    private function createModuleController(): void
    {
        $filePath = $this->modulePath . '/' . $this->moduleName . 'Module.php';
        $moduleController = fopen($filePath, 'a+');
        $content = require dirname(__DIR__) . '/templates/moduleController.php';
        $content = str_replace('%MODULE_NAME%', $this->moduleName, $content);
        $content = str_replace('%LCFIRST_MODULE_NAME%', lcfirst($this->moduleName), $content);
        fputs($moduleController, $content);
        fclose($moduleController);
    }

    /**
     * Create folder for the module
     */
    private function createModuleFolder(): void
    {
        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }

    /**
     * Create views folder
     */
    private function createViewsFolder(): void
    {
        mkdir($this->modulePath . '/views');
    }

    /**
     * @param string $index
     * @return string
     */
    private function getConfigFromDatabase(string $index): string
    {
        return (isset($this->databaseConfig['$index'])) ? $this->databaseConfig['$index'] : '';
    }
}
