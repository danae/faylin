<?php
namespace Danae\Faylin\App\Middleware\Route;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Routing\RouteContext;


// Middleware that adds the route context as attributes on the request
final class RouteContextMiddleware implements MiddlewareInterface
{
  // Process the middleware
  public function process(Request $request, RequestHandler $handler): Response
  {
    // Create the route context
    $routeContext = RouteContext::fromRequest($request);

    // Add attributes to the request
    $request = $request
      ->withAttribute('route', $routeContext->getRoute())
      ->withAttribute('routeParser', $routeContext->getRouteParser())
      ->withAttribute('routingResults', $routeContext->getRoutingResults());

    // Handle the request
    return $handler->handle($request);
  }
}
