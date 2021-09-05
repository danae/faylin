<?php
namespace Danae\Faylin\App\Authorization;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

use Danae\Faylin\Model\User;


// Middleware that adds required authorization to a route
final class AuthorizationMiddleware implements MiddlewareInterface
{
  // The authorization strategies to use with the middleware
  private $strategies;


  // Constructor
  public function __construct(array $strategies)
  {
    $this->strategies = $strategies;
  }

  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    try
    {
      // Authorize the request and get the authorized user
      $attributes = $this->authorize($request);
      if ($attributes === null)
        throw new HttpUnauthorizedException($request, "The request contains no acceptable authorization header");

      // Set the attributes on the request
      foreach ($attributes as $name => $value)
        $request = $request->withAttribute($name, $value);

      // Handle the request with the user stored as an attribute
      return $handler->handle($request);
    }
    catch (AuthorizationException $ex)
    {
      throw new HttpUnauthorizedException($request, $ex->getMessage(), $ex);
    }
  }

  // Process the middleware where authorization is optional
  public function optional(Request $request, RequestHandler $handler): Response
  {
    try
    {
      // Handle the request through the middleware
      return $this->process($request, $handler);
    }
    catch (HttpUnauthorizedException $ex)
    {
      // Handle the request without authorized user
      return $handler->handle($request);
    }
  }

  // Authorize a request and return the authorized user
  public function authorize(Request $request): ?array
  {
    // Iterate over the strategies
    foreach ($this->strategies as $strategy)
    {
      // Check if this strategy is able to authorize the request
      if (!($strategy instanceof AuthorizationStrategyInterface))
        continue;
      if (!$strategy->canAuthorize($request))
        continue;

      // Authorize the request and get the authorized user
      return $strategy->authorize($request);
    }

    // No strategies found that could authorize the request
    return null;
  }
}
