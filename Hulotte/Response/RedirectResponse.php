<?php

namespace Hulotte\Response;

use GuzzleHttp\Psr7\Response;

/**
 * Class RedirectResponse
 *
 * @package Hulotte\Response
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RedirectResponse extends Response
{
    /**
     * RedirectResponse constructor
     * @param string $url
     */
    public function __construct(string $url)
    {
        parent::__construct(200, ['location' => $url]);
    }
}
