<?php

namespace Hulotte\Middlewares;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use Hulotte\{
    Auth\AuthInterface,
    Exceptions\NoAuthException
};

/**
 * Class LoggedInMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class LoggedInMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * LoggedInMiddleware constructor
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth)
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

        if($user === null){
            throw new NoAuthException();
        }

        return $next->handle($request->withAttribute('user', $user));
    }
}
