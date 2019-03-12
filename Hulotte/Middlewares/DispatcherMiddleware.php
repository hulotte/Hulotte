<?php

namespace Hulotte\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface,
    Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
};
use Hulotte\{
    Router\Route,
    Services\Dictionary
};

/**
 * Class DispatcherMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class DispatcherMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Dictionary;
     */
    private $dictionary;

    /**
     * DispatcherMiddleware constructor
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, Dictionary $dictionary)
    {
        $this->container = $container;
        $this->dictionary = $dictionary;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);

        if (is_null($route)) {
            return $next->handle($request);
        }

        $callback = $route->getCallback();

        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }

        $response = call_user_func_array($callback, [$request]);

        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception($this->dictionary->translate('DispatcherMiddleware:Exception'));
        }
    }
}
