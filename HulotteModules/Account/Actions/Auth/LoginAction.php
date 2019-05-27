<?php

namespace HulotteModules\Account\Actions\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Hulotte\{
    Actions\RouterAwareAction,
    Renderer\RendererInterface,
    Router
};
use HulotteModules\Account\Auth;

/**
 * Class LoginAction
 *
 * @package HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class LoginAction
{
    use RouterAwareAction;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * LoginAction constructor
     * @param Auth $auth
     * @param RendererInterface $renderer
     * @param Router $router
     */
    public function __construct(Auth $auth, RendererInterface $renderer, Router $router)
    {
        $this->auth = $auth;
        $this->renderer = $renderer;
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|string
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if ($this->auth->getUser()) {
            return $this->redirect('account.dashboard');
        }

        return $this->renderer->render('@account/auth/login');
    }
}
