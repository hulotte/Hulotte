<?php

namespace Hulotte;

use DI\ContainerBuilder;
use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface,
    Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
};
use Hulotte\{
    Middlewares\RestrictedRouteMiddleware,
    Services\Dictionary
};

/**
 * Class App
 *
 * @package Hulotte
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class App implements RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var array
     */
    public $middlewares;

    /**
     * @var array
     */
    private $modules = [];

    /**
     * Add new module
     * @var string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;

        return $this;
    }

    /**
     * Instanciate the container if not exists and return it
     * @return ContainerInterface
     * @throws \Exception
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = getenv('PROJECT_ENV') ?: 'prod';

            $builder->addDefinitions(__DIR__ . '/config.php');

            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }

            if ($env === 'prod') {
                $builder->enableCompilation('tmp');
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }

            $this->container = $builder->build();
        }

        return $this->container;
    }

    /**
     * Modules getter
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Load middlewares
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();

        if (is_null($middleware)) {
            throw new \Exception(
                $this->getContainer()->get(Dictionary::class)->translate('MiddlewareError:handle_exception')
            );
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    /**
     * Add a middleware of the current pipe
     * @param string $middleware
     * @param array $restrictedPaths
     * @return App
     * @throws \Exception
     */
    public function pipe(string $middleware, array $restrictedPaths = []): self
    {
        if (empty($restrictedPaths)) {
            $this->middlewares[] = $middleware;
        } else {
            $this->middlewares[] = new RestrictedRouteMiddleware(
                $this->getContainer(),
                $restrictedPaths,
                $middleware
            );
        }
        
        return $this;
    }

    /**
     * Launch the application
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        return $this->handle($request);
    }

    /**
     * Define middleware
     * @return mixed
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }

            $this->index++;

            return $middleware;
        }

        return null;
    }
}
