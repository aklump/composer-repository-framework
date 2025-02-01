<?php

namespace AKlump\PackagesITLS\Config;

final class GetAPISecret {

  public function __invoke(): string {
    $path = __DIR__ . '/../../distributor/.env';

    return parse_ini_file($path)['API_SECRET'] ?? '';
  }
}
