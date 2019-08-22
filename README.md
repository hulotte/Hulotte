# Hulotte

## Description
Hulotte is a PHP framework for web sites and applications.

## Installation
The easiest way to install Hulotte is to use [Composer](https://getcomposer.org/) with this command :

```bash
$ composer require hulotte/hulotte
```

This will install Hulotte and all required dependencies.

## Start project
When Hulotte has been installed via [Composer](https://getcomposer.org/), you can initialize a project with this command line

```bash
$ ./vendor/bin/hulotte init
```

This command will install basics files and folders. It also install App module.

If you need a database for your project, you can easy create it with this command line
```bash
$ ./vendor/bin/hulotte database:create yourDatabaseName
```
If your project is initialized (with a _public/index.php_ file) and if the name of the database is written to a config file like this :
```php
'database.name' => 'yourDatabaseName',
```
You can simply run the command without specifying the name of the database
```bash
$ ./vendor/bin/hulotte database:create
```


## Commands
You can create your own command. For that your class must inherit from _Symfony\Component\Console\Command\Command_ and be declared in the config file of your module like this:

```php
'commands' => DI\add([YourClass::class]),
```

## License
The Hulotte framework is licensed under the MIT license. See [License File](LICENSE) for more information.
