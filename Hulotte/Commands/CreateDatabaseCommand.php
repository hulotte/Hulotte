<?php

namespace Hulotte\Commands;

use Symfony\Component\Console\{Command\Command, Input\InputArgument, Input\InputInterface, Output\OutputInterface};
use Hulotte\Database\Database;

/**
 * CreateDatabaseCommand
 * Command to create a database
 *
 * @package Hulotte\Commands
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class CreateDatabaseCommand extends Command
{
    use CommandContainer;

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this
            ->setName('create:database')
            ->setDescription('Create new database')
            ->addArgument('databaseName', InputArgument::REQUIRED, 'The name of the database.');
    }

    /**
     * Executes the current command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $databaseName = $input->getArgument('databaseName');
        $database = $this->container->get(Database::class);
        $database->query('CREATE DATABASE IF NOT EXISTS ' . $databaseName);

        $output->writeln('Database ' . $databaseName . ' is created');
    }
}
