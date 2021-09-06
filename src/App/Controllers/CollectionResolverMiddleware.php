<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

use Danae\Faylin\Model\CollectionRepositoryInterface;
use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Middleware that resolves a collection from the repository and adds it to the request as an attribute
final class CollectionResolverMiddleware implements MiddlewareInterface
{
  use RouteContextTrait;


  // The collection repository to use with the middleware
  private $collectionRepository;


  // Constructor
  public function __construct(CollectionRepositoryInterface $collectionRepository)
  {
    $this->collectionRepository = $collectionRepository;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Get the route
    $route = $this->getRoute($request);

    // Get the identifier from the route
    if (($id = $route->getArgument('collectionId')) !== null)
    {
      // Get the collection from the repository
      $collection = $this->collectionRepository->find(Snowflake::fromString($id));
      if ($collection == null)
        throw self::createIdNotFound($request, $id);

      // Store the collection as an attribute
      $request = $request->withAttribute('collection', $collection);
    }

    // Get the name from the route
    else if (($name = $route->getArgument('collectionName')) !== null)
    {
      // Get the collection from the repository
      $collection = $this->collectionRepository->findBy(['name' => $name]);
      if ($collection == null)
        throw self::createNameNotFound($request, $name);

      // Store the collection as an attribute
      $request = $request->withAttribute('collection', $collection);
    }

    // No identifier or name is provided
    else
      throw self::createBadRequest($request);

    // Handle the request
    return $handler->handle($request);
  }

  // Return a bad request exception for an invalid route
  public function createBadRequest(Request $request): HttpBadRequestException
  {
    new HttpBadRequestException($request, "No collection id or name was provided to the route");
  }

  // Return a not found exception for an identifier
  public function createIdNotFound(Request $request, string $id): HttpNotFoundException
  {
    return new HttpNotFoundException($request, "A collection with id \"{$id}\" could not be found");
  }

  // Return a not found exception for a name
  public function createNameNotFound(Request $request, string $name): HttpNotFoundException
  {
    return new HttpNotFoundException($request, "A collection with name \"{$name}\" could not be found");
  }
}
