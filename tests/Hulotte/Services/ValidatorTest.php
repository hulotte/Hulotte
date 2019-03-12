<?php

namespace Tests\Hulotte\Services;

use Psr\Container\ContainerInterface;
use Hulotte\{
    Database\Table,
    Services\Dictionary,
    Services\Validator
};
use Tests\DatabaseTestCase;

/**
 * Class ValidatorTest
 *
 * @package Tests\Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ValidatorTest extends DatabaseTestCase
{
    private $table;

    public function testRequiredIfSuccess()
    {
        $errors = $this->makeValidator(['name' => 'john', 'content' => 'content'])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testRequiredIfFailed()
    {
        $errors = $this->makeValidator(['name' => 'john'])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testNotEmpty()
    {
        $errors = $this->makeValidator(['name' => 'joe', 'content' => ''])
            ->notEmpty('content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->makeValidator([
            'slug' => 'fAke-slug1',
            'slug2' => 'fAke-slug2',
            'slug3' => 'fake--slug3'
        ])
            ->slug('slug')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();
        
        $this->assertCount(3, $errors);
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];

        $test = $this->makeValidator($params)->length('slug', 3)->getErrors();
        $test2 = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $test3 = $this->makeValidator($params)->length('slug', 3, 4)->getErrors();
        $test4 = $this->makeValidator($params)->length('slug', 3, 20)->getErrors();
        $test5 = $this->makeValidator($params)->length('slug', null, 20)->getErrors();
        $test6 = $this->makeValidator($params)->length('slug', null, 8)->getErrors();

        $this->assertCount(0, $test);
        $this->assertCount(1, $test2);
        $this->assertCount(1, $test3);
        $this->assertCount(0, $test4);
        $this->assertCount(0, $test5);
        $this->assertCount(1, $test6);
    }

    public function testDateTime()
    {
        $test = $this->makeValidator(['date' => '2012-12-12 11:12:13'])
            ->dateTime('date')->getErrors();
        $test2 = $this->makeValidator(['date' => '2012-12-12 00:00:00'])
            ->dateTime('date')->getErrors();
        $test3 = $this->makeValidator(['date' => '2012-21-12 00:00:00'])
            ->dateTime('date')->getErrors();
        $test4 = $this->makeValidator(['date' => '2013-02-29 11:12:13'])
            ->dateTime('date')->getErrors();

        $this->assertCount(0, $test);
        $this->assertCount(0, $test2);
        $this->assertCount(1, $test3);
        $this->assertCount(1, $test4);
    }
    
    public function testEmail()
    {
        $test = $this->makeValidator(['email' => 'test@test.com'])
            ->email('email')->getErrors();
        $test2 = $this->makeValidator(['email' => 'not-email'])
            ->email('email')->getErrors();
        
        $this->assertCount(0, $test);
        $this->assertCount(1, $test2);
    }

    public function testExists()
    {
        $this->makeTable();
        $test = $this->makeValidator(['id' => '1'])
            ->exists('id', $this->table)->getErrors();

        $this->assertCount(0, $test);
    }

    public function testNotExists()
    {
        $this->makeTable();
        $test = $this->makeValidator(['id' => '1'])
            ->notExists('id', $this->table)->getErrors();

        $this->assertCount(1, $test);
    }

    public function testExistWithNoId()
    {
        $this->makeTable();
        $test = $this->makeValidator(['name' => 'Sébastien'])
            ->exists('name', $this->table)->getErrors();

        $this->assertCount(0, $test);
    }

    public function testExistWithDifferentName()
    {
        $this->makeTable();
        $test = $this->makeValidator(['user_id' => '1'])
            ->exists(['id', 'user_id'], $this->table)->getErrors();

        $this->assertCount(0, $test);
    }

    public function testNotExistWithDifferentName()
    {
        $this->makeTable();
        $test = $this->makeValidator(['user_id' => '3'])
            ->exists(['id', 'user_id'], $this->table)->getErrors();

        $this->assertCount(1, $test);
        $this->assertEquals('test', (string)$test['user_id']);
    }

    private function makeValidator(array $params)
    {
        $dictionary = $this->createMock(Dictionary::class);
        $dictionary->method('translate')->willReturn('test');

        return new Validator($dictionary, $params);
    }

    private function makeTable()
    {
        $this->getPdo()->exec('CREATE TABLE user
            (id INTEGER PRIMARYKEY AUTO_INCREMENT, name VARCHAR(255))');
        $this->getDatabase();
        $this->table = new Table($this->createMock(ContainerInterface::class), $this->getDatabase());
        $this->table->setTable('user');
        $this->table->insert(['id' => 1, 'name' => 'Sébastien']);
    }
}
