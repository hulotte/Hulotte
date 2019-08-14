<?php

namespace Hulotte\Commands;

use Hulotte\Database\Database;

/**
 * Trait CommandDatabase
 * Methods to manage database on commands
 *
 * @package Hulotte\Commands
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
trait CommandDatabase
{
    /**
     * @var Database
     */
    private $database;

    public function setDatabase(Database $database): void
    {
        $this->database = $database;
    }
}
