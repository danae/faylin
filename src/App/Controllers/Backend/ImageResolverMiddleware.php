<?php
namespace Danae\Faylin\App\Controllers\Backend;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;

use Danae\Faylin\Model\ImageRepository;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Middleware that resolves an image from the repository and adds it to the request as an attribute
final class ImageResolverMiddleware implements MiddlewareInterface
{
  use RouteContextTrait;


  // The image repository to use with the middleware
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
    $id = $this->getRoute($request)->getArgument('id');

    // Get the image from the repository
    $image = $this->imageRepository->get($id);
    if ($image == null)
      throw new HttpNotFoundException($request, "An image with id \"{$id}\" coud not be found");

    // Store the image as an attribute
    $request = $request->withAttribute('image', $image);

    // Handle the request
    return $handler->handle($request);
  }
}
