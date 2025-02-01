<?php

namespace AKlump\PackagesITLS\Config;

final class GetRepositoryUrl {

  public function __invoke(): string {
    return parse_ini_file(__DIR__ . '/../../.env')['REPOSITORY_URL'] ?? '';
  }
}
