<?php

namespace InTheLoftStudios\Packages;

use Exception;
use InTheLoftStudios\Packages\HTTP\Authenticate;
use InTheLoftStudios\Packages\HTTP\Error;
use InTheLoftStudios\Packages\HTTP\Resources\Packages;

require_once __DIR__ . '/../../bootstrap.php';

try {
  (new Authenticate($_ENV['API_SECRET']))($_GET);

  $response = (new Error())(405, 'Invalid request method.');

  $scheduler = new Schedule(__DIR__ . '/../../' . $_ENV['CACHE_URI']);
  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      $response = (new Packages($scheduler))->get();
      break;

    case 'POST':
      $raw_input = file_get_contents('php://input');
      $response = (new Packages($scheduler))->post($raw_input);
      break;

    case 'DELETE':
      $response = (new Packages($scheduler))->delete();
      break;
  }
}
catch (Exception $exception) {
  $response = (new Error())($exception->getCode(), $exception->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_SLASHES);
