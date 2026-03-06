<?php
// SPDX-License-Identifier: BSD-3-Clause

namespace AKlump\Packages\HTTP;

use RuntimeException;

final class Authenticate {

  private string $secret;

  public function __construct(string $secret) {
    $this->secret = $secret;
  }

  public function __invoke(array $request_headers, string $request_body) {
    $request_headers = array_combine(array_map('strtoupper', array_keys($request_headers)), $request_headers);

    if (!isset($request_headers['X-HUB-SIGNATURE']) && !isset($request_headers['X-HUB-SIGNATURE-256'])) {
      throw new RuntimeException('Missing or empty X-HUB-SIGNATURE parameter', 400);
    }

    $signature_header = $request_headers['X-HUB-SIGNATURE-256'] ?? $request_headers['X-HUB-SIGNATURE'];
    if (empty(trim($signature_header))) {
      throw new RuntimeException('Missing or empty X-HUB-SIGNATURE parameter', 400);
    }

    if (isset($request_headers['X-HUB-SIGNATURE-256'])) {
      $algo = 'sha256';
      $prefix = 'sha256=';
    }
    else {
      $algo = 'sha1';
      $prefix = 'sha1=';
    }

    $signature = hash_hmac($algo, $request_body, $this->secret);
    if (!hash_equals($prefix . $signature, $signature_header)) {
      throw new RuntimeException('Invalid secret parameter', 403);
    }
  }
}
