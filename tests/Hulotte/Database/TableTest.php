<?php

namespace Tests\Hulotte\Database;

use Psr\Container\ContainerInterface;
use Hulotte\{
    Database\Table,
    Services\Paginator
};
use Tests\DatabaseTestCase;

/**
 * Class TableTest
 *
 * @package Tests\Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Database\Table
 */
class TableTest extends DatabaseTestCase
{
    /**
     * @var Table
     */
    private $table;

    public function setUp(): void
    {
        parent::setUp();

        $containerMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $this->table = new Table($containerMock, $this->getDatabase());
        $this->table->setTable('test');
    }

    /**
     * @covers ::query
     */
    public function testQuery(): void
    {
        $this->table->query('INSERT INTO test (id, label) VALUES (1, "hello-world")');
        $verify = $this->getDatabase()->query('SELECT label FROM test WHERE id = 1', true);

        $this->assertEquals('hello-world', $verify['label']);
    }

    /**
     * @covers ::query
     */
    public function testQueryPrepare(): void
    {
        $this->table->query(
            'INSERT INTO test (id, label) VALUES (:id, :label)',
            [
                ':id' => 1,
                ':label' => 'hello-world'
            ]
        );
        $verify = $this->getDatabase()->query('SELECT label FROM test WHERE id = 1', true);
        $tableResult = $this->table->query('SELECT label FROM test WHERE id = ?', [1], true);

        $this->assertEquals('hello-world', $verify['label']);
        $this->assertEquals('hello-world', $tableResult['label']);
    }

    /**
     * @covers ::find
     */
    public function testFind(): void
    {
        $this->insertData();
        $result = $this->table->find(1);

        $this->assertEquals('hello-world', $result['label']);
    }

    /**
     * @covers ::findBy
     */
    public function testFindBy(): void
    {
        $this->insertData();
        $result = $this->table->findBy('id', 1);

        $this->assertEquals('hello-world', $result['label']);
    }

    /**
     * @covers ::all
     */
    public function testAll(): void
    {
        $this->insertData();
        $result = $this->table->all();

        $this->assertIsArray($result);
        $this->assertEquals('hello-world', $result[0]['label']);
        $this->assertEquals('this is a test', $result[1]['label']);
    }

    /**
     * @covers ::allBy
     */
    public function testAllBy(): void
    {
        $this->insertData();
        $result = $this->table->allBy('number', 1);

        $this->assertIsArray($result);
        $this->assertEquals(2, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    /**
     * @covers ::insert
     */
    public function testInsert(): void
    {
        $this->table->insert([
            'id' => 1,
            'label' => 'new test',
            'number' => 1
        ]);
        
        $result = $this->table->find(1);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('new test', $result['label']);
    }

    /**
     * @covers ::insert
     */
    public function testInsertWithTable(): void
    {
        $this->table->insert([
            'id' => 1,
            'label' => 'new test',
            'number' => 1
        ], 'test');

        $result = $this->table->find(1);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('new test', $result['label']);
    }

    /**
     * @covers ::delete
     */
    public function testDelete(): void
    {
        $this->insertData();
        $this->table->delete(1);
        
        $result = $this->table->all();
        
        $this->assertCount(2, $result);
    }

    /**
     * @covers ::update
     */
    public function testUpdate(): void
    {
        $this->insertData();
        $test = $this->table->update(1, [
            'label' => 'new label'
        ]);
        
        $result = $this->table->find(1);

        $this->assertEquals('new label', $result['label']);
    }

    /**
     * @covers ::allList
     */
    public function testAllList(): void
    {
        $this->insertData();
        $test = $this->table->allList('id', 'label');

        $this->assertIsArray($test);
        $this->assertEquals($test[2], 'this is a test');
    }

    /**
     * @covers ::allList
     */
    public function testAllListWithOnlyOneParameter(): void
    {
        $this->insertData();
        $test = $this->table->allList('id');

        $this->assertIsArray($test);
        $this->assertEquals($test[0], 1);
    }

    /**
     * @covers ::paginate
     */
    public function testPaginate(): void
    {
        $this->insertData();
        $result = $this->table->paginate(
            $this->table->allStatement(),
            1
        );

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertEquals(3, $result->totalPages);
    }

    /**
     * @covers ::isExists
     */
    public function testIsExists(): void
    {
        $this->insertData();
        $result = $this->table->isExists(2);

        $this->assertTrue($result);
    }

    /**
     * @covers ::isExists
     */
    public function testIsNotExists(): void
    {
        $this->insertData();
        $result = $this->table->isExists(4);

        $this->assertFalse($result);
    }

    /**
     * @covers ::isExists
     */
    public function testIsExistsWithNotId(): void
    {
        $this->insertData();
        $result = $this->table->isExists('label', 'hello-world');

        $this->assertTrue($result);
    }

    /**
     * @covers ::isExists
     */
    public function testIsNotExistsWithNotId(): void
    {
        $this->insertData();
        $result = $this->table->isExists('label', 'coucou');

        $this->assertFalse($result);
    }

    /**
     * Add datas to fake database
     */
    private function insertData(): void
    {
        $this->getDatabase()->query('INSERT INTO test (id, label, number) 
            VALUES (1, "hello-world", 2), 
                (2, "this is a test", 1), 
                (3, "this is again a test", 1)');
    }
}
