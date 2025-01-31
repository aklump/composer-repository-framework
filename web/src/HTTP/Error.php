<?php

namespace InTheLoftStudios\Packages\HTTP;

use AKlump\AnnotatedResponse\AnnotatedResponse;

class Error {

  public function __invoke(int $code, string $reason): void {
    http_response_code($code);
    $response = new AnnotatedResponse();
    $response->setHttpStatus($code)->setMessage($reason);
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
    exit;
  }
}
