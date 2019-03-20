<?php

namespace Hulotte\Commands;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface
};

/**
 * Class ModuleCommand
 *
 * @package Hulotte\Commands
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ModuleCommand extends Command
{
    const MODULES_PATH = 'src/modules';

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var string
     */
    private $modulePath;

    /**
     * Configures the current command
     */
    protected function configure()
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
     * @throws LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->moduleName = ucfirst($input->getArgument('moduleName'));
        $this->createModulesFolder($output);

        $output->writeln('Creating ' . $this->moduleName . ' folders and files');
        $this->createCustomModuleFolder();
        $this->createActionsFolder();
        $this->createDatabaseFolders();
        $this->createDictionaryFiles();
        $this->createViewsFolder();
        $this->createConfigFile();
        $this->createModuleController();
        $output->writeln($this->moduleName . ' module is created');
    }

    /**
     * Create folder for Actions
     */
    private function createActionsFolder()
    {
        if(!file_exists($this->getModulePath() . '/Actions')){
            mkdir($this->getModulePath() . '/Actions');
        }
    }

    /**
     * Create Config file
     */
    private function createConfigFile()
    {
        if(!file_exists($this->getModulePath() . '/config.php')){
            $config = fopen($this->getModulePath() . '/config.php', 'a+');
            $content = require __DIR__ . '/templates/config.php';
            fputs($config, $content);
            fclose($config);
        }
    }

    /**
     * Create folder for the custom module
     */
    private function createCustomModuleFolder(): void
    {
        if(!file_exists($this->getModulePath())){
            mkdir($this->getModulePath());
        }
    }

    /**
     * Create folders for database migrations
     */
    private function createDatabaseFolders(): void
    {
        if(!file_exists($this->getModulePath() . '/database')){
            mkdir($this->getModulePath() . '/database');
            mkdir($this->getModulePath() . '/database/migrations');
            mkdir($this->getModulePath() . '/database/seeds');
        }
    }

    /**
     * Create dictionary folder and examples files
     */
    private function createDictionaryFiles(): void
    {
        if(!file_exists($this->getModulePath() . '/dictionary')){
            mkdir($this->getModulePath() . '/dictionary');

            // Create english dictionary
            $dictionaryEn = fopen($this->getModulePath() . '/dictionary/dictionary_en.toml', 'a+');
            $content = require __DIR__ . '/templates/dictionary.php';
            fputs($dictionaryEn, $content);
            fclose($dictionaryEn);

            // Create french dictionary
            $dictionaryFr = fopen($this->getModulePath() . '/dictionary/dictionary_fr.toml', 'a+');
            $content = require __DIR__ . '/templates/dictionary.php';
            fputs($dictionaryFr, $content);
            fclose($dictionaryFr);
        }
    }

    /**
     * Create the module controller
     */
    private function createModuleController(): void
    {
        $fileName = $this->moduleName . 'Module';

        if(!file_exists($this->getModulePath() . '/' . $fileName)){
            $moduleController = fopen($this->getModulePath() . '/' . $fileName, 'a+');
            $content = require __DIR__ . '/templates/moduleController.php';
            $content = str_replace('%MODULE_NAME%', $this->moduleName, $content);
            fputs($moduleController, $content);
            fclose($moduleController);
        }
    }

    /**
     * Create modules folder
     * @param OutputInterface $output
     */
    private function createModulesFolder(OutputInterface $output): void
    {
        if(!file_exists(self::MODULES_PATH)){
            $output->writeln('Creating modules folder');
            mkdir(self::MODULES_PATH);
            $output->writeln('modules folder is created');
        }
    }

    /**
     * Create views folder
     */
    private function createViewsFolder(): void
    {
        if(!file_exists($this->getModulePath() . '/views')){
            mkdir($this->getModulePath() . '/views');
        }
    }

    /**
     * Construct the module path with the name of the module
     * @return string
     */
    private function getModulePath(): string
    {
        if(!$this->modulePath){
            $this->modulePath = self::MODULES_PATH . '/' . $this->moduleName;
        }

        return $this->modulePath;
    }
}
