<?php

namespace HulotteModules\Account\Actions\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Hulotte\Renderer\RendererInterface;

/**
 * Class AccountPasswordAction
 *
 * @package HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AccountPasswordAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * LoginAction constructor
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        return $this->renderer->render('@account/auth/password');
    }
}
