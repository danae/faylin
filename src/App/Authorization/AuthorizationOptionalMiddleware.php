<?php
namespace Danae\Faylin\App\Authorization;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

use Danae\Faylin\Model\User;


// Middleware that makes authorization optional
final class AuthorizationOptionalMiddleware implements MiddlewareInterface
{
  // The authorization middleware to use with the middleware
  private $middleware;


  // Constructor
  public function __construct(AuthorizationMiddleware $middleware)
  {
    $this->middleware = $middleware;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    try
    {
      // Handle the request through the middleware
      return $this->middleware->process($request, $handler);
    }
    catch (HttpUnauthorizedException $ex)
    {
      // Check if the request contains an authorization header
      if ($request->hasHeader('Authorization'))
      {
        // Rethrow the exception
        throw $ex;
      }
      else
      {
        // Handle the request without authorized user
        return $handler->handle($request);
      }
    }
  }
}
