<?php

namespace Hulotte\Actions;

use Psr\{
    Container\ContainerInterface,
    Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface
};
use Hulotte\{
    Actions\RouterAwareAction,
    Database\Hydrator,
    Database\StatementBuilder,
    Renderer\RendererInterface,
    Router,
    Services\Dictionary,
    Services\Validator,
    Session\MessageFlash
};

/**
 * Class CrudAction
 *
 * @package Hulotte\Actions
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class CrudAction
{
    use RouterAwareAction;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The field labels
     * @var array
     */
    protected $fields;

    /**
     * Define the number of items per page
     * @var int
     */
    protected $paginationNbr = 15;

    /**
     * The path for the redirection
     * @var string
     */
    protected $redirectionPath;

    /**
     * The prefix route name
     * @var string
     */
    protected $routePrefix;

    /**
     * The statement for the read view
     * @var StatementBuilder
     */
    protected $statement;

    /**
     * @var mixed
     */
    protected $table;

    /**
     * Prefix of the path to the view
     * @var string
     */
    protected $viewPath;

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
     * @var Router
     */
    private $router;

    /**
     * CrudAction constructor
     * @param ContainerInterface $container
     * @param Dictionary $dictionary
     * @param MessageFlash $messageFlash
     * @param RendererInterface $renderer
     * @param Router $router
     * @param mixed $table A child class of Hulotte\Database\Table
     */
    public function __construct(
        ContainerInterface $container,
        Dictionary $dictionary,
        MessageFlash $messageFlash,
        RendererInterface $renderer,
        Router $router,
        $table
    ) {
        $this->container = $container;
        $this->dictionary = $dictionary;
        $this->messageFlash = $messageFlash;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }

        $suffixCreate = $this->container->get('crud.paths.suffix.create');
        $suffixLength = mb_strlen($suffixCreate);

        if (substr((string)$request->getUri(), -$suffixLength) === $suffixCreate) {
            return $this->create($request);
        }

        if ($request->getAttribute('id')) {
            return $this->update($request);
        }

        return $this->read($request);
    }

    /**
     * Fields setter
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * RedirectionPath setter
     * @param string $path
     */
    public function setRedirectionPath(string $path): void
    {
        $this->redirectionPath = $path;
    }

    /**
     * Statement setter
     * @param StatementBuilder $statement
     */
    public function setStatement(StatementBuilder $statement): void
    {
        $this->statement = $statement;
    }

    /**
     * Create an item
     * @param ServerRequestInterface $request
     * @return string|ResponseInterface
     */
    protected function create(ServerRequestInterface $request)
    {
        $item = $this->container->get($this->table->getEntity());

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->create($this->getCreateParams($request, $item));
                $this->messageFlash->success($this->dictionary->translate('CrudError:create'));

                return $this->redirect($this->redirectionPath);
            }

            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item, $this->container);
        }

        return $this->renderer->render(
            $this->viewPath . $this->container->get('crud.paths.suffix.create'),
            $this->setFormParams(compact('item', 'errors'))
        );
    }

    /**
     * Delete an item
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->table->delete($request->getAttribute('id'));
        $this->messageFlash->success($this->dictionary->translate('CrudError:delete'));

        return $this->redirect($this->redirectionPath);
    }

    /**
     * Get parameters if on the fields for create form
     * @param ServerRequestInterface $request
     * @param null|mixed $item
     * @return array
     */
    protected function getCreateParams(ServerRequestInterface $request, $item = null): array
    {
        return $this->getParams($request, $item);
    }

    /**
     * Get parameters if on the fields
     * @param ServerRequestInterface $request
     * @param null|mixed $item
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $item = null): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, $this->fields);
        }, ARRAY_FILTER_USE_KEY);
        
        return $params;
    }

    /**
     * Get parameters if on the fields for update form
     * @param ServerRequestInterface $request
     * @param null|mixed $item
     * @return array
     */
    protected function getUpdateParams(ServerRequestInterface $request, $item = null): array
    {
        return $this->getParams($request, $item);
    }

    /**
     * Define Validator
     * @param ServerRequestInterface $request
     * @return Validator
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return new Validator(
            $this->dictionary,
            $request->getParsedBody()
        );
    }

    /**
     * Load view with all items paginated
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function read(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->paginate(
            $this->statement ?? $this->table->allStatement(),
            $this->paginationNbr,
            $params['p'] ?? 1
        );
        
        return $this->renderer->render(
            $this->viewPath . $this->container->get('crud.paths.suffix.read'),
            compact('items')
        );
    }

    /**
     * Define the parameters to send to the view
     * @param array $params
     * @return array
     */
    protected function setFormParams(array $params): array
    {
        return $params;
    }

    /**
     * Update an item
     * @param ServerRequestInterface $request
     * @return string|ResponseInterface
     */
    protected function update(ServerRequestInterface $request)
    {
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->update($item->id, $this->getUpdateParams($request, $item));
                $this->messageFlash->success($this->dictionary->translate('CrudError:update'));

                return $this->redirect($this->redirectionPath);
            }

            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item, $this->container);
        }

        return $this->renderer->render(
            $this->viewPath . $this->container->get('crud.paths.suffix.update'),
            $this->setFormParams(compact('item', 'errors'))
        );
    }
}
