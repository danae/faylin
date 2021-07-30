<?php
use Slim\App;

use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\TwigMiddleware;

use Danae\Faylin\App\Handlers\HttpErrorHandler;


// Return a function that adds middleware to the app
return function(App $app)
{
  // Add content length middleware
  $app->add(ContentLengthMiddleware::class);

  // Add Twig middleware
  $app->add(TwigMiddleware::class);

  // Add body parsing middleware
  $app->addBodyParsingMiddleware();

  // Add routing middleware
  $app->addRoutingMiddleware();

  // Ad method override middleware
  $app->add(MethodOverrideMiddleware::class);

  // Add error middleware
  $errorHandler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory());
  $errorMiddleware = $app->addErrorMiddleware(true, false, false);
  $errorMiddleware->setDefaultErrorHandler($errorHandler);
};
