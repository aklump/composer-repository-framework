<?php

namespace AKlump\PackagesITLS\Config;

final class GetCacheURI {

  public function __invoke(): string {
    return parse_ini_file(__DIR__ . '/../../.env')['CACHE_URI'] ?? '';
  }
}
