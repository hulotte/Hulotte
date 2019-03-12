<?php

namespace Tests\Hulotte\Database;

use Tests\DatabaseTestCase;
use Hulotte\Database\StatementBuilder;

/**
 * Class StatementBuilderTest
 *
 * @package Tests\Hulotte\Database
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class StatementBuilderTest extends DatabaseTestCase
{
    
    public function testSimpleStatement()
    {
        $statement = (new StatementBuilder())
            ->from('post')
            ->select('name');

        $this->assertEquals('SELECT name FROM post', $statement);
    }

    public function testWithWhere()
    {
        $statement = (new StatementBuilder())
            ->from('post', 'p')
            ->where('a = :a OR b = :b');
            
        $statement2 = (new StatementBuilder())
            ->from('post', 'p')
            ->where(['a = :a OR b = :b', 'c = :c']);

        $this->assertEquals('SELECT * FROM post as p WHERE (a = :a OR b = :b)', $statement);
        $this->assertEquals('SELECT * FROM post as p WHERE (a = :a OR b = :b) AND (c = :c)', $statement2);
    }

    public function testWithOrder()
    {
        $statement = (new StatementBuilder())
            ->from('post')
            ->order('createdAt');

        $statement2 = (new StatementBuilder())
            ->from('post')
            ->order(['createdAt DESC', 'label ASC']);

        $this->assertEquals('SELECT * FROM post ORDER BY createdAt', $statement);
        $this->assertEquals('SELECT * FROM post ORDER BY createdAt DESC, label ASC', $statement2);
    }

    public function testWithLimit()
    {
        $statement = (new StatementBuilder())
            ->from('post')
            ->limit(10);

        $statement2 = (new Statementbuilder())
            ->from('post')
            ->limit(10, 14);

        $this->assertEquals('SELECT * FROM post LIMIT 10', $statement);
        $this->assertEquals('SELECT * FROM post LIMIT 10, 14', $statement2);
    }

    public function testWithGroup()
    {
        $statement = (new StatementBuilder())
            ->from('post')
            ->group('label');
        
        $this->assertEquals('SELECT * FROM post GROUP BY label', $statement);
    }
    
    public function testWithJoin()
    {
        $statement = (new StatementBuilder())
            ->from('post')
            ->join('categories as c', 'c.id = p.category_id')
            ->join('categories as c2', 'c2.id = p.category_id', 'inner');
        
        $this->assertEquals(
            'SELECT * FROM post LEFT JOIN categories as c ON c.id = p.category_id '
                . 'INNER JOIN categories as c2 ON c2.id = p.category_id',
            $statement
        );
    }

    public function testWithAll()
    {
        $statement = (new StatementBuilder())
            ->group('label')
            ->limit(5)
            ->select(['id', 'label'])
            ->where(['a = :a', 'b = :b'])
            ->order('createdAt DESC')
            ->from('post');

        $result = 'SELECT id, label FROM post '
            . 'WHERE (a = :a) AND (b = :b) GROUP BY label ORDER BY createdAt DESC LIMIT 5';
        
        $this->assertEquals($result, $statement);
    }
    
    public function testInsertUnique()
    {
        $statement = (new StatementBuilder())
            ->insert('post')
            ->columns('title')
            ->values('My title');

        $result = 'INSERT INTO post (title) VALUES ("My title")';
        
        $this->assertEquals($result, $statement);
    }
    
    public function testInsert()
    {
        $statement = (new StatementBuilder())
            ->insert('post')
            ->columns(['title', 'slug'])
            ->values(['My title', 'my-slug']);

        $result = 'INSERT INTO post (title, slug) VALUES ("My title", "my-slug")';
        
        $this->assertEquals($result, $statement);
    }
    
    public function testInsertMultiple()
    {
        $statement = (new StatementBuilder())
            ->insert('post')
            ->columns(['title', 'slug'])
            ->values(['My title', 'my-slug'])
            ->values(['My next title', 'my-next-slug']);
        
        $result = 'INSERT INTO post (title, slug) VALUES ("My title", "my-slug"), ("My next title", "my-next-slug")';
        
        $this->assertEquals($result, $statement);
    }
    
    public function testDelete()
    {
        $statement = (new StatementBuilder())
            ->delete('post');
        
        $result = 'DELETE FROM post';
        
        $this->assertEquals($result, $statement);
    }
    
    public function testDeleteMultipleCondition()
    {
        $statement = (new StatementBuilder())
            ->delete('post')
            ->where(['a = :a OR b = :b', 'c = :c']);
        
        $result = 'DELETE FROM post WHERE (a = :a OR b = :b) AND (c = :c)';
        
        $this->assertEquals($result, $statement);
    }
    
    public function testUpdate()
    {
        $statement = (new StatementBuilder())
            ->update('post')
            ->set(['a = :a', 'b = :b'])
            ->where('id = :id');

        $result = 'UPDATE post SET a = :a, b = :b WHERE (id = :id)';
        
        $this->assertEquals($result, $statement);
    }
}
