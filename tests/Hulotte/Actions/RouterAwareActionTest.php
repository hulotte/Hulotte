<?php

namespace Tests\Hulotte\Actions;

use PHPUnit\Framework\TestCase;
use Hulotte\{
    Actions\RouterAwareAction,
    Router
};

/**
 * Class RouterAwareActionTest
 *
 * @package Tests\Hulotte\Actions
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RouterAwareActionTest extends TestCase
{
    private $routerAwareAction;

    public function setUp()
    {
        $this->routerAwareAction = $this->getObjectForTrait(RouterAwareAction::class);
    }

    public function testredirect()
    {
        $this->addRouterGenerateUri();
        $response = $this->routerAwareAction->redirect('/test');

        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testRedirectWithParams()
    {
        $this->addRouterGenerateUri(true);

        $response = $this->routerAwareAction->redirect('/test', ['id' => 1, 'slug' => 'le-test']);

        $this->assertEquals(301, $response->getStatusCode());
    }

    private function addRouterGenerateUri(bool $isParams = false)
    {
        $routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()->getMock();
        
        if ($isParams) {
            $routerMock->method('generateUri')->willReturn('/test/1/le-test');
        } else {
            $routerMock->method('generateUri')->willReturn('/test');
        }

        $this->routerAwareAction->router = $routerMock;
    }
}
