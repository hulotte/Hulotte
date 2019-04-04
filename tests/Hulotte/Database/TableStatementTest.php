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
 * @coversDefaultClass \Hulotte\Database\TableStatement
 */
class TableStatementTest extends TestCase
{
    /**
     * @var Trait_TableStatement
     */
    private $tableStatement;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $this->tableStatement = $this->getObjectForTrait(TableStatement::class);
        $this->tableStatement->table = 'test';
    }

    /**
     * @covers ::allStatement
     */
    public function testAllStatement(): void
    {
        $response = $this->tableStatement->allStatement();

        $this->assertInstanceOf(StatementBuilder::class, $response);
        $this->assertEquals('SELECT * FROM test', (string)$response);
    }

    /**
     * @covers ::countStatement
     */
    public function testCountStatement(): void
    {
        $response = $this->tableStatement->countStatement();

        $this->assertInstanceOf(StatementBuilder::class, $response);
        $this->assertEquals('SELECT COUNT(id) as count FROM test', (string)$response);
    }
}
