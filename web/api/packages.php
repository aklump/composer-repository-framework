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
 * - Requires bootstrap.php for application setup and configuration.
 * - Utilizes the Authenticate, Schedule, Router, and Error classes for handling API logic.
 *
 * Exception Handling:
 * - Any exceptions thrown during processing are caught, and an error response is generated with the exception's code and message.
 *
 * Output:
 * - Returns a JSON-encoded response with slashes unescaped to ensure proper formatting.
 */

namespace AKlump\Packages;

use AKlump\Packages\Config;
use Exception;
use AKlump\Packages\API\Router;
use AKlump\Packages\HTTP\Authenticate;
use AKlump\Packages\HTTP\Error;

require_once __DIR__ . '/../../bootstrap.php';

try {
  (new Authenticate($_ENV['API_SECRET']))($_GET);
  $scheduler = new Schedule(__DIR__ . '/../../' . Config::CACHE_DIR_BASENAME);
  $response = (new Router($scheduler))->handle(
    $_SERVER['REQUEST_METHOD'] ?? '',
    pathinfo(__FILE__, PATHINFO_FILENAME),
    file_get_contents('php://input')
  );
}
catch (Exception $exception) {
  $response = (new Error())($exception->getCode(), $exception->getMessage());
}
echo json_encode($response, JSON_UNESCAPED_SLASHES);
