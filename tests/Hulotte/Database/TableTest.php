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
 */
class TableTest extends DatabaseTestCase
{
    private $table;

    public function setUp()
    {
        parent::setUp();

        $containerMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $this->table = new Table($containerMock, $this->getDatabase());
        $this->table->setTable('test');
    }

    public function testQuery()
    {
        $this->table->query('INSERT INTO test (id, label) VALUES (1, "hello-world")');
        $verify = $this->getDatabase()->query('SELECT label FROM test WHERE id = 1', true);

        $this->assertEquals('hello-world', $verify['label']);
    }

    public function testQueryPrepare()
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

    public function testFind()
    {
        $this->insertData();
        $result = $this->table->find(1);

        $this->assertEquals('hello-world', $result['label']);
    }

    public function testFindBy()
    {
        $this->insertData();
        $result = $this->table->findBy('id', 1);

        $this->assertEquals('hello-world', $result['label']);
    }

    public function testAll()
    {
        $this->insertData();
        $result = $this->table->all();

        $this->assertInternalType('array', $result);
        $this->assertEquals('hello-world', $result[0]['label']);
        $this->assertEquals('this is a test', $result[1]['label']);
    }

    public function testAllBy()
    {
        $this->insertData();
        $result = $this->table->allBy('number', 1);

        $this->assertInternalType('array', $result);
        $this->assertEquals(2, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }
    
    public function testInsert()
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

    public function testInsertWithTable()
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
    
    public function testDelete()
    {
        $this->insertData();
        $this->table->delete(1);
        
        $result = $this->table->all();
        
        $this->assertCount(2, $result);
    }
    
    public function testUpdate()
    {
        $this->insertData();
        $test = $this->table->update(1, [
            'label' => 'new label'
        ]);
        
        $result = $this->table->find(1);

        $this->assertEquals('new label', $result['label']);
    }

    public function testAllList()
    {
        $this->insertData();
        $test = $this->table->allList('id', 'label');

        $this->assertInternalType('array', $test);
        $this->assertEquals($test[2], 'this is a test');
    }

    public function testAllListWithOnlyOneParameter()
    {
        $this->insertData();
        $test = $this->table->allList('id');

        $this->assertInternalType('array', $test);
        $this->assertEquals($test[0], 1);
    }

    public function testPaginate()
    {
        $this->insertData();
        $result = $this->table->paginate(
            $this->table->allStatement(),
            1
        );

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertEquals(3, $result->totalPages);
    }

    public function testIsExists()
    {
        $this->insertData();
        $result = $this->table->isExists(2);

        $this->assertTrue($result);
    }

    public function testIsNotExists()
    {
        $this->insertData();
        $result = $this->table->isExists(4);

        $this->assertFalse($result);
    }

    public function testIsExistsWithNotId()
    {
        $this->insertData();
        $result = $this->table->isExists('label', 'hello-world');

        $this->assertTrue($result);
    }

    public function testIsNotExistsWithNotId()
    {
        $this->insertData();
        $result = $this->table->isExists('label', 'coucou');

        $this->assertFalse($result);
    }

    private function insertData()
    {
        $this->getDatabase()->query('INSERT INTO test (id, label, number) 
            VALUES (1, "hello-world", 2), 
                (2, "this is a test", 1), 
                (3, "this is again a test", 1)');
    }
}
