<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

use Danae\Faylin\Model\ImageRepositoryInterface;
use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Middleware that resolves an image from the repository and adds it to the request as an attribute
final class ImageResolverMiddleware implements MiddlewareInterface
{
  use RouteContextTrait;


  // The image repository to use with the middleware
  private $imageRepository;


  // Constructor
  public function __construct(ImageRepositoryInterface $imageRepository)
  {
    $this->imageRepository = $imageRepository;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Get the route
    $route = $this->getRoute($request);

    // Get the identifier from the route
    if (($id = $route->getArgument('imageId')) !== null)
    {
      // Get the image from the repository
      $image = $this->imageRepository->find(Snowflake::fromString($id));
      if ($image == null)
        throw new HttpNotFoundException($request, "An image with id \"{$id}\" coud not be found");

      // Store the image as an attribute
      $request = $request->withAttribute('image', $image);
    }

    // Get the name from the route
    else if (($name = $route->getArgument('imageName')) !== null)
    {
      // Get the image from the repository
      $image = $this->imageRepository->findBy(['name' => $name]);
      if ($image == null)
        throw new HttpNotFoundException($request, "An image with name \"{$name}\" coud not be found");

      // Store the image as an attribute
      $request = $request->withAttribute('image', $image);
    }

    // No identifier or name is provided
    else
      throw new HttpBadRequestException($request, "No image id or name was provided to the route");

    // Handle the request
    return $handler->handle($request);
  }
}
