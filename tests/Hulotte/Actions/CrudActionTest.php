<?php

namespace Tests\Hulotte\Actions;

use GuzzleHttp\Psr7\ServerRequest;
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
use Tests\{
    DatabaseTestCase,
    Hulotte\Actions\TestEntity,
    Hulotte\Actions\TestTable
};

/**
 * Class CrudActionTest
 *
 * @package Tests\Hulotte\Actions
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
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

    public function setUp()
    {
        $this->getPdo()->exec('CREATE TABLE test
            (id INTEGER PRIMARYKEY AUTO_INCREMENT, label VARCHAR(255))');
        $this->database = new Database($this->getPdo());
    }

    public function testCreateRedirection()
    {
        $request = new ServerRequest('GET', '/manager/create');

        $this->getCrudAction(true)->expects($this->once())->method('create');

        call_user_func_array($this->crudAction, [$request]);
    }
    
    public function testReadRedirection()
    {
        $request = new ServerRequest('GET', '/manager/read');

        $this->getCrudAction(true)->expects($this->once())->method('read');

        call_user_func_array($this->crudAction, [$request]);
    }

    public function testUpdateRedirection()
    {
        $request = (new ServerRequest('GET', '/manager/update'))
            ->withAttribute('id', 1);

        $this->getCrudAction(true)->expects($this->once())->method('update');

        call_user_func_array($this->crudAction, [$request]);
    }

    public function testDeleteRedirection()
    {
        $request = (new ServerRequest('DELETE', '/manager/delete'))
            ->withAttribute('id', 1);

        $this->getCrudAction(true)->expects($this->once())->method('delete');

        call_user_func_array($this->crudAction, [$request]);
    }

    public function testCreate()
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

    public function testRead()
    {
        $this->insertDatas();
        $request = new ServerRequest('GET', '/manager/read');

        $this->getContainer()->get('crud.paths.suffix.read')->shouldBeCalled();
        $this->getRenderer()->expects($this->once())->method('render');

        $action = $this->getCrudAction();
        $action->setStatement($this->getTable()->allStatement());

        $response = call_user_func_array($action, [$request]);

        $this->assertInternalType('string', $response);
    }

    public function testReadWithoutStatementDefined()
    {
        $this->insertDatas();
        $request = new ServerRequest('GET', '/manager/read');

        $this->getContainer()->get('crud.paths.suffix.read')->shouldBeCalled();
        $this->getRenderer()->expects($this->once())->method('render');

        $action = $this->getCrudAction();

        $response = call_user_func_array($action, [$request]);

        $this->assertInternalType('string', $response);
    }

    public function testUpdate()
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

    public function testDelete()
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
    
    protected function getTable()
    {
        if (!$this->testTable) {
            $this->testTable = new TestTable($this->getContainer()->reveal(), $this->database);
        }

        return $this->testTable;
    }

    private function getContainer()
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

    private function getDictionary()
    {
        if (!$this->dictionary) {
            $this->dictionary = $this->createMock(Dictionary::class);
        }

        return $this->dictionary;
    }

    private function getMessageFlash()
    {
        if (!$this->messageFlash) {
            $this->messageFlash = $this->createMock(MessageFlash::class);
        }

        return $this->messageFlash;
    }

    private function getRenderer()
    {
        if (!$this->renderer) {
            $this->renderer = $this->createMock(RendererInterface::class);
        }
       
        return $this->renderer;
    }

    private function getRouter()
    {
        if (!$this->router) {
            $this->router = $this->createMock(Router::class);
        }
       
        return $this->router;
    }

    private function insertDatas()
    {
        $statement = 'INSERT INTO test(id, label) VALUES '
            . '(1, "first"), (2, "second"), (3, "third"), (4, "fourth")';
        $this->database->query($statement);
    }
}
