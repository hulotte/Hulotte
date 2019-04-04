<?php

namespace Tests\Hulotte\Actions;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface
};
use Hulotte\{
    Actions\CrudAction,
    Database\Database,
    Renderer\RendererInterface,
    Router,
    Services\Dictionary,
    Session\MessageFlash
};
use Tests\DatabaseTestCase;

/**
 * Class CrudActionTest
 *
 * @package Tests\Hulotte\Actions
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Actions\CrudAction
 */
class CrudActionTest extends DatabaseTestCase
{
    private $container;
    private $crudAction;
    private $dictionary;
    private $messageFlash;
    private $renderer;
    private $router;
    private $testTable;

    public function setUp(): void
    {
        $this->getPdo()->exec('CREATE TABLE test
            (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255))');
        $this->database = new Database($this->getPdo());
    }

    /**
     * @covers ::create
     */
    public function testCreateRedirection(): void
    {
        $request = new ServerRequest('GET', '/manager/create');

        $this->getCrudAction(true)->expects($this->once())->method('create');

        call_user_func_array($this->crudAction, [$request]);
    }

    /**
     * @covers ::read
     */
    public function testReadRedirection(): void
    {
        $request = new ServerRequest('GET', '/manager/read');

        $this->getCrudAction(true)->expects($this->once())->method('read');

        call_user_func_array($this->crudAction, [$request]);
    }

    /**
     * @covers ::update
     */
    public function testUpdateRedirection(): void
    {
        $request = (new ServerRequest('GET', '/manager/update'))
            ->withAttribute('id', 1);

        $this->getCrudAction(true)->expects($this->once())->method('update');

        call_user_func_array($this->crudAction, [$request]);
    }

    /**
     * @covers ::delete
     */
    public function testDeleteRedirection(): void
    {
        $request = (new ServerRequest('DELETE', '/manager/delete'))
            ->withAttribute('id', 1);

        $this->getCrudAction(true)->expects($this->once())->method('delete');

        call_user_func_array($this->crudAction, [$request]);
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $request = (new ServerRequest('POST', '/manager/create'))
            ->withParsedBody(['label' => 'test']);

        $this->getMessageFlash()->expects($this->once())->method('success');

        $action = $this->getCrudAction();
        $action->setRedirectionPath('/manager/read');
        $action->setFields(['label']);

        $response = call_user_func_array($action, [$request]);
        $record = $this->database->query('SELECT * FROM test WHERE label="test"');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertCount(1, $record);
    }

    /**
     * @covers ::read
     */
    public function testRead(): void
    {
        $this->insertDatas();
        $request = new ServerRequest('GET', '/manager/read');

        $this->getContainer()->get('crud.paths.suffix.read')->shouldBeCalled();
        $this->getRenderer()->expects($this->once())->method('render');

        $action = $this->getCrudAction();
        $action->setStatement($this->getTable()->allStatement());

        $response = call_user_func_array($action, [$request]);

        $this->assertIsString($response);
    }

    /**
     * @covers ::read
     */
    public function testReadWithoutStatementDefined(): void
    {
        $this->insertDatas();
        $request = new ServerRequest('GET', '/manager/read');

        $this->getContainer()->get('crud.paths.suffix.read')->shouldBeCalled();
        $this->getRenderer()->expects($this->once())->method('render');

        $action = $this->getCrudAction();

        $response = call_user_func_array($action, [$request]);

        $this->assertIsString($response);
    }

    /**
     * @covers ::update
     */
    public function testUpdate(): void
    {
        $this->insertDatas();
        $request = (new ServerRequest('POST', '/manager/update'))
            ->withAttribute('id', 1)
            ->withParsedBody(['label' => 'modify']);

        $this->getMessageFlash()->expects($this->once())->method('success');

        $action = $this->getCrudAction();
        $action->setRedirectionPath('/manager/read');
        $action->setFields(['label']);

        $response = call_user_func_array($action, [$request]);
        $record = $this->database->query('SELECT * FROM test WHERE id = 1')[0];

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('modify', $record['label']);
    }

    /**
     * @covers ::delete
     */
    public function testDelete(): void
    {
        $this->insertDatas();
        $request = (new ServerRequest('DELETE', '/manager/delete'))
            ->withAttribute('id', 1);

        $this->getMessageFlash()->expects($this->once())->method('success');
        
        $action = $this->getCrudAction();
        $action->setRedirectionPath('/manager/read');

        $response = call_user_func_array($action, [$request]);
        $records = $this->database->query('SELECT * FROM test');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertCount(3, $records);
    }

    /**
     * @return TestTable
     */
    protected function getTable(): TestTable
    {
        if (!$this->testTable) {
            $this->testTable = new TestTable($this->getContainer()->reveal(), $this->database);
        }

        return $this->testTable;
    }

    /**
     * @return ObjectProphecy
     */
    private function getContainer(): ObjectProphecy
    {
        if (!$this->container) {
            $this->container = $this->prophesize(ContainerInterface::class);
            $this->container->get('crud.paths.suffix.create')->willReturn('/create');
            $this->container->get('crud.paths.suffix.read')->willReturn('/read');
            $this->container->get('crud.paths.suffix.update')->willReturn('/update');
            $this->container->get('crud.paths.suffix.delete')->willReturn('/delete');
            $this->container->get(TestEntity::class)->willReturn(new TestEntity());
        }
       
        return $this->container;
    }

    /**
     * @param bool $mock
     * @return CrudAction|MockObject
     */
    private function getCrudAction(bool $mock = false)
    {
        if (!$this->crudAction) {
            if ($mock) {
                $this->crudAction = $this->getMockBuilder(CrudAction::class)
                    ->setConstructorArgs([
                        $this->getContainer()->reveal(),
                        $this->getDictionary(),
                        $this->getMessageFlash(),
                        $this->getRenderer(),
                        $this->getRouter(),
                        $this->getTable()
                    ])
                    ->setMethods(['create', 'read', 'update', 'delete'])
                    ->getMock();
            } else {
                $this->crudAction  = new CrudAction(
                    $this->getContainer()->reveal(),
                    $this->getDictionary(),
                    $this->getMessageFlash(),
                    $this->getRenderer(),
                    $this->getRouter(),
                    $this->getTable()
                );
            }
        }
       
        return $this->crudAction;
    }

    /**
     * @return MockObject
     */
    private function getDictionary(): MockObject
    {
        if (!$this->dictionary) {
            $this->dictionary = $this->createMock(Dictionary::class);
        }

        return $this->dictionary;
    }

    /**
     * @return MockObject
     */
    private function getMessageFlash(): MockObject
    {
        if (!$this->messageFlash) {
            $this->messageFlash = $this->createMock(MessageFlash::class);
        }

        return $this->messageFlash;
    }

    /**
     * @return MockObject
     */
    private function getRenderer(): MockObject
    {
        if (!$this->renderer) {
            $this->renderer = $this->createMock(RendererInterface::class);
        }
       
        return $this->renderer;
    }

    /**
     * @return MockObject
     */
    private function getRouter(): MockObject
    {
        if (!$this->router) {
            $this->router = $this->createMock(Router::class);
        }
       
        return $this->router;
    }

    /**
     * Add Datas to fake database
     */
    private function insertDatas(): void
    {
        $statement = 'INSERT INTO test(id, label) VALUES '
            . '(1, "first"), (2, "second"), (3, "third"), (4, "fourth")';
        $this->database->query($statement);
    }
}
