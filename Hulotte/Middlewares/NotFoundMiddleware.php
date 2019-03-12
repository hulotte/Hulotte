<?php

namespace Hulotte\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use Hulotte\Services\Dictionary;

/**
 * Class NotFoundMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class NotFoundMiddleware implements MiddlewareInterface
{
    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * NotFoundMiddleware constructor
     * @param Dictionary $dictionary
     */
    public function __construct(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * Return 404 page if no page found
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        return new Response(404, [], $this->dictionary->translate('NotFoundMiddleware:errorMessage'));
    }
}
