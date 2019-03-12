<?php

namespace Tests;

use \PDO;
use PHPUnit\Framework\TestCase;
use Hulotte\Database\Database;

/**
 * Class DatabaseTestCase
 *
 * @package Tests
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class DatabaseTestCase extends TestCase
{
    private $database;
    private $pdo;

    public function setUp()
    {
        $this->getPdo()->exec('CREATE TABLE test
            (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255), number INTEGER)');
        $this->getDatabase();
    }

    protected function getDatabase()
    {
        if ($this->database === null) {
            $this->database = new Database($this->getPdo());
        }

        return $this->database;
    }

    protected function getPdo()
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO('sqlite::memory:', null, null, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        }

        return $this->pdo;
    }
}
