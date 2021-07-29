<?php
require(__DIR__ . "/../vendor/autoload.php");

use DI\ContainerBuilder;

use Danae\Faylin\App\Bridge\AppFactory;


// Create the container builder
$containerBuilder = new ContainerBuilder();

$settings = require(__DIR__ . "/../app/settings.php");
$settings($containerBuilder);

$dependencies = require(__DIR__ . "/../app/dependencies.php");
$dependencies($containerBuilder);

// Create the container
$container = $containerBuilder->build();


// Create the application
$app = AppFactory::create($container);
$app->setBasePath($container->get('root'));

$middleware = require(__DIR__ . "/../app/middleware.php");
$middleware($app);

$routes = require(__DIR__ . "/../app/routes.php");
$routes($app);

// Run the application
$app->run();
