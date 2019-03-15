<?php

namespace Hulotte\Commands;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

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
        $output->writeln('coucou');

        $this->createPublicFolder();
        $output->writeln('Index file is created');

        /*
        $this->moduleName = $input->getArgument('moduleName');
        $this->pathModule = 'module/' . $this->moduleName;

        if (!file_exists('module/' . $this->moduleName)) {
            mkdir($this->pathModule);

            $this->createController();

            mkdir($this->pathModule . '/database');

            $this->createDictionary();

            mkdir($this->pathModule . '/entity');
            mkdir($this->pathModule . '/public');

            $this->createStyle();
            $this->createJs();

            mkdir($this->pathModule . '/public/image');
            mkdir($this->pathModule . '/table');

            $this->createView();

            $this->createConfigFile();

            $output->writeln('Le module ' . $this->moduleName . ' a bien été créé.');
        } else {
            $output->writeln('Le module ' . $this->moduleName . ' existe déjà.');
        }
        */
    }

    /**
     * 
     */
    private function createPublicFolder(): void
    {
        mkdir('public');
        $indexFile = fopen('public/index.php', 'a+');

        $content = 'test';

        fputs($indexFile, $content);
        fclose($indexFile);
    }
}
