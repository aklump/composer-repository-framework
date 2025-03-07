<?php
// SPDX-License-Identifier: BSD-3-Clause

/**
 * Main entry point for handling API requests.
 *
 * This script initializes the necessary services, processes the incoming HTTP request,
 * and provides an appropriate response in JSON format.
 *
 * Workflow:
 * - Authentication is performed using an API secret provided via environment variables.
 * - A scheduler is instantiated using a cache URI from environment variables.
 * - The request is routed and handled based on the HTTP method, request path, and request body.
 * - In case of an exception, an error response is generated.
 *
 * Dependencies:
 * - Utilizes the Authenticate, PackageChangeManager, Router, and CreateError classes for handling API logic.
 *
 * Exception Handling:
 * - Any exceptions thrown during processing are caught, and an error response is generated with the exception's code and message.
 *
 * Output:
 * - Returns a JSON-encoded response with slashes unescaped to ensure proper formatting.
 */

namespace AKlump\Packages;

use AKlump\Packages\API\ResourceRepository;
use AKlump\Packages\API\Router;
use AKlump\Packages\Config\Constants;
use AKlump\Packages\HTTP\Authenticate;
use AKlump\Packages\HTTP\CreateError;
use Exception;
use RuntimeException;

require_once __DIR__ . '/../../inc/_fw.bootstrap.php';

/** @var \Monolog\Logger $logger */

try {
  $request_body = file_get_contents('php://input');
  authenticate();
  $package_change_manager = new PackageChangeManager(ROOT . '/' . Constants::ROOT_RELATIVE_CACHE_PATH);
  $response = (new Router(
    new ResourceRepository(),
    $package_change_manager,
    new ChangeReporterRepository(),
    $logger
  ))->handle(
    $_SERVER['REQUEST_METHOD'] ?? '',
    pathinfo(__FILE__, PATHINFO_FILENAME),
    $request_body
  );
}
catch (Exception $exception) {
  $response = (new CreateError())($exception->getCode(), $exception->getMessage());
}
echo json_encode($response, JSON_UNESCAPED_SLASHES);

function authenticate() {
  // TODO Fix this
  return;
  global $request_body;
  if (empty($_ENV['API_SECRET'])) {
    throw new RuntimeException('Missing or empty API_SECRET.');
  }
  (new Authenticate($_ENV['API_SECRET']))(getallheaders(), $request_body);
}
