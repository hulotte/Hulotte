<?php

namespace Hulotte\Middlewares;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};

/**
 * Class MethodMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class MethodMiddleware implements MiddlewareInterface
{
    /**
     * @var ServerRequestInterface $request
     * @var RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        if (array_key_exists('_method', $parsedBody)
            && in_array($parsedBody['_method'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }

        return $next->handle($request);
    }
}
