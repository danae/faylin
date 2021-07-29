<?php
namespace Danae\Faylin\App\Middleware\Resolvers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

use Danae\Faylin\Model\UserRepository;


// Middleware that resolves a user from the repository and adds it to the request as an attribute
final class UserResolverMiddleware implements MiddlewareInterface
{
  // The user repository of this middleware
  private $userRepository;


  // Constructor
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Get the identifier from the route
    $routeContext = RouteContext::fromRequest($request);
    $route = $routeContext->getRoute();
    $id = $route->getArgument('id');

    // Get the user from the repository
    $user = $this->userRepository->selectOne(['id' => $id]);
    if ($user == null)
      throw new HttpNotFoundException($request, "A user with id \"{$id}\" coud not be found");

    // Store the link as an attribute
    $request = $request->withAttribute('user', $user);

    // Handle the request
    return $handler->handle($request);
  }
}
