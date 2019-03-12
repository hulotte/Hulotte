<?php

namespace Hulotte\Middlewares;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use Hulotte\{
    Exceptions\CsrfException,
    Services\Dictionary
};

/**
 * Class CsrfMiddleware
 *
 * @package Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var string
     */
    private $formKey;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     * var array|\ArrayAccess
     */
    private $session;

    /**
     * CsrfMiddleware constructor
     * @param array|\ArrayAccess $session
     * @param Dictionary $dictionary
     * @param int $limit
     * @param string $formKey
     * @param string $sessionKey
     */
    public function __construct(
        $session,
        Dictionary $dictionary,
        int $limit = 50,
        string $formKey = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->validSession($session);
        $this->dictionary = $dictionary;
        $this->formKey = $formKey;
        $this->session = $session;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    /**
     * Generate a random token
     * @return string
     * @throws \Exception
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();

        return $token;
    }

    /**
     * FormKey getter
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }

    /**
     * Session getter
     * @return array|\ArrayAccess
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ? : [];

            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];

                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);

                    return $next->handle($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $next->handle($request);
        }
    }

    /**
     * Limit the number of tokens
     */
    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];

        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }

        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * @throws CsrfException
     */
    private function reject(): void
    {
        throw new CsrfException();
    }

    /**
     * @param string $token
     */
    private function useToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });

        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * Check if $session is an array or an instance of ArrayAccess
     * @param mixed $session
     * @throws \TypeError
     */
    private function validSession($session): void
    {
        if (!is_array($session) && !$session instanceof  \ArrayAccess) {
            throw new \TypeError($this->dictionary->translate('CsrfMiddleware:TypeError'));
        }
    }
}
