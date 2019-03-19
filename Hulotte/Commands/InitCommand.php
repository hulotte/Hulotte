<?php

namespace Hulotte\Commands;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};
use Hulotte\Commands\Details\{
    Init\PublicCommand, 
    Init\SrcCommand,
    Init\TmpCommand
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
        TmpCommand::execute($input, $output);
        PublicCommand::execute($input, $output); 
        SrcCommand::execute($input, $output);
    }
}
