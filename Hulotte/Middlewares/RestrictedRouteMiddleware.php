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
    Exceptions\ForbiddenException
};

/**
 * Class RestrictedRouteMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RestrictedRouteMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $middleware;

    /**
     * @var array
     */
    private $restrictedPaths;

    /**
     * RoutePrefixedMiddleware constructor
     * @param ContainerInterface $container
     * @param string $middleware
     * @param array $restrictedPaths
     */
    public function __construct(
        ContainerInterface $container,
        string $middleware,
        array $restrictedPaths
    ) {
        $this->container = $container;
        $this->middleware = $middleware;
        $this->restrictedPaths = $restrictedPaths;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     * @throws ForbiddenException
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        foreach ($this->restrictedPaths as $permission => $restrictedPath) {
            if (!is_int($permission)) {
                if (strpos($permission, '/') !== false) {
                    $permission = explode('/', $permission);
                }

                if (strpos($path, $restrictedPath) === 0
                    && !$this->container->get(AuthInterface::class)->hasPermission($permission)
                ) {
                    throw new ForbiddenException();
                }
            } else {
                if (strpos($path, $restrictedPath) === 0) {
                    return $this->container->get($this->middleware)->process($request, $next);
                }
            }
        }
        
        return $next->handle($request);
    }
}
