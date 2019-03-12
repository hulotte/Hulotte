<?php

namespace Hulotte\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};

/**
 * Class TrailingSlashMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TrailingSlashMiddleware implements MiddlewareInterface
{
    /**
     * Delete the ending slash of the URL
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $uri = $request->getUri()->getPath();

        if (!empty($uri) && $uri !== "/" && $uri[-1] === "/") {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }

        return $next->handle($request);
    }
}
