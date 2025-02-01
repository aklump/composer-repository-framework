<?php

namespace InTheLoftStudios\Packages\HTTP;

use RuntimeException;

final class Authenticate {

  private string $secret;

  public function __construct(string $secret) {
    $this->secret = $secret;
  }

  public function __invoke(array $request_vars) {
    if (!isset($request_vars['secret']) || empty(trim($request_vars['secret']))) {
      throw new RuntimeException('Missing or empty secret parameter', 400);
    }

    if (hash_equals($this->secret, $request_vars['secret']) === FALSE) {
      throw new RuntimeException('Invalid secret parameter', 403);
    }
  }
}
