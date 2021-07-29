<?php
namespace Danae\Faylin\Utils\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\Route;
use Slim\Routing\RouteContext;
use Slim\Routing\RouteParser;


// Trait that uses the route context to resolve URLs
trait RouteContextTrait
{
  // Return the route from a request
  public function getRoute(Request $request): Route
  {
    $routeContext = RouteContext::fromRequest($request);
    return $routeContext->getRoute();
  }

  // Return the route parser from a request
  public function getRouteParser(Request $request): RouteParser
  {
    $routeContext = RouteContext::fromRequest($request);
    return $routeContext->getRouteParser();
  }

  // Resolve the specified endpoint to a relative URL
  public function urlFor(Request $request, string $name, array $params = [], array $queryParams = []): string
  {
    return $this->getRouteParser($request)->urlFor($name, $params, $queryParams);
  }

  // Resolve the specified endpoint to an absolute URL
  public function fullUrlFor(Request $request, string $name, array $params = [], array $queryParams = []): string
  {
    return $this->getRouteParser($request)->fullUrlFor($request->getUri(), $name, $params, $queryParams);
  }
}
