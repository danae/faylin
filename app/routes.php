<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;

use Danae\Faylin\App\Authorization\AuthorizationMiddleware;
use Danae\Faylin\App\Authorization\AuthorizationOptionalMiddleware;
use Danae\Faylin\App\Controllers\AuthorizationController;
use Danae\Faylin\App\Controllers\BackendController;
use Danae\Faylin\App\Controllers\CollectionController;
use Danae\Faylin\App\Controllers\CollectionResolverMiddleware;
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

    // Collection controller routes
    $group->group('/collections', function(RouteCollectorProxy $group)
    {
      // Return all collections as a JSON response
      $group->get('/', [CollectionController::class, 'getCollections'])
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('collections.index');

      // Post a new collection and return the collection as a JSON response
      $group->post('/', [CollectionController::class, 'postCollection'])
        ->add(AuthorizationMiddleware::class)
        ->setName('collections.post');

      // Get a collection as a JSON response
      $group->get('/{collectionId:[A-Za-z0-9-_]+}', [CollectionController::class, 'getCollection'])
        ->add(CollectionResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('collections.get');

      // Patch a collection and return the collection as a JSON response
      $group->patch('/{collectionId:[A-Za-z0-9-_]+}', [CollectionController::class, 'patchCollection'])
        ->add(CollectionResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('collections.patch');

      // Delete a collection
      $group->delete('/{collectionId:[A-Za-z0-9-_]+}', [CollectionController::class, 'deleteCollection'])
        ->add(CollectionResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('collections.delete');

      // Get all images in a collection as a JSON response
      $group->get('/{collectionId:[A-Za-z0-9-_]+}/images/', [CollectionController::class, 'getCollectionImages'])
        ->add(CollectionResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('collections.images.index');

      // Put an image in a collection
      $group->put('/{collectionId:[A-Za-z0-9-_]+}/images/{imageId:[A-Za-z0-9-_]+}', [CollectionController::class, 'putCollectionImage'])
        ->add(ImageResolverMiddleware::class)
        ->add(CollectionResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('collections.images.put');

      // Delete an image in a collection
      $group->delete('/{collectionId:[A-Za-z0-9-_]+}/images/{imageId:[A-Za-z0-9-_]+}', [CollectionController::class, 'deleteCollectionImage'])
        ->add(ImageResolverMiddleware::class)
        ->add(CollectionResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('collections.images.delete');
    });

    // Image controller routes
    $group->group('/images', function(RouteCollectorProxy $group)
    {
      // Return all images as a JSON response
      $group->get('/', [ImageController::class, 'getImages'])
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('images.index');

      // Get an image as a JSON response
      $group->get('/{imageId:[A-Za-z0-9-_]+}', [ImageController::class, 'getImage'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('images.get');

      // Patch an image and return the image as a JSON response
      $group->patch('/{imageId:[A-Za-z0-9-_]+}', [ImageController::class, 'patchImage'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('images.patch');

      // Delete an image
      $group->delete('/{imageId:[A-Za-z0-9-_]+}', [ImageController::class, 'deleteImage'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('images.delete');

      // Upload an image
      $group->post('/upload', [ImageController::class, 'uploadImage'])
        ->add(AuthorizationMiddleware::class)
        ->setName('images.upload');

      // Replace an image
      $group->post('/{imageId:[A-Za-z0-9-_]+}/upload', [ImageController::class, 'replaceImage'])
        ->add(ImageResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('images.replace');
    });

    // User controller routes
    $group->group('/users', function(RouteCollectorProxy $group)
    {
      // Return all users as a JSON response
      $group->get('/', [UserController::class, 'getUsers'])
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('users.index');

      // Get a user as a JSON response
      $group->get('/{userId:[A-Za-z0-9-_]+}', [UserController::class, 'getUser'])
        ->add(UserResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('users.get');

      // Patch a user and return the user as a JSON response
      $group->patch('/{userId:[A-Za-z0-9-_]+}', [UserController::class, 'patchUser'])
        ->add(UserResolverMiddleware::class)
        ->add(AuthorizationMiddleware::class)
        ->setName('users.patch');

      // Return all collections owned by a user as a JSON response
      $group->get('/{userId:[A-Za-z0-9-_]+}/collections/', [UserController::class, 'getUserCollections'])
        ->add(UserResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('users.collections.index');

      // Return all images owned by a user as a JSON response
      $group->get('/{userId:[A-Za-z0-9-_]+}/images/', [UserController::class, 'getUserImages'])
        ->add(UserResolverMiddleware::class)
        ->add(AuthorizationOptionalMiddleware::class)
        ->setName('users.images.index');
    });

    // Authorized user routes
    $group->group('/me', function(RouteCollectorProxy $group)
    {
      // Get the authorized user as a JSON response
      $group->get('', [UserController::class, 'getAuthorizedUser'])
        ->add(AuthorizationMiddleware::class)
        ->setName('me.get');

      // Patch the authorized user and return the user as a JSON response
      $group->patch('', [UserController::class, 'patchAuthorizedUser'])
        ->add(AuthorizationMiddleware::class)
        ->setName('me.patch');

      // Update the email address of the authorized user and return the user as a JSON response
      $group->post('/email', [UserController::class, 'updateAuthorizedUserEmail'])
        ->add(AuthorizationMiddleware::class)
        ->setName('me.updateEmail');

      // Update the password of the authorized user and return the user as a JSON response
      $group->post('/password', [UserController::class, 'updateAuthorizedUserPassword'])
        ->add(AuthorizationMiddleware::class)
        ->setName('me.updatePassword');

      // Delete the authorized user
      $group->delete('', [UserController::class, 'deleteAuthorizedUser'])
        ->add(AuthorizationMiddleware::class)
        ->setName('me.delete');

      // Return all collections owned by the authorized user as a JSON response
      $group->get('/collections/', [UserController::class, 'getAuthorizedUserCollections'])
        ->add(AuthorizationMiddleware::class)
        ->setName('me.collections.index');

      // Return all images owned by the authorized user as a JSON response
      $group->get('/images/', [UserController::class, 'getAuthorizedUserImages'])
        ->add(AuthorizationMiddleware::class)
        ->setName('me.images.index');
    });
  });

  // Frontend controller routes
  $app->group('', function(RouteCollectorProxy $group)
  {
    // Routes doubled from the frontend app
    $group->get('/', [FrontendController::class, 'render']);
    $group->get('/login', [FrontendController::class, 'render']);
    $group->get('/logout', [FrontendController::class, 'render']);
    $group->get('/settings', [FrontendController::class, 'render']);
    $group->get('/images/{id:[0-9]+}', [FrontendController::class, 'render']);
    $group->get('/collections/{id:[0-9]+}', [FrontendController::class, 'render']);
    $group->get('/users', [FrontendController::class, 'render']);
    $group->get('/users/{id:[0-9]+}', [FrontendController::class, 'render']);

    // Download the contents of an image
    $group->get('/{imageName:[A-Za-z0-9-_]+}[.{format}]', [ImageController::class, 'downloadImage'])
      ->add(ImageResolverMiddleware::class)
      ->setName('images.download');
  });
};
