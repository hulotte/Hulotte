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
 * @coversDefaultClass \Hulotte\Actions\RouterAwareAction
 */
class RouterAwareActionTest extends TestCase
{
    /**
     * @var RouterAwareAction
     */
    private $routerAwareAction;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $this->routerAwareAction = $this->getObjectForTrait(RouterAwareAction::class);
    }

    /**
     * @covers redirect
     */
    public function testRedirect(): void
    {
        $this->addRouterGenerateUri();
        $response = $this->routerAwareAction->redirect('/test');

        $this->assertEquals(301, $response->getStatusCode());
    }

    /**
     * @covers redirect
     */
    public function testRedirectWithParams(): void
    {
        $this->addRouterGenerateUri(true);

        $response = $this->routerAwareAction->redirect('/test', ['id' => 1, 'slug' => 'le-test']);

        $this->assertEquals(301, $response->getStatusCode());
    }

    /**
     * @param bool $isParams
     */
    private function addRouterGenerateUri(bool $isParams = false): void
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
