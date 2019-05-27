<?php

namespace Tests\Hulotte\Database;

use Tests\DatabaseTestCase;

/**
 * Class DatabaseTest
 *
 * @package Tests\Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Database\Database
 */
class DatabaseTest extends DatabaseTestCase
{
    /**
     * @covers::query
     */
    public function testQuery(): void
    {
        $this->getDatabase()->query('INSERT INTO test(id, label) VALUES (1, "first test"), (2, "second test")');
        $arrayResult = $this->getDatabase()->query('SELECT id, label FROM test');
        $oneResult = $this->getDatabase()->query('SELECT id, label FROM test WHERE id = 1', true);

        $this->assertIsArray($arrayResult);
        $this->assertEquals('first test', $oneResult['label']);
    }

    /**
     * @covers ::lastInsertId
     */
    public function testLastInsertId(): void
    {
        $this->getDatabase()->query('INSERT INTO test(id, label) VALUES (1, "first test")');
        $lastInsertId = $this->getDatabase()->lastInsertId();

        $this->assertEquals(1, $lastInsertId);
    }

    /**
     * @covers ::prepare
     */
    public function testPrepare(): void
    {
        $this->getDatabase()->prepare(
            'INSERT INTO test(id, label) VALUES (:id, :label)',
            [1, 'first test']
        );
        $result = $this->getDatabase()->prepare('SELECT id, label FROM test WHERE id = :id', [1], true);

        $this->assertEquals('first test', $result['label']);
    }
}
