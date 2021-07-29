<?php
namespace Danae\Faylin\Model\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;


// Trait that uses the route context to resolve URLs
trait RouteContextTrait
{
  // Resolve the specified endpoint to a relative URL
  public function urlFor(Request $request, string $name, array $params = [], array $queryParams = []): string
  {
    return $request->getAttribute('routeParser')->urlFor($name, $params, $queryParams);
  }

  // Resolve the specified endpoint to an absolute URL
  public function fullUrlFor(Request $request, string $name, array $params = [], array $queryParams = []): string
  {
    return $request->getAttribute('routeParser')->fullUrlFor($request->getUri(), $name, $params, $queryParams);
  }
}
