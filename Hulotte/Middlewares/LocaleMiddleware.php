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
use Hulotte\Session\SessionInterface;

/**
 * Class LocaleMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class LocaleMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * LocaleMiddleware constructor
     * @param ContainerInterface $container
     * @param SessionInterface $session
     */
    public function __construct(ContainerInterface $container, SessionInterface $session)
    {
        $this->container = $container;
        $this->session = $session;
    }

    /**
     * Change locale on container if it is sent to the url
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $queryParams = $request->getUri()->getQuery();

        if (!empty($queryParams) && strpos($queryParams, 'lang') !== false) {
            $uri = $request->getUri()->getPath();
            $locale = explode('=', $queryParams)[1];

            if (in_array($locale, $this->container->get('accepted.locales'))) {
                $this->session->set('locale', $locale);
            }

            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', $uri);
        }

        return $next->handle($request);
    }
}
