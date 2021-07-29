<?php
namespace Danae\Faylin\App\Bridge;

use Invoker\CallableResolver as InvokerCallableResolver;
use Psr\Container\ContainerInterface;
use Slim\CallableResolver as SlimCallableResolver;
use Slim\Interfaces\AdvancedCallableResolverInterface;


// Advanced callable resolver for PSR-15 middleware support
class CallableResolver implements AdvancedCallableResolverInterface
{
  // References to callable resolvers
  private $callableResolver;
  private $slimCallableResolver;


  // Constructor
  public function __construct(ContainerInterface $container)
  {
    $this->callableResolver = new InvokerCallableResolver($container);
    $this->slimCallableResolver = new SlimCallableResolver($container);
  }

  // Resolve the parameter into a callable
  public function resolve($toResolve): callable
  {
    return $this->callableResolver->resolve($toResolve);
  }

  // Resolve the parameter into a callable route
  public function resolveRoute($toResolve): callable
  {
    return $this->resolve($toResolve);
  }

  // Resolve the parameter into a callable middleware
  public function resolveMiddleware($toResolve): callable
  {
    return $this->slimCallableResolver->resolveMiddleware($toResolve);
  }
}
