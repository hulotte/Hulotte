<?php

namespace Hulotte\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait RouterAwareAction
 * Add methods related to the use of the router
 *
 * @package Hulotte\Actions
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
trait RouterAwareAction
{
    /**
     * Return a redirection response
     * @param string $path
     * @param array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);

        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}
