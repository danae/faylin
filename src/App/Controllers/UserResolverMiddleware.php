<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;

use Danae\Faylin\Model\UserRepository;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Middleware that resolves a user from the repository and adds it to the request as an attribute
final class UserResolverMiddleware implements MiddlewareInterface
{
  use RouteContextTrait;


  // The user repository to use with the middleware
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
    $id = $this->getRoute($request)->getArgument('userId');

    // Get the user from the repository
    $user = $this->userRepository->get($id);
    if ($user == null)
      throw new HttpNotFoundException($request, "A user with id \"{$id}\" coud not be found");

    // Store the link as an attribute
    $request = $request->withAttribute('user', $user);

    // Handle the request
    return $handler->handle($request);
  }
}
