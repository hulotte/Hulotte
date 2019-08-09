<?php

namespace Hulotte\Database;

use \PDO;

/**
 * Class Database
 *
 * @package Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Database
{
    /**
     * @Var PDO
     */
    private $pdo;

    /**
     * Database constructor
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get the last id insert in database
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Make a prepared request on database
     * @param string $statement
     * @param array $attributes
     * @param bool $one
     * @return mixed
     */
    public function prepare(string $statement, array $attributes, bool $one = false)
    {
        $query = $this->pdo->prepare($statement);
        $query->execute($attributes);

        if (strpos($statement, 'UPDATE') === 0
            || strpos($statement, 'INSERT') === 0
            || strpos($statement, 'DELETE') === 0
        ) {
            return $query;
        }

        if ($one) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    /**
     * Make a request on database
     * @param string $statement
     * @param bool $one
     * @return mixed
     */
    public function query(string $statement, bool $one = false)
    {
        $query = $this->pdo->query($statement);

        if (strpos($statement, 'UPDATE') === 0
            || strpos($statement, 'INSERT') === 0
            || strpos($statement, 'DELETE') === 0
            || strpos($statement, 'CREATE') === 0
        ) {
            return $query;
        }

        if ($one) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }
}
