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
    protected function configure(): void
    {
        $this
            ->setName('create:database')
            ->setDescription('Create new database')
            ->addArgument('databaseName', InputArgument::OPTIONAL, 'The name of the database.');
    }

    /**
     * Executes the current command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $database = $this->container->get(Database::class);
        $databaseName = $this->defineDatabaseName($input);

        if ($databaseName) {
            $database->query('CREATE DATABASE IF NOT EXISTS ' . $databaseName);
            $output->writeln('Database ' . $databaseName . ' is created');
        } else {
            $output->writeln('<error>No database name specified !</error>');
        }
    }

    /**
     * Define the name of database on input argument or container parameter
     * @param InputInterface $input
     * @return null|string
     */
    private function defineDatabaseName(InputInterface $input): ?string
    {
        if ($input->hasArgument('databaseName')) {
            return $input->getArgument('databaseName');
        }

        if ($this->container->has('database.name')) {
            return $this->container->get('database.name');
        }

        return null;
    }
}
