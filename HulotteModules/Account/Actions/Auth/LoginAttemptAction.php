<?php

namespace HulotteModules\Account\Actions\Auth;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Hulotte\{
    Actions\RouterAwareAction,
    Renderer\RendererInterface,
    Response\RedirectResponse,
    Router,
    Services\Dictionary,
    Services\Validator,
    Session\SessionInterface
};
use HulotteModules\Account\Auth;

/**
 * Class LoginAttemptAction
 *
 * @package HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class LoginAttemptAction
{
    use RouterAwareAction;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var RendererInterface
     */
    private $renderer;
    
    /**
     * @var Router
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * LoginAttemptAction constructor
     * @param Auth $auth
     * @param Dictionary $dictionary
     * @param RendererInterface $renderer
     * @param Router $router
     * @param SessionInterface $session
     */
    public function __construct(
        Auth $auth,
        Dictionary $dictionary,
        RendererInterface $renderer,
        Router $router,
        SessionInterface $session
    ) {
        $this->auth = $auth;
        $this->dictionary = $dictionary;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $validator = (new Validator($this->dictionary, $params))
            ->required('email', 'password')
            ->email('email');
        
        if ($validator->isValid()) {
            $user = $this->auth->login($params['email'], $params['password']);
            
            if ($user) {
                $path = $this->session->get('account.auth.redirect') ?: $this->router->generateUri('account.dashboard');
                $this->session->delete('account.auth.redirect');
                
                return new RedirectResponse($path);
            } else {
                return $this->redirect('account.auth.login');
            }
        }

        $errors = $validator->getErrors();
        $item = $params;

        return $this->renderer->render('@account/auth/login', compact('item', 'errors'));
    }
}
