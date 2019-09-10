<?php

return <<< 'EOD'
<?php

namespace %MODULE_NAME%\Actions;

use Psr\Http\Message\ServerRequestInterface;
use Hulotte\Renderer\RendererInterface;

/**
 * Class IndexAction
 *
 * @package %MODULE_NAME%\Actions
 */
class IndexAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * IndexAction constructor
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        return $this->renderer->render('@%LCFIRST_MODULE_NAME%/index');
    }
}

EOD;
