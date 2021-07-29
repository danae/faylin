<?php
namespace Danae\Faylin\App\Authorization;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;


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
      // Iterate over the strategies
      foreach ($this->strategies as $strategy)
      {
        // Check if this strategy is able to authorize the request
        if (!($strategy instanceof AuthorizationStrategyInterface))
          continue;
        if (!$strategy->canAuthorize($request))
          continue;

        // Authorize the request and get the authorized user
        $user = $strategy->authorize($request);

        // Handle the request with the user stored as an attribute
        return $handler->handle($request->withAttribute('authUser', $user));
      }
    }
    catch (AuthorizationException $ex)
    {
      throw new HttpUnauthorizedException($request, $ex->getMessage(), $ex);
    }

    // No strategies found that could authorize the request
    throw new HttpUnauthorizedException($request, "The request contains an invalid authorization header");
  }
}
