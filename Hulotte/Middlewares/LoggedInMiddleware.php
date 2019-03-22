<?php

namespace Hulotte\Middlewares;

use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface,
    Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
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
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get(AuthInterface::class);
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     * @throws NoAuthException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        if($this->auth === null){
            return $next->handle($request);
        }
        
        $user = $this->auth->getUser();

        if($user === null){
            throw new NoAuthException();
        }

        return $next->handle($request->withAttribute('user', $user));
    }
}
