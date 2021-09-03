<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

use Danae\Faylin\Model\UserRepositoryInterface;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Middleware that resolves a user from the repository and adds it to the request as an attribute
final class UserResolverMiddleware implements MiddlewareInterface
{
  use RouteContextTrait;


  // The user repository to use with the middleware
  private $userRepository;


  // Constructor
  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Get the route
    $route = $this->getRoute($request);

    // Get the identifier from the route
    if (($id = $route->getArgument('userId')) !== null);
    {
      // Get the user from the repository
      $user = $this->userRepository->find($id);
      if ($user == null)
        throw new HttpNotFoundException($request, "A user with id \"{$id}\" coud not be found");

      // Store the link as an attribute
      $request = $request->withAttribute('user', $user);
    }

    // Get the name from the route
    if (($name = $route->getArgument('userName')) !== null);
    {
      // Get the user from the repository
      $user = $this->userRepository->findBy(['name' => $name]);
      if ($user == null)
        throw new HttpNotFoundException($request, "A user with name \"{$name}\" coud not be found");

      // Store the link as an attribute
      $request = $request->withAttribute('user', $user);
    }

    // No identifier or name is provided
    else
      throw new HttpBadRequestException($request, "No user id or name was provided to the route");

    // Handle the request
    return $handler->handle($request);
  }
}
