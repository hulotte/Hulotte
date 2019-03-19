<?php

namespace Hulotte\Commands\Details\Init;

use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};
use Hulotte\Commands\Details\CommandInterface;

/**
 * Class PublicCommand
 * @package Hulotte\Commands\Details\Init
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PublicCommand implements CommandInterface
{
     /**
     * Executes list of commands
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public static function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Creating public folder with index file');
        self::createPublicFolder();
        self::createIndexFile();
        $output->writeln('Public folder with index file is created');
    }

    /**
     * Create index file and write content
     */
    private static function createIndexFile(): void
    {
        $indexFile = fopen('public/index.php', 'a+');

        $content = <<< 'EOD'
<?php

use \GuzzleHttp\Psr7\ServerRequest;
use \Middlewares\Whoops;
use function \Http\Response\send;
use App\AppModule;
use Hulotte\{
    App,
    Middlewares\CsrfMiddleware,
    Middlewares\DispatcherMiddleware,
    Middlewares\LocaleMiddleware,
    Middlewares\MethodMiddleware,
    Middlewares\NotFoundMiddleware,
    Middlewares\RouterMiddleware,
    Middlewares\TrailingSlashMiddleware
};
use HulotteModules\Account\{
    AccountModule,
    Middlewares\ForbiddenMiddleware,
    Middlewares\LoggedInMiddleware
};

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

// Load modules
$app = (new App())
    ->addModule(AccountModule::class)
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

        fputs($indexFile, $content);
        fclose($indexFile);
    }

    /**
     * Create public folder
     */
    private static function createPublicFolder(): void
    {
        mkdir('public');
    }
}
