<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;

use Danae\Faylin\App\Authorization\AuthorizationMiddleware;
use Danae\Faylin\App\Controllers\AuthorizationController;
use Danae\Faylin\App\Controllers\BackendController;
use Danae\Faylin\App\Controllers\FrontendController;
use Danae\Faylin\App\Controllers\ImageController;
use Danae\Faylin\App\Controllers\ImageResolverMiddleware;
use Danae\Faylin\App\Controllers\UserController;
use Danae\Faylin\App\Controllers\UserResolverMiddleware;

// Return a function that adds routes to the app
return function(App $app)
{
  // Backend controller routes
  $app->group('/api/v1', function(RouteCollectorProxy $group)
  {
    // Request an access token
    $group->post('/token', [AuthorizationController::class, 'token'])
      ->setName('token');

    // Return the capabilities of the API
    $group->get('/capabilities', [BackendController::class, 'capabilities'])
      ->setName('capabilities');

    // Image controller routes
    $group->group('/images', function(RouteCollectorProxy $group)
    {
      // Return all images as a JSON response
      $group->get('/', [ImageController::class, 'index'])
        ->setName('images.index');

      // Get an image as a JSON response
      $group->get('/{id:[A-Za-z0-9-_]+}', [ImageController::class, 'get'])
        ->add(ImageResolverMiddleware::class)
        ->setName('images.get');

      // Patch an image and return the image as a JSON response
      $group->patch('/{id:[A-Za-z0-9-_]+}', [ImageController::class, 'patch'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('images.patch');

      // Delete an image
      $group->delete('/{id:[A-Za-z0-9-_]+}', [ImageController::class, 'delete'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('images.delete');

      // Upload an image
      $group->post('/upload', [ImageController::class, 'upload'])
        ->add(AuthorizationMiddleware::class)
        ->setName('images.upload');

      // Replace an image
      $group->post('/{id:[A-Za-z0-9-_]+}/upload', [ImageController::class, 'replace'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('images.replace');
    });

    // User controller routes
    $group->group('/users', function(RouteCollectorProxy $group)
    {
      // Return all users as a JSON response
      $group->get('/', [UserController::class, 'index'])
        ->setName('users.index');

      // Get the authorized user as a JSON response
      $group->get('/me', [UserController::class, 'getMe'])
        ->add(AuthorizationMiddleware::class)
        ->setName('users.get.me');

      // Get a user as a JSON response
      $group->get('/{id:[A-Za-z0-9-_]+}', [UserController::class, 'get'])
        ->add(UserResolverMiddleware::class)
        ->setName('users.get');

      // Patch the authorized user and return the user as a JSON response
      $group->patch('/me', [UserController::class, 'patchMe'])
        ->add(AuthorizationMiddleware::class)
        ->setName('users.patch.me');

      // Patch a user and return the user as a JSON response
      $group->patch('/{id:[A-Za-z0-9-_]+}', [UserController::class, 'patch'])
        ->add(UserResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('users.patch');

      // Return all images owned by the authorized user as a JSON response
      $group->get('/me/images/', [UserController::class, 'imagesMe'])
        ->setName('users.me.images');

      // Return all images owned by a user as a JSON response
      $group->get('/{id:[A-Za-z0-9-_]+}/images/', [UserController::class, 'images'])
        ->add(UserResolverMiddleware::class)
        ->setName('users.get.images');
    });
  });

  // Frontend controller routes
  $app->group('', function(RouteCollectorProxy $group)
  {
    // Routes doubled from the frontend app
    $group->get('/', [FrontendController::class, 'render']);
    $group->get('/login', [FrontendController::class, 'render']);
    $group->get('/logout', [FrontendController::class, 'render']);
    $group->get('/users', [FrontendController::class, 'render']);
    $group->get('/users/{id:[A-Za-z0-9-_]+}', [FrontendController::class, 'render']);
    $group->get('/images', [FrontendController::class, 'render']);
    $group->get('/images/{id:[A-Za-z0-9-_]+}', [FrontendController::class, 'render']);

    // Download the contents of an image
    $group->get('/{id:[A-Za-z0-9-_]+}[.{extension}]', [ImageController::class, 'download'])
      ->add(ImageResolverMiddleware::class)
      ->setName('images.download');
  });
};
