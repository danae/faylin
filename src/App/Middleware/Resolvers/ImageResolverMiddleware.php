<?php
namespace Danae\Faylin\App\Middleware\Resolvers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

use Danae\Faylin\Model\ImageRepository;


// Middleware that resolves an image from the repository and adds it to the request as an attribute
final class ImageResolverMiddleware implements MiddlewareInterface
{
  // The image repository of this middleware
  private $imageRepository;


  // Constructor
  public function __construct(ImageRepository $imageRepository)
  {
    $this->imageRepository = $imageRepository;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Get the identifier from the route
    $routeContext = RouteContext::fromRequest($request);
    $route = $routeContext->getRoute();
    $id = $route->getArgument('id');

    // Get the image from the repository
    $image = $this->imageRepository->selectOne(['id' => $id]);
    if ($image == null)
      throw new HttpNotFoundException($request, "An image with id \"{$id}\" coud not be found");

    // Store the image as an attribute
    $request = $request->withAttribute('image', $image);

    // Handle the request
    return $handler->handle($request);
  }
}
