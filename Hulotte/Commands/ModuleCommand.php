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
        $output->writeln($this->moduleName . ' module is created');
    }

    /**
     * Create folder for the custom module
     */
    private function createCustomModuleFolder(): void
    {
        if(!file_exists(self::MODULES_PATH . '/' . $this->moduleName)){
            mkdir(self::MODULES_PATH . '/' . $this->moduleName);
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
}
