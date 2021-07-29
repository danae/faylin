<?php
namespace Danae\Faylin\App\Middleware\Authorization;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;


// Base class for authorization middleware
abstract class AuthorizationMiddleware implements MiddlewareInterface
{
  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Authorize using the request and store the attributes
    $attributes = $this->authorize($request);
    foreach ($attributes as $name => $value)
      $request = $request->withAttribute($name, $value);

    // Handle the request
    return $handler->handle($request);
  }

  // Authorize a user using the request and returns attributes to set on the request
  public abstract function authorize(Request $request): array;
}
