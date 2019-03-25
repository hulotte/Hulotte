<?php

namespace HulotteModules\Account\Actions\Auth;

use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface
};
use Hulotte\{
    Actions\RouterAwareAction,
    Renderer\RendererInterface,
    Router,
    Services\Dictionary,
    Services\Validator,
    Session\MessageFlash,
    Session\SessionInterface
};
use HulotteModules\Account\{
    AccountModule,
    Auth,
    Table\UserTable
};

/**
 * Class AccountPasswordUpdateAction
 *
 * @package HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AccountPasswordUpdateAction
{
    use RouterAwareAction;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var ContainerInterface
     */
    private $container;

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
     * @var UserTable
     */
    private $userTable;

    /**
     * AccountPasswordUpdateAction constructor.
     * @param Auth $auth
     * @param ContainerInterface $container
     * @param Dictionary $dictionary
     * @param RendererInterface $renderer
     * @param Router $router
     * @param SessionInterface $session
     * @param UserTable $userTable
     */
    public function __construct(
        Auth $auth,
        ContainerInterface $container,
        Dictionary $dictionary,
        RendererInterface $renderer,
        Router $router,
        SessionInterface $session,
        UserTable $userTable
    ) {
        $this->auth = $auth;
        $this->container = $container;
        $this->dictionary = $dictionary;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->session = $session;
        $this->userTable = $userTable;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     * @throws \HulotteModules\Account\Exceptions\NoAuthException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $validator = (new Validator($this->dictionary, $params))
            ->required('old', 'new', 'verify');
        
        if ($validator->isValid()) {
            $user = $this->auth->getUser();

            if (!password_verify($params['old'], $user->password)) {
                (new MessageFlash($this->session))->error(
                    $this->dictionary->translate('AccountPasswordUpdateAction:wrongOldPassword', AccountModule::class)
                );

                return $this->redirect('account.auth.password.update');
            }

            if ($params['new'] !== $params['verify']) {
                (new MessageFlash($this->session))->error(
                    $this->dictionary->translate(
                        'AccountPasswordUpdateAction:wrongPasswordVerify',
                        AccountModule::class
                    )
                );

                return $this->redirect('account.auth.password.update');
            }

            $this->userTable->update($user->id, ['password' => password_hash($params['new'], PASSWORD_DEFAULT)]);
            (new MessageFlash($this->session))->success(
                $this->dictionary->translate('AccountPasswordUpdateAction:update', AccountModule::class)
            );

            return $this->redirect('account.dashboard');
        }

        $errors = $validator->getErrors();

        return $this->renderer->render('@account/auth/password', compact('errors'));
    }
}
