<?php

namespace HulotteModules\Account\Actions\Auth;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Hulotte\{
    Renderer\RendererInterface,
    Response\RedirectResponse,
    Services\Dictionary,
    Session\MessageFlash
};
use HulotteModules\Account\{
    AccountModule,
    Auth
};

/**
 * Class LogoutAction
 *
 * @package HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class LogoutAction
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var MessageFlash
     */
    private $messageFlash;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * LogoutAction constructor
     * @param Auth $auth
     * @param Dictionary $dictionary
     * @param MessageFlash $messageFlash
     * @param RendererInterface $renderer
     */
    public function __construct(
        Auth $auth,
        Dictionary $dictionary,
        MessageFlash $messageFlash,
        RendererInterface $renderer
    ) {
        $this->auth = $auth;
        $this->dictionary = $dictionary;
        $this->messageFlash = $messageFlash;
        $this->renderer = $renderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->auth->logout();
        $this->messageFlash->success(
            $this->dictionary->translate('LogoutAction:offline', AccountModule::class)
        );

        return new RedirectResponse('/');
    }
}
