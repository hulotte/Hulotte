<?php

namespace HulotteModules\Account\Middlewares;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use HulotteModules\Account\{
    Auth,
    Exceptions\NoAuthException
};

/**
 * Class LoggedInMiddleware
 *
 * @package HulotteModules\Account\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class LoggedInMiddleware implements MiddlewareInterface
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * LoggedInMiddleware constructor
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     * @throws NoAuthException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $user = $this->auth->getUser();

        if ($user === null) {
            throw new NoAuthException();
        }

        return $next->handle($request->withAttribute('user', $user));
    }
}
