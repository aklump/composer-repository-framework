<?php

/**
 * @file Endpoint for webhooks that trigger updates.
 */

namespace InTheLoftStudios\Packages;

use AKlump\AnnotatedResponse\AnnotatedResponse;
use InTheLoftStudios\Packages\Sender\Github;
use InTheLoftStudios\Packages\HTTP\Error;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

const CACHE_DIR = __DIR__ . '/.cache/';

if (!isset($_GET['secret']) || empty(trim($_GET['secret']))) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing or empty secret parameter.']);
  exit;
}

$secret = trim($_GET['secret']);
$expected_secret = $_ENV['EVENT_SECRET'];

if (hash_equals($expected_secret, $secret) === FALSE) {
  (new Error())(403, 'Invalid secret parameter: .');
}

$raw_input = file_get_contents('php://input');
if (!empty($raw_input)) {
  $data = json_decode($raw_input, TRUE); // Decode JSON input into an associative array
  if (json_last_error() === JSON_ERROR_NONE) {
    $repositories = [];

    $github = new Github();
    if ($github->shouldHandle($data)) {
      $repositories[] = $github->getRepositoryEntry($data);
    }

    if (empty($repositories)) {
      (new Error())(400, 'Unknown event sender.');
    }

    $is_scheduled = (new Schedule(CACHE_DIR))($repositories);
    if ($is_scheduled) {
      $response = new AnnotatedResponse();
      $response->setHttpStatus(202)
        ->setMessage('Repository queued for update.');
      echo json_encode($response, JSON_UNESCAPED_SLASHES);
      exit;
    }
    (new Error())(400, 'Empty repositories; nothing to update.');
  }
  else {
    (new Error())(400, 'Invalid JSON payload.');
  }
}
else {
  (new Error())(400, 'No input provided.');
}
