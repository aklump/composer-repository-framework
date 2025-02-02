#!/usr/bin/env php
<?php
// SPDX-License-Identifier: BSD-3-Clause

/**
 * This script acts as an entry point for handling API routing and streaming input.
 *
 * It utilizes a Scheduler instance configured with a cache URI path and a Router instance
 * to process incoming HTTP requests and corresponding data. The script reads input from
 * the command line or standard input, processes the request via the router, and returns
 * the resulting response in JSON format.
 *
 * Dependencies:
 *  - The bootstrap.php file for application initialization.
 *  - A Scheduler instance that manages scheduling tasks with a cache URI.
 *  - A Router instance that handles API routes.
 *
 * Workflow:
 *  1. Configures the Scheduler with a cache URI.
 *  2. Sets standard input to non-blocking mode.
 *  3. Creates a Router instance with the Scheduler to handle API calls.
 *  4. Determines the HTTP method and route from command-line arguments.
 *  5. Reads additional data from standard input stream.
 *  6. Passes the HTTP method, route, and standard input to the router.
 *  7. Returns the processed response in JSON format with unescaped slashes.
 *
 * Input:
 * - $argv[1]: The HTTP method (e.g., GET, POST) passed as a command-line argument.
 * - $argv[2]: The API route passed as a command-line argument.
 * - Standard input: Data payload to be processed.
 *
 * Output:
 * - A JSON-encoded response from the Router, printed to standard output.
 *
 * @code
 * cli_server.php GET packages.php
 * cli_server.php DELETE packages.php
 * echo '{"key": "value", "content": "file content here"}' | cli_server.php POST packages.php
 * @endcode
 */

namespace AKlump\Packages;

use AKlump\Packages\API\Router;

require_once __DIR__ . '/bootstrap.php';

/** @var \Monolog\Logger $logger */

$scheduler = new Schedule(__DIR__ . '/' . Config::CACHE_DIR_BASENAME);
stream_set_blocking(STDIN, 0);
$response = (new Router($logger, $scheduler))->handle(
  strtoupper($argv[1] ?? ''),
  $argv[2] ?? '',
  fgets(STDIN)
);
echo json_encode($response, JSON_UNESCAPED_SLASHES);
