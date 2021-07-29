<?php
namespace Danae\Faylin\App\Bridge;

use DI\Container;
use DI\Bridge\Slim\ControllerInvoker;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory as SlimAppFactory;
use Slim\Interfaces\CallableResolverInterface;


// Class that defines the app
class AppFactory
{
  // Create the app
  public static function create(ContainerInterface $container = null): App
  {
    $container->set(CallableResolverInterface::class, new CallableResolver($container));

    $app = SlimAppFactory::createFromContainer($container);
    $container->set(App::class, $app);

    $controllerInvoker = self::createControllerInvoker($container);
    $app->getRouteCollector()->setDefaultInvocationStrategy($controllerInvoker);

    return $app;
  }

  // Create the controller invoker
  private static function createControllerInvoker(ContainerInterface $container): ControllerInvoker
  {
    $resolvers = [
      // Inject parameters by name first
      new AssociativeArrayResolver(),
      // Then inject services by type-hints for those that weren't resolved
      new TypeHintContainerResolver($container),
      // Then fall back on parameters default values for optional route parameters
      new DefaultValueResolver(),
    ];

    $invoker = new Invoker(new ResolverChain($resolvers), $container);
    return new ControllerInvoker($invoker);
  }
}
