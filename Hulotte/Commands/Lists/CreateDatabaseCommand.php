<?php

namespace Hulotte\Commands\Lists;

use Hulotte\Database\Database;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface,
    Question\Question
};

/**
 * CreateDatabaseCommand
 * Command to create a database
 *
 * @package Hulotte\Commands\Lists
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class CreateDatabaseCommand extends Command
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var string
     */
    private $databaseHost;

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var string
     */

    private $databasePassword;

    /**
     * @var string
     */
    private $databaseUserName;

    /**
     * @return string
     */
    public function getDatabaseHost(): string
    {
        return $this->databaseHost;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getDatabasePassword(): string
    {
        return $this->databasePassword;
    }

    /**
     * @return string
     */
    public function getDatabaseUserName(): string
    {
        return $this->databaseUserName;
    }

    /**
     * Instanciate database object
     * @param Database|null $database
     * @return Database
     */
    public function setDatabase(?Database $database = null): Database
    {
        if (!$this->database) {
            if (!$database) {
                $pdo = new \PDO(
                    'mysql:host=' . $this->databaseHost . ';charset=utf8',
                    $this->databaseUserName,
                    $this->databasePassword
                );

                $this->database = new Database($pdo);
            } else {
                $this->database = $database;
            }
        }

        return $this->database;
    }

    /**
     * Configures the current command
     */
    protected function configure(): void
    {
        $this
            ->setName('database:create')
            ->setDescription('Create new database');
    }

    /**
     * Executes the current command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $hostQuestion = new Question('Database host ? <comment>[localhost]</comment> ', 'localhost');
        $nameQuestion = new Question('Database name ? ');
        $nameQuestion->setValidator(function ($value) {
            if (trim($value) === '') {
                throw new \Exception('Database name cannot be empty');
            }

            return $value;
        });
        $userNameQuestion = new Question('Database user name ? <comment>[root]</comment> ', 'root');
        $passwordQuestion = new Question('Database password ? ', '');

        $this->databaseHost = $helper->ask($input, $output, $hostQuestion);
        $this->databaseName = $helper->ask($input, $output, $nameQuestion);
        $this->databaseUserName = $helper->ask($input, $output, $userNameQuestion);
        $this->databasePassword = $helper->ask($input, $output, $passwordQuestion);

        $this->setDatabase()->query('CREATE DATABASE iF NOT EXISTS ' . $this->databaseName);

        $output->writeln('');
        $output->writeln('<info>Database ' . $this->databaseName . ' is created</info>');
    }
}
