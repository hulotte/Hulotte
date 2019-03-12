<?php

namespace Tests\Hulotte\Database;

use PHPUnit\Framework\TestCase;
use Hulotte\{
    Database\StatementBuilder,
    Database\TableStatement
};

/**
 * Class TableStatementTest
 *
 * @package Tests\Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TableStatementTest extends TestCase
{
    private $tableStatement;

    public function setUp()
    {
        $this->tableStatement = $this->getObjectForTrait(TableStatement::class);
        $this->tableStatement->table = 'test';
    }

    public function testAllStatement()
    {
        $response = $this->tableStatement->allStatement();

        $this->assertInstanceOf(StatementBuilder::class, $response);
        $this->assertEquals('SELECT * FROM test', (string)$response);
    }

    public function testCountStatemen()
    {
        $response = $this->tableStatement->countStatement();

        $this->assertInstanceOf(StatementBuilder::class, $response);
        $this->assertEquals('SELECT COUNT(id) as count FROM test', (string)$response);
    }
}
