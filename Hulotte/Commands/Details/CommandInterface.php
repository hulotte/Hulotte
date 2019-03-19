<?php

namespace Hulotte\Commands\Details;

use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};

/**
 * Interface CommandInterface
 *
 * @package Hulotte\Commands\Details
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
interface CommandInterface
{
    /**
     * Executes list of commands
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public static function execute(InputInterface $input, OutputInterface $output): void;
}
