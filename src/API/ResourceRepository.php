<?php

namespace AKlump\Packages\API;

use AKlump\Packages\API\Resource\PackagesResource;
use AKlump\Packages\API\Resource\ResourceInterface;

class ResourceRepository {


  /**
   * Retrieve a resource controller instance based on the given route.
   *
   * This method maps a provided route to a corresponding resource controller
   * class and instantiates it with the provided constructor arguments.
   *
   * @param string $route
   *   The route identifying the resource controller (e.g., 'packages').
   * @param mixed ...$constructor_args
   *   A variadic list of arguments to pass to the resource controller's
   *   constructor.
   *
   * @return ?\AKlump\Packages\API\Resource\ResourceInterface
   *   The instantiated resource controller instance.
   */
  public function getResourceController(string $route, ...$constructor_args): ?ResourceInterface {
    $class_name = ([
      'packages' => PackagesResource::class,
    ])[$route] ?? '';
    if (!class_exists($class_name)) {
      return NULL;
    }

    return new $class_name(...$constructor_args);
  }

}
