<?php

namespace HulotteModules\Account\Actions\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Hulotte\Renderer\RendererInterface;
use HulotteModules\Account\{
    Auth,
    Table\RoleTable
};

/**
 * Class AccountAction
 *
 * @package HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AccountAction
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var RoleTable
     */
    private $roleTable;

    /**
     * AccountAction constructor
     * @param Auth $auth
     * @param RendererInterface $renderer
     * @param RoleTable $roleTable
     */
    public function __construct(Auth $auth, RendererInterface $renderer, RoleTable $roleTable)
    {
        $this->auth = $auth;
        $this->renderer = $renderer;
        $this->roleTable = $roleTable;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        $item = $this->auth->getUser();
        $roles = $this->roleTable->allList('id', 'label');

        return $this->renderer->render('@account/auth/update', compact('item', 'roles'));
    }
}
