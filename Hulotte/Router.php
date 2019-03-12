<?php

namespace Hulotte;

use Psr\{
    Container\ContainerInterface,
    Http\Message\ServerRequestInterface
};
use Zend\Expressive\Router\{
    FastRouteRouter,
    Route as ZendRoute
};
use Hulotte\{
    Middlewares\RouterMiddleware,
    Router\Route
};

/**
 * Class Router
 *
 * @package Hulotte
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Router
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var FastRouteRouter
     */
    private $router;

    /**
     * Router constructor
     * @param ContainerInterface $container
     * @param null|string $cache
     */
    public function __construct(ContainerInterface $container, ?string $cache = null)
    {
        $this->container = $container;
        $this->router = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => !is_null($cache),
            FastRouteRouter::CONFIG_CACHE_FILE => $cache
        ]);
    }

    /**
     * Generate CRUD routes
     * @param string $prefixPath
     * @param mixed $callable
     * @param string $prefixName
     */
    public function crud(string $prefixPath, $callable, string $prefixName): void
    {
        // Create
        $createPath = $prefixPath . $this->container->get('crud.paths.suffix.create');
        $this->get($createPath, $callable, $prefixName . '.create');
        $this->post($createPath, $callable);

        // Read
        $readPath = $prefixPath . $this->container->get('crud.paths.suffix.read');
        $this->get($readPath, $callable, $prefixName . '.read');

        // Update
        $updatePath = $prefixPath . $this->container->get('crud.paths.suffix.update');
        $this->get($updatePath . '/{id:\d+}', $callable, $prefixName . '.update');
        $this->post($updatePath . '/{id:\d+}', $callable);

        // Delete
        $deletePath = $prefixPath . $this->container->get('crud.paths.suffix.delete');
        $this->delete($deletePath . '/{id:\d+}', $callable, $prefixName . '.delete');
    }

    /**
     * Add DELETE route to the router
     * @param string $path
     * @param string|callable $callable
     * @param null|string $name
     */
    public function delete(string $path, $callable, ?string $name = null)
    {
        $this->router->addRoute(
            new ZendRoute(
                $path,
                new RouterMiddleware($this, $callable),
                ['DELETE'],
                $name
            )
        );
    }

    /**
     * Get the URI for a route
     * @var string $name
     * @var array $params
     * @var array $queryParams
     * @return null|string
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generateUri($name, $params);

        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }

        return $uri;
    }

    /**
     * Add GET route to the router
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function get(string $path, $callable, ?string $name = null): void
    {
        $this->router->addRoute(
            new ZendRoute(
                $path,
                new RouterMiddleware($this, $callable),
                ['GET'],
                $name
            )
        );
    }

    /**
     * Verify if a route match
     * @var ServerRequestInterface $request
     * @return null|Route
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedRoute()->getMiddleware()->getCallback(),
                $result->getMatchedParams()
            );
        }

        return null;
    }

    /**
     * Add POST route to the router
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function post(string $path, $callable, ?string $name = null): void
    {
        $this->router->addRoute(
            new ZendRoute(
                $path,
                new RouterMiddleware($this, $callable),
                ['POST'],
                $name
            )
        );
    }
}
