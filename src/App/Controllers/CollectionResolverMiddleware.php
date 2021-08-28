<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;

use Danae\Faylin\Model\CollectionRepository;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Middleware that resolves an collection from the repository and adds it to the request as an attribute
final class CollectionResolverMiddleware implements MiddlewareInterface
{
  use RouteContextTrait;


  // The collection repository to use with the middleware
  private $collectionRepository;


  // Constructor
  public function __construct(CollectionRepository $collectionRepository)
  {
    $this->collectionRepository = $collectionRepository;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Get the identifier from the route
    $id = $this->getRoute($request)->getArgument('collectionId');

    // Get the collection from the repository
    $collection = $this->collectionRepository->get($id);
    if ($collection == null)
      throw new HttpNotFoundException($request, "A collection with id \"{$id}\" coud not be found");

    // Store the collection as an attribute
    $request = $request->withAttribute('collection', $collection);

    // Handle the request
    return $handler->handle($request);
  }
}
