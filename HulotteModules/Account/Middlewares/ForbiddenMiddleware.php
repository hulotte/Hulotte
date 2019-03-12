<?php

namespace HulotteModules\Account\Middlewares;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use Hulotte\{
    Response\RedirectResponse,
    Services\Dictionary,
    Session\MessageFlash,
    Session\SessionInterface
};
use HulotteModules\Account\{
    AccountModule,
    Exceptions\ForbiddenException,
    Exceptions\NoAuthException
};

/**
 * Class ForbiddenMiddleware
 *
 * @package HulotteModules\Account\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ForbiddenMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $dashboardPath;

    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var string
     */
    private $loginPath;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ForbiddenMiddleware constructor
     * @param string $loginPath
     * @param string $dashboardPath
     * @param Dictionary $dictionary
     * @param SessionInterface $session
     */
    public function __construct(
        string $loginPath,
        string $dashboardPath,
        Dictionary $dictionary,
        SessionInterface $session
    ) {
        $this->loginPath = $loginPath;
        $this->dashboardPath = $dashboardPath;
        $this->dictionary = $dictionary;
        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        try {
            return $next->handle($request);
        } catch (NoAuthException $exception) {
            $this->session->set('account.auth.redirect', $request->getUri()->getPath());

            (new MessageFlash($this->session))
                ->error(
                    $this->dictionary->translate('ForbiddenMiddleware:errorNotConnected', AccountModule::class)
                );

            return new RedirectResponse($this->loginPath);
        } catch (ForbiddenException $exception) {
            (new MessageFlash($this->session))
                ->error(
                    $this->dictionary->translate('ForbiddenMiddleware:errorNotPermission', AccountModule::class)
                );

            return new RedirectResponse($this->dashboardPath);
        }
    }
}
