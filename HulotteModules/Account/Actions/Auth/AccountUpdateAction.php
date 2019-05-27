<?php

namespace HulotteModules\Account\Actions\Auth;

use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface
};
use Hulotte\{
    Actions\RouterAwareAction,
    Database\Hydrator,
    Renderer\RendererInterface,
    Router,
    Services\Dictionary,
    Services\Validator,
    Session\MessageFlash
};
use HulotteModules\Account\{
    Auth,
    AccountModule,
    Table\RoleTable,
    Table\UserTable
};

/**
 * Class AccountUpdateAction
 *
 * @package HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AccountUpdateAction
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
     * @var MessageFlash
     */
    private $messageFlash;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var RoleTable
     */
    private $roleTable;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * AccountUpdateAction constructor
     * @param Auth $auth
     * @param ContainerInterface $container
     * @param Dictionary $dictionary
     * @param MessageFlash $messageFlash
     * @param RendererInterface $renderer
     * @param RoleTable $roleTable
     * @param Router $router
     * @param UserTable $userTable
     */
    public function __construct(
        Auth $auth,
        ContainerInterface $container,
        Dictionary $dictionary,
        MessageFlash $messageFlash,
        RendererInterface $renderer,
        RoleTable $roleTable,
        Router $router,
        UserTable $userTable
    ) {
        $this->auth = $auth;
        $this->container = $container;
        $this->dictionary = $dictionary;
        $this->messageFlash = $messageFlash;
        $this->renderer = $renderer;
        $this->roleTable = $roleTable;
        $this->router = $router;
        $this->userTable = $userTable;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $item = $this->auth->getUser();
        $roles = $this->roleTable->allList('id', 'label');

        if ($request->getMethod() === 'POST') {
            $validator = (new Validator($this->dictionary, $request->getParsedBody()))
                ->required('civility', 'name', 'firstName', 'email', 'roles')
                ->email('email');

            if ($validator->isValid()) {
                $params = array_filter($request->getParsedBody(), function ($key) {
                    return in_array($key, ['civility', 'name', 'firstName', 'email', 'roles']);
                }, ARRAY_FILTER_USE_KEY);

                $user = $this->userTable->update($item->id, $params);
                $this->auth->setUserHashSession($user);
                $this->messageFlash->success(
                    $this->dictionary->translate('AccountUpdateAction:update', AccountModule::class)
                );

                return $this->redirect('account.dashboard');
            }

            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item, $this->container);
        }

        return $this->renderer->render('@account/auth/update', compact('item', 'errors', 'roles'));
    }
}
