<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\API;

use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use Exception;
use AKlump\Packages\Schedule;
use AKlump\Packages\HTTP\Resources\Packages;
use AKlump\Packages\HTTP\Error;
use Monolog\Logger;
use RuntimeException;

class Router {

  private Schedule $scheduler;

  private Logger $logger;

  public function __construct(Logger $logger, Schedule $scheduler) {
    $this->logger = $logger;
    $this->scheduler = $scheduler;
  }

  public function handle(string $method, string $route, string $content): AnnotatedResponseInterface {
    try {
      $controllers = $this->getControllers();
      if (!isset($controllers[$route])) {
        throw new RuntimeException(sprintf('Invalid route: %s', $route), 404);
      }
      $controller = new $controllers[$route]($this->logger, $this->scheduler);
      switch ($method) {
        case 'GET':
          $response = $controller->get();
          break;

        case 'POST':
          $response = $controller->post($content);
          break;

        case 'DELETE':
          $response = $controller->delete();
          break;

        default:
          $response = (new Error())(405, 'Invalid request method.');
          break;
      }
    }
    catch (Exception $exception) {
      $response = (new Error())($exception->getCode(), $exception->getMessage());
    }

    return $response;
  }

  private function getControllers(): array {
    return [
      'packages' => Packages::class,
    ];
  }

}
