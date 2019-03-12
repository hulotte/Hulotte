<?php

namespace Hulotte\Middlewares;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use Hulotte\Router;

/**
 * Class RouterMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var Router
     */
    private $router;

    /**
     * RouterMiddleware construct
     * @var Router $router
     */
    public function __construct(Router $router, $callable = null)
    {
        $this->router = $router;
        $this->callable = $callable;
    }

    /**
     * Callable getter
     * @return callable|null
     */
    public function getCallback()
    {
        return $this->callable;
    }

    /**
     * Create routes
     * @var ServerRequestInterface $request
     * @var RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $route = $this->router->match($request);

        if (is_null($route)) {
            return $next->handle($request);
        }

        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        $request = $request->withAttribute(get_class($route), $route);

        return $next->handle($request);
    }
}
