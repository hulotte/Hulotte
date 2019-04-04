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
 * @coversNothing
 */
class DatabaseTestCase extends TestCase
{
    /**
     * @var Database
     */
    protected $database;

    /**
     * @var PDO
     */
    protected $pdo;

    public function setUp(): void
    {
        $this->getPdo()->exec('CREATE TABLE test
            (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255), number INTEGER)');
        $this->getDatabase();
    }

    /**
     * @return Database
     */
    protected function getDatabase(): Database
    {
        if ($this->database === null) {
            $this->database = new Database($this->getPdo());
        }

        return $this->database;
    }

    /**
     * @return PDO
     */
    protected function getPdo(): PDO
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
