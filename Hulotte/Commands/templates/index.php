<?php

return <<< 'EOD'
<?php

use \GuzzleHttp\Psr7\ServerRequest;
use \Middlewares\Whoops;
use function \Http\Response\send;
use App\AppModule;
use Hulotte\{
    App,
    Middlewares\CsrfMiddleware,
    Middlewares\DispatcherMiddleware,
    Middlewares\ForbiddenMiddleware,
    Middlewares\LocaleMiddleware,
    Middlewares\LoggedInMiddleware,
    Middlewares\MethodMiddleware,
    Middlewares\NotFoundMiddleware,
    Middlewares\RouterMiddleware,
    Middlewares\TrailingSlashMiddleware
};

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

// Load modules
$app = (new App())
    ->addModule(AppModule::class);

// Load middlewares
$app->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(LocaleMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe(LoggedInMiddleware::class, $app->getContainer()->get('restricted.paths'))
    ->pipe(MethodMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== 'cli') {
    // Run application
    $response = $app->run(ServerRequest::fromGlobals());
    send($response);
}

EOD;
