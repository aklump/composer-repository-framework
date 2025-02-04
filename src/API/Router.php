<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\API;

use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use AKlump\Packages\API\Resource\PackagesResource;
use AKlump\Packages\ChangeReporterRepositoryInterface;
use AKlump\Packages\HTTP\CreateError;
use AKlump\Packages\PackageChangeManager;
use Exception;
use Psr\Log\LoggerInterface;

class Router {

  private PackageChangeManager $packageChangeManager;

  private LoggerInterface $logger;

  /**
   * @var \AKlump\Packages\ChangeReporterRepositoryInterface
   */
  private ChangeReporterRepositoryInterface $changeReporterRepository;

  /**
   * @var \AKlump\Packages\API\ResourceRepository
   */
  private ResourceRepository $resourceRepository;

  public function __construct(
    ResourceRepository $resource_repository,
    PackageChangeManager $package_change_manager,
    ChangeReporterRepositoryInterface $change_reporter_repository,
    LoggerInterface $logger
  ) {
    $this->resourceRepository = $resource_repository;
    $this->packageChangeManager = $package_change_manager;
    $this->changeReporterRepository = $change_reporter_repository;
    $this->logger = $logger;
  }

  public function handle(string $method, string $route, string $content = ''): AnnotatedResponseInterface {

    $controller = $this->resourceRepository->getResourceController($route, $this->packageChangeManager, $this->logger);
    if (empty($controller)) {
      return (new CreateError())(404, sprintf('Invalid route: %s', $route));
    }

    /** @var PackagesResource $controller */
    if (method_exists($controller, 'setChangeReporterRepository')) {
      $controller->setChangeReporterRepository($this->changeReporterRepository);
    }

    try {
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
      }
    }
    catch (Exception $e) {
      $this->logger->error($e->getMessage(), ['exception' => $e]);
      $response = (new CreateError())(500, 'Internal server error.');
    }

    if (empty($response)) {
      $response = (new CreateError())(405, sprintf('Invalid request method: %s', $method));
    }

    return $response;
  }

}
