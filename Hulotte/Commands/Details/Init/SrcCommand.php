<?php

namespace Hulotte\Commands\Details\Init;

use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};
use Hulotte\Commands\Details\CommandInterface;

/**
 * Class SrcCommand
 * @package Hulotte\Commands\Details\Init
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class SrcCommand implements CommandInterface
{
     /**
     * Executes list of commands
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public static function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Creating src folder');
        mkdir('src');
        $output->writeln('src folder is created');
    }
}
