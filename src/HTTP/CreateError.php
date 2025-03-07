<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\HTTP;

use AKlump\AnnotatedResponse\AnnotatedResponse;
use AKlump\AnnotatedResponse\AnnotatedResponseInterface;

class CreateError {

  public function __invoke(int $code, string $reason): AnnotatedResponseInterface {
    http_response_code($code);
    $response = new AnnotatedResponse();
    $response->setHttpStatus($code)->setMessage($reason);

    return $response;
  }
}
