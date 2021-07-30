<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use Danae\Faylin\App\Authorization\AuthorizationMiddleware;
use Danae\Faylin\App\Controllers\Backend\BackendController;
use Danae\Faylin\App\Controllers\Backend\ImageController;
use Danae\Faylin\App\Controllers\Backend\ImageResolverMiddleware;
use Danae\Faylin\App\Controllers\Backend\UserController;
use Danae\Faylin\App\Controllers\Backend\UserResolverMiddleware;

// Return a function that adds routes to the app
return function(App $app)
{
  // Backend controller routes
  $app->group('/api/v1', function(RouteCollectorProxy $group)
  {
    // Return a JWT token for an authenticated client
    $group->post('/token', [BackendController::class, 'token'])
      ->setName('token');

    // Return the capabilities of the API
    $group->get('/capabilities', [BackendController::class, 'capabilities'])
      ->add(AuthorizationMiddleware::class)
      ->setName('capabilities');

    // Image controller routes
    $group->group('/images', function(RouteCollectorProxy $group)
    {
      // Return all images as a JSON response
      $group->get('/', [ImageController::class, 'index'])
        ->add(AuthorizationMiddleware::class)
        ->setName('images.index');

      // Get an image as a JSON response
      $group->get('/{id:[A-Za-z0-9-_]+}', [ImageController::class, 'get'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
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

      // Download the contents of an image
      $group->get('/{id:[A-Za-z0-9-_]+}/download', [ImageController::class, 'download'])
        ->add(ImageResolverMiddleware::class)
        ->setName('images.download');

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
        ->add(AuthorizationMiddleware::class)
        ->setName('users.index');

      // Get the authorized user as a JSON response
      $group->get('/me', [UserController::class, 'getMe'])
        ->add(AuthorizationMiddleware::class)
        ->setName('users.get.me');

      // Get a user as a JSON response
      $group->get('/{id:[A-Za-z0-9-_]+}', [UserController::class, 'get'])
        ->add(UserResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
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
        ->add(AuthorizationMiddleware::class)
        ->setName('users.me.images');

      // Return all images owned by a user as a JSON response
      $group->get('/{id:[A-Za-z0-9-_]+}/images/', [UserController::class, 'images'])
        ->add(UserResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('users.get.images');
    });
  });
};
